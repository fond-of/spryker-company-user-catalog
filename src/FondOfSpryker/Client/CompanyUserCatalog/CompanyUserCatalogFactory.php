<?php

namespace FondOfSpryker\Client\CompanyUserCatalog;

use FondOfSpryker\Client\CompanyUserCatalog\Dependency\Client\CompanyUserCatalogToCustomerClientInterface;
use Spryker\Client\Kernel\AbstractFactory;

class CompanyUserCatalogFactory extends AbstractFactory
{
    /**
     * @return \FondOfSpryker\Client\CompanyUserCatalog\Dependency\Client\CompanyUserCatalogToCustomerClientInterface
     */
    public function getCustomerClient(): CompanyUserCatalogToCustomerClientInterface
    {
        return $this->getProvidedDependency(CompanyUserCatalogDependencyProvider::CLIENT_CUSTOMER);
    }
}
