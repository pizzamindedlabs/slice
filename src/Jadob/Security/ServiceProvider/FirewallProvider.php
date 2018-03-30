<?php

namespace Jadob\Security\ServiceProvider;

use Jadob\Container\Container;
use Jadob\Container\ServiceProvider\ServiceProviderInterface;
use Jadob\Security\Firewall\EventListener\FirewallListener;

class FirewallProvider implements ServiceProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfigNode()
    {
        return 'security';
    }

    /**
     * @param Container $container
     * @param array|null $config
     */
    public function register(Container $container, $config)
    {
        $container->get('event.listener')->addListener(
            new FirewallListener(
                $container->get('request'),
                $container->get('auth.user.storage'),
                $config,
                $container->get('router')
            ),
            2
        );
    }
}