<?php

/**
 * A complete list of services provided by framework.
 */
return [
    \Jadob\Core\ServiceProvider\TopLevelServicesProvider::class,
    \Jadob\Core\ServiceProvider\GlobalVariablesProvider::class,
    \Jadob\Router\ServiceProvider\RouterServiceProvider::class,
    \Jadob\DoctrineDBALBridge\ServiceProvider\DoctrineDBALServiceProvider::class,
    \Jadob\Database\ServiceProvider\DatabaseServiceProvider::class,
//    \Jadob\Debug\ServiceProvider\DebugServiceProvider::class,
    \Jadob\Form\ServiceProvider\FormProvider::class,
    \Jadob\Security\ServiceProvider\SecurityProvider::class,
    \Jadob\Cache\ServiceProvider\CacheProvider::class,
    \Jadob\TwigBridge\ServiceProvider\TwigServiceProvider::class,
    \Jadob\SymfonyTranslationBridge\ServiceProvider\TranslationServiceProvider::class,
];