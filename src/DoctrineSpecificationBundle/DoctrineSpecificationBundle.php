<?php

namespace GBProd\DoctrineSpecificationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use GBProd\DoctrineSpecificationBundle\DependencyInjection\Compiler\ExpressionBuilderPass;

/**
 * Bundle
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class DoctrineSpecificationBundle extends Bundle
{
    /**
     * {inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ExpressionBuilderPass());
    }
}