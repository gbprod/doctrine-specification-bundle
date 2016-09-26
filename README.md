# Doctrine specification bundle

This bundle integrates [doctrine-specification](git@github.com:gbprod/doctrine-specification.git) with Symfony.

[![Build Status](https://travis-ci.org/gbprod/doctrine-specification-bundle.svg?branch=master)](https://travis-ci.org/gbprod/doctrine-specification-bundle) 
[![codecov](https://codecov.io/gh/gbprod/doctrine-specification-bundle/branch/master/graph/badge.svg)](https://codecov.io/gh/gbprod/doctrine-specification-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gbprod/doctrine-specification-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gbprod/doctrine-specification-bundle/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/574a9c9ace8d0e004130d337/badge.svg)](https://www.versioneye.com/user/projects/574a9c9ace8d0e004130d337)

[![Latest Stable Version](https://poser.pugx.org/gbprod/doctrine-specification-bundle/v/stable)](https://packagist.org/packages/gbprod/doctrine-specification-bundle)
[![Total Downloads](https://poser.pugx.org/gbprod/doctrine-specification-bundle/downloads)](https://packagist.org/packages/gbprod/doctrine-specification-bundle)
[![Latest Unstable Version](https://poser.pugx.org/gbprod/doctrine-specification-bundle/v/unstable)](https://packagist.org/packages/gbprod/doctrine-specification-bundle)
[![License](https://poser.pugx.org/gbprod/doctrine-specification-bundle/license)](https://packagist.org/packages/gbprod/doctrine-specification-bundle)

## Installation

Download bundle using [composer](https://getcomposer.org/) :

```bash
composer require gbprod/doctrine-specification-bundle
```

Declare in your `app/AppKernel.php` file:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new GBProd\DoctrineSpecificationBundle\DoctrineSpecificationBundle(),
        // ...
    );
}
```

## Create your specification and your expression builder

Take a look to [Specification](https://github.com/gbprod/specification) and [Doctrine Specification](https://github.com/gbprod/specification) libraries for more informations.

### Create a specification

```php
<?php

namespace GBProd\Acme\CoreDomain\Specification\Product;

use GBProd\Specification\Specification;

class IsAvailable implements Specification
{
    public function isSatisfiedBy($candidate)
    {
        return $candidate->isSellable() 
            && $candidate->expirationDate() > new \DateTime('now')
        ;
    }
}
```

### Create a query factory

```php
<?php

namespace GBProd\Acme\Infrastructure\Doctrine\QueryFactory\Product;

use GBProd\DoctrineSpecification\QueryFactory\Factory;
use GBProd\Specification\Specification;
use Doctrine\ORM\QueryBuilder;

class IsAvailableFactory implements Factory
{
    public function create(Specification $spec, QueryBuilder $qb)
    {
        return $qb->expr()
            ->andx(
                $qb->expr()->eq('sellable', "1"),
                $qb->expr()->gt('expirationDate', (new \DateTime('now'))->format('c'))
            )
        ;
    }
}
```

## Configuration

### Declare your factory

```yaml
// src/GBProd/Acme/AcmeBundle/Resource/config/service.yml

services:
    acme.doctrine.query_factory.is_available:
        class: GBProd\Acme\Infrastructure\Doctrine\QueryFactory\Product\IsAvailableFactory
        tags:
            - { name: doctrine.query_factory, specification: GBProd\Acme\CoreDomain\Specification\Product\IsAvailable }
```

### Inject handler in your repository class

```yaml
// src/GBProd/Acme/AcmeBundle/Resource/config/service.yml

services:
    acme.product_repository:
        class: GBProd\Acme\Infrastructure\Product\DoctrineProductRepository
        arguments:
            - "@doctrine.orm.entity_manager"
            - "@gbprod.doctrine_specification_handler"
```

```php
<?php

namespace GBProd\Acme\Infrastructure\Product;

use Doctrine\Common\Persistence\ObjectManager;
use GBProd\DoctrineSpecification\Handler;
use GBProd\Specification\Specification;

class DoctrineProductRepository implements ProductRepository
{
    private $em;

    private $handler;

    public function __construct(ObjectManager $em, Handler $handler)
    {
        $this->em      = $em;
        $this->handler = $handler;
    }

    public function findSatisfying(Specification $specification)
    {
        $qb = $this
            ->em
            ->getRepository('GBProd\Acme\CoreDomain\Product\Product')
            ->createQueryBuilder('p')
        ;
        
        return $this->handler->handle($specification, $qb);
    }
}
```

### Usage

```php
<?php

$products = $productRepository->findSatisfying(
    new AndX(
        new IsAvailable(),
        new IsLowStock()
    )
);
```
