<?php

namespace Tests\GBProd\DoctrineSpecificationBundle;

use GBProd\DoctrineSpecificationBundle\DoctrineSpecificationBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use GBProd\DoctrineSpecificationBundle\DependencyInjection\Compiler\QueryFactoryPass;

/**
 * Tests for Bundle
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class DoctrineSpecificationBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        new DoctrineSpecificationBundle();
    }

    public function testBuildAddCompilerPass()
    {
        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects($this->once())
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(QueryFactoryPass::class))
        ;

        $bundle = new DoctrineSpecificationBundle();
        $bundle->build($container);
    }
}
