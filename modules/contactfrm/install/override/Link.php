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

class Link extends LinkCore
{
    public function getCFormLink($id_shop, $idform, $alias, $contactfrm, $id_lang)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        $url = $this->getBaseLink($id_shop).$this->getLangLink($id_lang, null, $id_shop);
        if ($contactfrm == 'contact' && !(int)$idform && !$alias) {
            $idform = (int)Configuration::get('CONTACTFRM_DEFAULTFORM');
            $alias = DB::getInstance()->getValue('SELECT `alias` FROM `'._DB_PREFIX_.'contactform_lang`
                                                 WHERE id_lang='.(int)$id_lang.' AND fid='.(int)$idform);
            $alias = self::getRewriteString($alias);
        }
        $params = array();
        $params['fid'] = $idform;
        $params['rewrite'] = $alias;
        $rule = ($contactfrm == 'cform') ? 'cform_rule' : 'contact_rule';
        return $url.Dispatcher::getInstance()->createUrl($rule, $id_lang, $params, $this->allow, '', $id_shop);
    }
    public static function getRewriteString($s_string)
    {
        //Conversion des majuscules en minuscule
        $string = Tools::strtolower(htmlentities($s_string));
        //Listez ici tous les balises HTML que vous pourriez rencontrer
        $string = preg_replace('/&(.)(acute|cedil|circ|ring|tilde|uml|grave);/', '$1', $string);
        //Tout ce qui n'est pas caractère alphanumérique  -> _
        $string = preg_replace('/([^a-z0-9]+)/', '-', html_entity_decode($string));
        return $string;
    }
}
