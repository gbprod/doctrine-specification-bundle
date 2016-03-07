<?php

namespace Tests\GBProd\DoctrineSpecificationBundle\DependencyInjection\Compiler;

use GBProd\DoctrineSpecification\Handler;
use GBProd\DoctrineSpecificationBundle\DependencyInjection\Compiler\ExpressionBuilderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Tests for ExpressionBuilderPass
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class ExpressionBuilderPassTest extends \PHPUnit_Framework_TestCase
{
    public function testThrowExceptionIfNoHandlerDefinition()
    {
        $pass = new ExpressionBuilderPass();
        
        $this->setExpectedException('\Exception');
        
        $pass->process(new ContainerBuilder());
    }
    
    public function testDoNothingIfNoTaggedServices()
    {
        $pass = new ExpressionBuilderPass();
        $container = $this->createContainerWithHandler();
        
        $pass->process($container);
        
        $calls = $container
            ->getDefinition('gbprod.doctrine_specification_handler')
            ->getMethodCalls()
        ;
        
        $this->assertEmpty($calls);
    }
    
    private function createContainerWithHandler()
    {
        $container = new ContainerBuilder();
        
        $container->setDefinition(
            'gbprod.doctrine_specification_handler',
            new Definition(Handler::class)
        );
        
        return $container;
    }
    
    public function testThrowExceptionIfTagHasNoSpecification()
    {
        $pass = new ExpressionBuilderPass();
        
        $container = $this->createContainerWithHandler();
        $container
            ->register('builder', '\stdClass')
            ->addTag('doctrine.expression_builder')
        ;
        
        $this->setExpectedException('\Exception');
        $pass->process($container);
    }
    
    public function testAddMethodCalls()
    {
        $pass = new ExpressionBuilderPass();
        
        $container = $this->createContainerWithHandler();
        $container
            ->register('builder1', 'Builder1')
            ->addTag('doctrine.expression_builder', ['specification' => 'Specification1'])
        ;

        $container
            ->register('builder2', 'Builder2')
            ->addTag('doctrine.expression_builder', ['specification' => 'Specification2'])
        ;
        
        $pass->process($container);
    
        $calls = $container
            ->getDefinition('gbprod.doctrine_specification_handler')
            ->getMethodCalls()
        ;
        
        $this->assertCount(2, $calls);
        
        $this->assertEquals('registerBuilder', $calls[0][0]);
        $this->assertEquals('Specification1', $calls[0][1][0]);
        $this->assertInstanceOf(Reference::class, $calls[0][1][1]);
        $this->assertEquals('builder1', $calls[0][1][1]);
        
        
        $this->assertEquals('registerBuilder', $calls[1][0]);
        $this->assertEquals('Specification2', $calls[1][1][0]);
        $this->assertInstanceOf(Reference::class, $calls[1][1][1]);
        $this->assertEquals('builder2', $calls[1][1][1]);
    }
}