<?php

namespace Tests\GBProd\DoctrineSpecificationBundle;

use GBProd\DoctrineSpecificationBundle\DoctrineSpecificationBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use GBProd\DoctrineSpecificationBundle\DependencyInjection\Compiler\ExpressionBuilderPass;

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
        $container = $this->getMock(ContainerBuilder::class);
        $container
            ->expects($this->once())
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(ExpressionBuilderPass::class))
        ;
        
        $bundle = new DoctrineSpecificationBundle();
        $bundle->build($container);
    }
}
