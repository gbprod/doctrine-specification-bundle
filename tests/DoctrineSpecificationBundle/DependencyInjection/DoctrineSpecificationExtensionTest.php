<?php

namespace Tests\GBProd\DoctrineSpecificationBundle\DependencyInjection;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use GBProd\DoctrineSpecificationBundle\DependencyInjection\DoctrineSpecificationExtension;
use GBProd\DoctrineSpecification\Handler;
use GBProd\DoctrineSpecification\Registry;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Tests for DoctrineSpecificationExtension
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class DoctrineSpecificationExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $extension;
    private $container;

    protected function setUp()
    {
        $this->extension = new DoctrineSpecificationExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);

        $em = $this->prophesize(EntityManager::class);
        $em
            ->createQueryBuilder()
            ->willReturn(
                $this->prophesize(QueryBuilder::class)->reveal()
            )
        ;

        $this->container->set('doctrine.orm.entity_manager', $em->reveal());

        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();
    }

    public function testLoadHasServices()
    {
        $this->assertTrue(
            $this->container->has('gbprod.doctrine_specification_registry')
        );

        $this->assertTrue(
            $this->container->has('gbprod.doctrine_specification_handler')
        );
    }

    public function testLoadRegistry()
    {
        $registry = $this->container->get('gbprod.doctrine_specification_registry');

        $this->assertInstanceOf(Registry::class, $registry);
    }

    public function testLoadHandler()
    {
        $handler = $this->container->get('gbprod.doctrine_specification_handler');

        $this->assertInstanceOf(Handler::class, $handler);
    }
}
