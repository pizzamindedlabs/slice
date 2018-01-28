<?php

/**
 * A complete list of services provided by framework.
 */
return [
    \Slice\Core\ServiceProvider\TopLevelServicesProvider::class,
    \Slice\Core\ServiceProvider\GlobalVariablesProvider::class,
    \Slice\Router\ServiceProvider\RouterServiceProvider::class,
    \Slice\DoctrineDBALBridge\ServiceProvider\DoctrineDBALServiceProvider::class,
    \Slice\Database\ServiceProvider\DatabaseServiceProvider::class,
//    \Slice\Debug\ServiceProvider\DebugServiceProvider::class,
    \Slice\Form\ServiceProvider\FormProvider::class,
    \Slice\Security\ServiceProvider\SecurityProvider::class,
    \Slice\Cache\ServiceProvider\CacheProvider::class,
    \Slice\TwigBridge\ServiceProvider\TwigServiceProvider::class,
    \Slice\SymfonyTranslationBridge\ServiceProvider\TranslationServiceProvider::class,
];