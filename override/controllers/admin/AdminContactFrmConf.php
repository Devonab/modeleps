<?php
/**
* 2016 Aretmic
*
* NOTICE OF LICENSE
*
* ARETMIC the Company grants to each customer who buys a virtual product license to use, and non-exclusive and
* worldwide. This license is valid only once for a single e-commerce store.
* No assignment of rights is hereby granted by the Company to the Customer.
* It is also forbidden for the Customer to resell or use on other virtual
* shops Products made by ARETMIC. This restriction includes all resources provided with the virtual product.
*
* @author    Aretmic SA
* @copyright 2016 Aretmic SA
* @license   ARETMIC
* International Registered Trademark & Property of Aretmic SA
*/

class AdminContactFrmConfController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $context = Context::getContext();
        $tokenModules = Tools::getAdminToken(
            'AdminModules'.(int)Tab::getIdFromClassName('AdminModules').(int)$context->employee->id
        );
        $contactfrm_default = 'index.php?tab=AdminModules&configure=contactfrm&token='.
        $tokenModules.'&task=settings';
        Tools::redirectAdmin($contactfrm_default);
    }
}
