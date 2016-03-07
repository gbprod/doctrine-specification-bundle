<?php

namespace GBProd\DoctrineSpecificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Register doctrine expression builder
 * 
 * @author gbprod 
 */
class ExpressionBuilderPass implements CompilerPassInterface
{
    /**
     * {inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('gbprod.doctrine_specification_handler')) {
            throw new \Exception('Missing gbprod.doctrine_specification_handler definition');
        }

        $handler = $container
            ->findDefinition('gbprod.doctrine_specification_handler')
        ;
        
        $builders = $container->findTaggedServiceIds('doctrine.expression_builder');
        
        foreach ($builders as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!isset($attributes['specification'])) {
                    throw new \Exception('The doctrine.expression_builder tag must always have a "specification" attribute');
                }
                
                $handler->addMethodCall(
                    'registerBuilder',
                    [$attributes['specification'], new Reference($id)]
                );
            }
        }
    }
}