<?php

namespace FondOfSpryker\Client\CompanyUserCatalog\Dependency\Client;

use Generated\Shared\Transfer\CustomerTransfer;

interface CompanyUserCatalogToCustomerClientInterface
{
    /**
     * @return \Generated\Shared\Transfer\CustomerTransfer|null
     */
    public function getCustomer(): ?CustomerTransfer;
}
