<?php
/**
 * 2007-2019 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace Finvero\Repository;

use Doctrine\DBAL\Connection;
use PDO;

class FinveroRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(Connection $connection, $dbPrefix)
    {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * Finds product id if such exists.
     *
     * @param int $productId
     *
     * @return int
     */
    public function findIdByProduct($productId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->select('`id_finvero_product`')
            ->from($this->dbPrefix . 'finvero_products')
            ->where('`id_product` = :product_id')
        ;

        $queryBuilder->setParameter('product_id', $product_id);

        return (int) $queryBuilder->execute()->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * Gets finvero product status by product.
     *
     * @param int $productId
     *
     * @return bool
     */
    public function getIsFinveroProductStatus($productId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->select('`is_finvero_product`')
            ->from($this->dbPrefix . 'finvero_products')
            ->where('`id_product` = :product_id')
        ;

        $queryBuilder->setParameter('product_id', $productId);

        return (bool) $queryBuilder->execute()->fetch(PDO::FETCH_COLUMN);
    }
}
