<?php

namespace GBProd\DoctrineSpecificationBundle;

use GBProd\DoctrineSpecificationBundle\DependencyInjection\Compiler\QueryFactoryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

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
        $container->addCompilerPass(new QueryFactoryPass());
    }
}
