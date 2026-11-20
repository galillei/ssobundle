<?php

/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

use SSO\FpBundle\Controller\SSO\Logout;
use SSO\FpBundle\Provider\FactoryPortalUserProvider;
use SSO\FpBundle\Repository\UserRepository;
use SSO\FpBundle\Security\FactoryPortalAuthenticator;
use SSO\FpBundle\Service\MergeUserData;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

/*
 * Services of the bundle. PHP config instead of XML: Symfony 8 can not load XML service files any more.
 * Same services as the former controller.xml and provider.xml.
 */
return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('SSO\\FpBundle\\Controller\\', '../Controller/*')
        ->public();

    $services->set(Logout::class)
        ->public()
        ->arg('$factoryPortalUrl', env('FACTORY_PORTAL_LOGOUT'));

    $services->set(MergeUserData::class)->public();

    $services->set(UserRepository::class)
        ->public()
        ->tag('doctrine.repository_service');

    $services->set(FactoryPortalAuthenticator::class)->public();

    $services->set(FactoryPortalUserProvider::class)->public();
};
