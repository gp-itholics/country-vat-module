<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidProfessionalServices\CountryVatAdministration\Model;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Registry as EshopRegistry;
use OxidProfessionalServices\CountryVatAdministration\Core\Service;

class Category2CountryVat extends BaseModel
{
    /**
     * Core database table name. $sCoreTable could be only original data table name and not view name.
     *
     * @var string
     */
    protected $_sCoreTable = 'oxpscategory2countryvat';

    public function __construct()
    {
        parent::__construct();
        $this->init('oxpscategory2countryvat');
    }

    public function loadByFirstCategoryCountry(array $categoryIds, string $countryId): bool
    {
        if (empty($categoryIds)) {
            // nothing to be done
            return false;
        }

        $shopId = EshopRegistry::getConfig()->getShopId();
       
        $queryBuilder = Service::getInstance()->getQueryBuilder();
        $oxid         = (string) $queryBuilder
            ->select('OXID')
            ->from($this->getCoreTableName())
            ->where('OXCATEGORYID IN (:categoryIds)')
            ->setParameter('categoryIds', $categoryIds, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->andWhere('OXCOUNTRYID = :countryId')
            ->setParameter('countryId', $countryId)
            ->andWhere('OXSHOPID = :shopId')
            ->setParameter('shopId', $shopId)
            ->orderBy('FIELD (OXCATEGORYID, :categoryIds)')
            ->execute()
            ->fetchOne()
        ;

        return $this->load($oxid);
    }

    public function getVat()
    {
        return $this->getFieldData('vat');
    }

    /**
     * Gets field data.
     *
     * @param string $fieldName name (eg. 'oxtitle') of a data field to get
     *
     * @return mixed value of a data field
     */
    public function getFieldData($fieldName)
    {
        $longFieldName = $this->getFieldLongName($fieldName);

        return ($this->{$longFieldName} instanceof Field) ? $this->{$longFieldName}->value : null;
    }
}
