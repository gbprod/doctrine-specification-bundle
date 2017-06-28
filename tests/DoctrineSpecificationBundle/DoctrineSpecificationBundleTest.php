<?php

namespace Tests\GBProd\DoctrineSpecificationBundle;

use GBProd\DoctrineSpecificationBundle\DependencyInjection\Compiler\QueryFactoryPass;
use GBProd\DoctrineSpecificationBundle\DoctrineSpecificationBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Tests for Bundle
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class DoctrineSpecificationBundleTest extends TestCase
{
    public function testConstruct()
    {
        $bundle = new DoctrineSpecificationBundle();

        $this->assertInstanceOf(Bundle::class, $bundle);
        $this->assertInstanceOf(DoctrineSpecificationBundle::class, $bundle);
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
