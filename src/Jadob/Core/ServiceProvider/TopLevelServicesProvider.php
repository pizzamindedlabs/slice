<?php

namespace Jadob\Core\ServiceProvider;

use Jadob\Container\Container;
use Jadob\Container\ContainerBuilder;
use Jadob\Container\ServiceProvider\ServiceProviderInterface;
use Jadob\Core\ControllerUtils;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class TopLevelServicesProvider
 * @package Jadob\Core\ServiceProvider
 * @author pizzaminded <miki@appvende.net>
 * @license MIT
 */
class TopLevelServicesProvider implements ServiceProviderInterface
{

    /**
     * @return mixed|null
     */
    public function getConfigNode()
    {
        return null;
    }

    /**
     * @param ContainerBuilder $container
     * @param $config
     * @return mixed|void
     */
    public function register(ContainerBuilder $container, $config)
    {
        $container->add('session', function () {
            return new Session();
        });

        $container->add('controller.utils', function (Container $container) {
            return new ControllerUtils($container);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function onContainerBuild(Container $container, $config)
    {
        return null;
    }
}