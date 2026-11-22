<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2025
 */
declare(strict_types=1);

namespace  SSO\FpBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;

class SSOFpExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container): void
    {
        // PHP config: Symfony 8 removed the XML loader for services
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.php');
    }


}