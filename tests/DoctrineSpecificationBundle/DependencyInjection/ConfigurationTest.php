<?php

namespace Tests\GBProd\DoctrineSpecificationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use GBProd\DoctrineSpecificationBundle\DependencyInjection\Configuration;

/**
 * Tests for Configuration
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConfigTreeBuilder()
    {
        $configuration = new Configuration();
        
        $tree = $configuration->getConfigTreeBuilder();
        
        $this->assertInstanceOf(
            TreeBuilder::class,
            $tree
        );
        
        $this->assertEquals(
            'doctrine_specification_bundle',
            $tree->buildTree()->getName()
        );
    }
}