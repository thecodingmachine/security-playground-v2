<?php

declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getProjectDir() . '/config/bundles.php'));
        $container->setParameter('container.dumper.inline_class_loader', true);
        $container->setParameter('container.dumper.inline_factories', true);

        $loader->load($this->getProjectDir() . '/config/packages/*.yaml', 'glob');
        $loader->load($this->getProjectDir() . '/config/packages/' . $this->environment . '/*.yaml', 'glob');
        $loader->load($this->getProjectDir() . '/config/services.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import($this->getProjectDir() . '/config/routes/*.yaml');
        $routes->import($this->getProjectDir() . '/config/routes/' . $this->environment . '/*.yaml');
        $routes->import($this->getProjectDir() . '/src/Controller/', 'attribute');
    }
}
