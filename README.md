# Doctrine specification bundle

This bundle integrates [doctrine-specification](git@github.com:gbprod/doctrine-specification.git) with Symfony.

[![Build Status](https://travis-ci.org/gbprod/doctrine-specification-bundle.svg?branch=master)](https://travis-ci.org/gbprod/doctrine-specification-bundle) [![Build Status](https://travis-ci.org/gbprod/doctrine-specification-bundle.svg?branch=master)](https://travis-ci.org/gbprod/doctrine-specification-bundle) [![Code Climate](https://codeclimate.com/github/gbprod/doctrine-specification-bundle/badges/gpa.svg)](https://codeclimate.com/github/gbprod/doctrine-specification-bundle)

[![Latest Stable Version](https://poser.pugx.org/gbprod/doctrine-specification-bundle/v/stable)](https://packagist.org/packages/gbprod/doctrine-specification) [![Total Downloads](https://poser.pugx.org/gbprod/doctrine-specification-bundle/downloads)](https://packagist.org/packages/gbprod/doctrine-specification) [![Latest Unstable Version](https://poser.pugx.org/gbprod/doctrine-specification-bundle/v/unstable)](https://packagist.org/packages/gbprod/doctrine-specification) [![License](https://poser.pugx.org/gbprod/doctrine-specification-bundle/license)](https://packagist.org/packages/gbprod/doctrine-specification)

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

### Create an expression builder

```php
<?php

namespace GBProd\Acme\Infrastructure\Doctrine\ExpressionBuilder\Product;

use GBProd\DoctrineSpecification\ExpressionBuilder\Builder;
use GBProd\Specification\Specification;
use Doctrine\ORM\QueryBuilder;

class IsAvailableBuilder implements Builder
{
    public function build(Specification $spec, QueryBuilder $qb)
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

### Declare your Builder

```yaml
// src/GBProd/Acme/AcmeBundle/Resource/config/service.yml

services:
    acme.doctrine.expression_builder.is_available:
        class: GBProd\Acme\Infrastructure\Doctrine\ExpressionBuilder\Product\IsAvailableBuilder
        tags:
            - { name: doctrine.expression_builder, specification: GBProd\Acme\CoreDomain\Specification\Product\IsAvailable }
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