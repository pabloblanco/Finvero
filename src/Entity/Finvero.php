<?php
/**
 * 2007-2019 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace Finvero\Entity;

use PrestaShop\PrestaShop\Adapter\Entity\ObjectModel;

class Finvero extends ObjectModel
{
    /**
     * @var int
     */
    public $id_product;

    /**
     * @var int
     */
    public $is_finvero_product;

    public static $definition = [
        'table' => 'finvero_products',
        'primary' => 'id_finvero_product',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'is_finvero_product' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
        ],
    ];
}
