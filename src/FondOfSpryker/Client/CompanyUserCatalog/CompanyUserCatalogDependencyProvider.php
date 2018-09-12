<?php

namespace FondOfSpryker\Client\CompanyUserCatalog;

use FondOfSpryker\Client\CompanyUserCatalog\Dependency\Client\CompanyUserCatalogToCustomerClientBridge;
use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;

class CompanyUserCatalogDependencyProvider extends AbstractDependencyProvider
{
    public const CLIENT_CUSTOMER = 'CLIENT_CUSTOMER';

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    public function provideServiceLayerDependencies(Container $container): Container
    {
        $container = parent::provideServiceLayerDependencies($container);

        $container = $this->addCustomerClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addCustomerClient(Container $container): Container
    {
        $container[static::CLIENT_CUSTOMER] = function (Container $container) {
            return new CompanyUserCatalogToCustomerClientBridge($container->getLocator()->customer()->client());
        };
        return $container;
    }
}
