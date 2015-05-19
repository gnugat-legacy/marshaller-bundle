<?php

/*
 * This file is part of the gnugat/marshaller-bundle package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gnugat\MarshallerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class MarshallerStrategyCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('gnugat_marshaller.marshaller')) {
            return;
        }
        $definition = $container->getDefinition('gnugat_marshaller.marshaller');
        $taggedServices = $container->findTaggedServiceIds('gnugat_marshaller');
        foreach ($taggedServices as $id => $attributes) {
            $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;
            $definition->addMethodCall('add', array(new Reference($id), $priority));
        }
    }
}
