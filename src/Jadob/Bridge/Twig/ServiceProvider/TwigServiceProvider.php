<?php

namespace Jadob\Bridge\Twig\ServiceProvider;

use Jadob\Bridge\Twig\Extension\WebpackManifestAssetExtension;
use Jadob\Container\Container;
use Jadob\Container\ServiceProvider\ServiceProviderInterface;
use Jadob\Core\BootstrapInterface;
use Jadob\TwigBridge\Twig\Extension\PathExtension;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

/**
 * @author pizzaminded <miki@appvende.net>
 * @license MIT
 */
class TwigServiceProvider implements ServiceProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfigNode()
    {
        return 'twig';
    }

    /**
     * {@inheritdoc}
     * @throws \Twig\Error\LoaderError
     */
    public function register($config)
    {
        $loaderClosure = function (ContainerInterface $container) use ($config) {
            $loader = new FilesystemLoader();
            $loader->addPath(__DIR__ . '/../templates', 'Jadob');

            foreach ($config['templates_paths'] as $key => $path) {
                $loader->addPath($container->get(BootstrapInterface::class)->getRootDir() . '/' . $path, $key);
            }

            return $loader;
        };

        $environmentClosure = function (ContainerInterface $container) use ($config) {
            $cache = false;
            if ($config['cache']) {
                $cache = $container->get(BootstrapInterface::class)->getCacheDir() . '/twig';
            }

            $options = [
                'cache' => $cache,
                'strict_variables' => $config['strict_variables']
            ];

            $environment = new Environment(
                $container->get(LoaderInterface::class),
                $options
            );

            if (isset($config['globals']) && \is_array($config['globals'])) {
                foreach ($config['globals'] as $globalKey => $globalValue) {
                    $environment->addGlobal($globalKey, $globalValue);
                }
            }

            return $environment;
        };

        return [
            LoaderInterface::class => $loaderClosure,
            Environment::class => $environmentClosure
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function onContainerBuild(Container $container, $config)
    {
        $container->alias(Environment::class, 'twig');

        /** @var Environment $twig */
        $twig = $container->get(Environment::class);

        //@TODO: fix referencing to router
        if($container->has('router')) {
            $twig->addExtension(new PathExtension($container->get('router')));
        }

        //register additional extensions provided with framework
        if(!isset($config['extensions'])) {
            return;
        }

        $extensions = $config['extensions'];

        if(isset($extensions['webpack_manifest'])) {

            $webpackManifestConfig = $extensions['webpack_manifest'];

            $manifestJsonLocation = $container->get(BootstrapInterface::class)->getRootDir()
                .'/'.
                $webpackManifestConfig['manifest_json_location'];

            $manifest = \json_decode(\file_get_contents($manifestJsonLocation),true);
            $twig->addExtension(new WebpackManifestAssetExtension($manifest));
        }
    }
}