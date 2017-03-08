<?php

namespace AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AddDbalCacheConfigurationPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $id = 'doctrine.dbal.default_connection.configuration';

        if ($container->hasDefinition($id)) {
            $idCache = 'app.cache.doctrine';

            if (!$container->hasDefinition($idCache)) {
                $container->setDefinition($idCache, new Definition('Symfony\Component\Cache\DoctrineProvider', [new Reference('cache.doctrine')]));
            }

            $container
                ->getDefinition($id)
                ->addMethodCall('setResultCacheImpl', [new Reference($idCache)]);
        }
    }
}
