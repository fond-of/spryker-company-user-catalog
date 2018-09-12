<?php

namespace FondOfSpryker\Client\CompanyUserCatalog\Plugin\Search;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Terms;
use Generated\Shared\Search\PageIndexMap;
use Generated\Shared\Transfer\CompanyUserProductListCollectionTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\CustomerProductListCollectionTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use InvalidArgumentException;
use Spryker\Client\Kernel\AbstractPlugin;
use Spryker\Client\Search\Dependency\Plugin\QueryExpanderPluginInterface;
use Spryker\Client\Search\Dependency\Plugin\QueryInterface;

/**
 * @method \FondOfSpryker\Client\CompanyUserCatalog\CompanyUserCatalogFactory getFactory()
 */
class ProductListQueryExpanderPlugin extends AbstractPlugin implements QueryExpanderPluginInterface
{
    /**
     * @api
     *
     * @param \Spryker\Client\Search\Dependency\Plugin\QueryInterface $searchQuery
     * @param array $requestParameters
     *
     * @return \Spryker\Client\Search\Dependency\Plugin\QueryInterface
     */
    public function expandQuery(QueryInterface $searchQuery, array $requestParameters = []): QueryInterface
    {
        $query = $searchQuery->getSearchQuery();

        $this->expandQueryWithBlacklistFilter($query);
        $this->expandQueryWithWhitelistFilter($query);

        return $searchQuery;
    }

    /**
     * @param \Elastica\Query $query
     *
     * @return void
     */
    protected function expandQueryWithWhitelistFilter(Query $query): void
    {
        $whitelistIds = $this->getWhitelistIds();

        if (count($whitelistIds)) {
            $boolQuery = $this->getBoolQuery($query);
            $boolQuery->addFilter($this->createWhitelistTermQuery($whitelistIds));
        }
    }

    /**
     * @return int[]
     */
    protected function getWhitelistIds(): array
    {
        $whitelistIds = [];
        $customerProductListCollectionTransfer = $this->findCustomerProductListCollection();
        $companyUserProductListCollectionTransfer = $this->findCompanyUserProductListCollection();

        if ($customerProductListCollectionTransfer !== null) {
            $whitelistIds = $customerProductListCollectionTransfer->getWhitelistIds();
        }

        if (empty($whitelistIds) && $companyUserProductListCollectionTransfer !== null) {
            $whitelistIds = $customerProductListCollectionTransfer->getWhitelistIds();
        }

        return $whitelistIds;
    }

    /**
     * @param array $whitelistIds
     *
     * @return \Elastica\Query\Terms
     */
    protected function createWhitelistTermQuery(array $whitelistIds): Terms
    {
        return new Terms(PageIndexMap::PRODUCT_LISTS_WHITELISTS, $whitelistIds);
    }

    /**
     * @param \Elastica\Query $query
     *
     * @return void
     */
    protected function expandQueryWithBlacklistFilter(Query $query): void
    {
        $blacklistIds = $this->getBlacklistIds();

        if (count($blacklistIds)) {
            $boolQuery = $this->getBoolQuery($query);
            $boolQuery->addMustNot($this->createBlacklistTermQuery($blacklistIds));
        }
    }

    /**
     * @return int[]
     */
    protected function getBlacklistIds(): array
    {
        $blacklistIds = [];
        $customerProductListCollectionTransfer = $this->findCustomerProductListCollection();
        $companyUserProductListCollectionTransfer = $this->findCompanyUserProductListCollection();

        if ($customerProductListCollectionTransfer !== null) {
            $blacklistIds = $customerProductListCollectionTransfer->getBlacklistIds();
        }

        if (empty($blacklistIds) && $companyUserProductListCollectionTransfer !== null) {
            $blacklistIds = $customerProductListCollectionTransfer->getBlacklistIds();
        }

        return $blacklistIds;
    }

    /**
     * @param array $blacklistIds
     *
     * @return \Elastica\Query\Terms
     */
    protected function createBlacklistTermQuery(array $blacklistIds): Terms
    {
        return new Terms(PageIndexMap::PRODUCT_LISTS_BLACKLISTS, $blacklistIds);
    }

    /**
     * @return \Generated\Shared\Transfer\CustomerProductListCollectionTransfer|null
     */
    protected function findCompanyUserProductListCollection(): ?CompanyUserProductListCollectionTransfer
    {
        $companyUser = $this->getCompanyUser();

        if ($companyUser === null) {
            return null;
        }

        $companyUserProductListCollection = $companyUser->getProductListCollection();

        if ($companyUserProductListCollection === null) {
            return null;
        }

        return $companyUserProductListCollection;
    }

    /**
     * @return \Generated\Shared\Transfer\CompanyUserTransfer|null
     */
    protected function getCompanyUser(): ?CompanyUserTransfer
    {
        $customer = $this->getCustomer();

        if ($customer === null) {
            return null;
        }

        return $customer->getCompanyUserTransfer();
    }

    /**
     * @return \Generated\Shared\Transfer\CustomerProductListCollectionTransfer|null
     */
    protected function findCustomerProductListCollection(): ?CustomerProductListCollectionTransfer
    {
        $customer = $this->getCustomer();

        if ($customer === null) {
            return null;
        }

        $customerProductListCollection = $customer->getProductListCollection();

        if ($customerProductListCollection === null) {
            return null;
        }

        return $customerProductListCollection;
    }

    /**
     * @return \Generated\Shared\Transfer\CustomerTransfer|null
     */
    protected function getCustomer(): ?CustomerTransfer
    {
        return $this->getFactory()
            ->getCustomerClient()
            ->getCustomer();
    }

    /**
     * @param \Elastica\Query $query
     *
     * @throws \InvalidArgumentException
     *
     * @return \Elastica\Query\BoolQuery
     */
    protected function getBoolQuery(Query $query): BoolQuery
    {
        $boolQuery = $query->getQuery();
        if (!$boolQuery instanceof BoolQuery) {
            throw new InvalidArgumentException(sprintf(
                'Product List Query Expander available only with %s, got: %s',
                BoolQuery::class,
                \get_class($boolQuery)
            ));
        }
        return $boolQuery;
    }
}
