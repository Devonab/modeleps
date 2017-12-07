<?php
/**
* 2016 Aretmic
*
* NOTICE OF LICENSE
*
* ARETMIC the Company grants to each customer who buys a
virtual product license to use, and non-exclusive and worldwide. This license is
* valid
* only once for a single e-commerce store. No assignment of
rights is hereby granted by the Company to the Customer. It is also forbidden for
* the Customer to resell or use on other virtual shops
Products made by ARETMIC. This restriction includes all resources provided with the
* virtual product.
*
* @author    Aretmic SA
* @copyright 2016 Aretmic SA
* @license   ARETMIC
* International Registered Trademark & Property of Aretmic SA
*/

class CformControllerCore extends FrontController
{
    public $php_self = 'cform';
    public $ssl = true;

    public function canonicalRedirection($canonical_url = '')
    {
        if (!$canonical_url
            || !Configuration::get('PS_CANONICAL_REDIRECT')
            || Tools::strtoupper($_SERVER['REQUEST_METHOD']) != 'GET'
            || Tools::getValue('live_edit')) {
            return;
        }
        $idform = (int)Tools::getValue('fid');
        $id_shop = $this->context->controller->context->shop->id;
        $id_lang = $this->context->controller->context->language->id;
        $alias = '';
        if (!Tools::getValue('noredirect') && (!$idform || $idform == 0 || empty($idform))) {
            parent::canonicalRedirection(
                $this->context->link->getCFormLink(
                    $id_shop,
                    $idform,
                    $alias,
                    'cform',
                    $id_lang
                )
            );
        }
    }
    public function postProcess()
    {
        $fid = (int)Tools::getValue('fid');
        $idshop = $this->context->controller->context->shop->id;
        if (!$fid || $fid == 0 || empty($fid)) {
            $listforms = Db::getInstance()->ExecuteS('SELECT * 
            FROM `'._DB_PREFIX_.'contactform` WHERE id_shop='.(int)$idshop);
            if (count($listforms) > 0) {
                $fid = $listforms[0]['fid'];
            } else {
                $fid = 0;
            }
        }
        $id_lang = $this->context->language->id;
        $error = 0;
        $errortxt = array();
        if (Tools::isSubmit('submitform')) {
            $tabfields = array();
            $tabfields['name'] = array();
            $tabfields['label'] = array();
            $tabfields['value'] = array();
            $tabfields['fields_require'] = array();
            $tabfields['confirmation'] = array();
            $tabfields['fields_type'] = array();
            $tabfields['fields_valid'] = array();
            $tabfields['order'] = array();
            $listfields = Db::getInstance()->ExecuteS('SELECT cf.*, cfl.*
                                                      FROM `'._DB_PREFIX_.'contactform_item` cf 
                                                      LEFT JOIN `'._DB_PREFIX_.'contactform_item_lang` cfl 
                                                      ON cf.`fdid` = cfl.`fdid`
                                                      WHERE cfl.`id_lang`='.(int)$id_lang.' 
                                                      AND  cf.`fid`='.(int)$fid.' AND cf.`published`=1
                                                      ORDER BY cf.`order` ASC
                                                        ');
            foreach ($listfields as $fields) {
                ${$fields['fields_name']}    =    Tools::getValue($fields['fields_name']);
                array_push($tabfields['name'], $fields['fields_name']);
                array_push($tabfields['label'], $fields['fields_title']);
                array_push($tabfields['fields_require'], $fields['fields_require']);
                array_push($tabfields['confirmation'], $fields['confirmation']);
                array_push($tabfields['fields_type'], $fields['fields_type']);
                array_push($tabfields['fields_valid'], $fields['fields_valid']);
                if ($fields['fields_type'] == 'fileup') {
                    if (!empty($_FILES[$fields['fields_name']]['type'])
                     || $_FILES[$fields['fields_name']]['type'] != '') {
                        $typename = '+'.$_FILES[$fields['fields_name']]['type'];
                    } else {
                        $typename = '';
                    }
                    if (!empty($_FILES[$fields['fields_name']]['name'])
                     || $_FILES[$fields['fields_name']]['name'] != '') {
                        $namename = '+'.$_FILES[$fields['fields_name']]['name'];
                    } else {
                        $namename = '';
                    }
                    array_push(
                        $tabfields['value'],
                        $_FILES[$fields['fields_name']]['tmp_name'].$typename.$namename
                    );
                } else {
                    array_push($tabfields['value'], $$fields['fields_name']);
                }
                //Requierd field
                if ($fields['fields_require'] == 1 && $fields['fields_type'] != 'fileup'
                    && $fields['fields_type'] != 'captcha') {
                    if (${$fields['fields_name']} == '' || empty(${$fields['fields_name']})) {
                        array_push(
                            $errortxt,
                            '<b>'.$fields['fields_title'].': </b>'.
                            (!empty($fields['error_txt']) ? $fields['error_txt'] :
                            Translate::getModuleTranslation(
                                'contactfrm',
                                'Fields not properly completed',
                                'contactfrm'
                            )
                            )
                        );
                        $error++;
                    }
                }
                if ($fields['fields_require'] == 1
                    && $fields['fields_type'] == 'fileup' && $fields['fields_type'] != 'captcha') {
                    if (empty($_FILES[$fields['fields_name']]['tmp_name'])) {
                        array_push(
                            $errortxt,
                            '<b>'.$fields['fields_title'].': </b>'.
                            (!empty($fields['error_txt']) ? $fields['error_txt'] :
                            Translate::getModuleTranslation(
                                'contactfrm',
                                'Fields not properly completed',
                                'contactfrm'
                            )
                            )
                        );
                        $error++;
                    }
                }
                //Email verification
                if ($fields['fields_type'] == 'email' || $fields['fields_valid'] == 'email') {
                    $syntaxe = '#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
                    if (!preg_match($syntaxe, ${$fields['fields_name']})) {
                        array_push(
                            $errortxt,
                            '<b>'.
                            $fields['fields_title'].': </b>'.(!empty($fields['error_txt']) ? $fields['error_txt'] :
                            Translate::getModuleTranslation('contactfrm', 'Invalid address email', 'contactfrm'))
                        );
                            $error++;
                    }
                }
                //Verify confirmation
                if ($fields['fields_require'] == 1 && $fields['fields_type'] != 'fileup'
                && $fields['fields_type'] != 'captcha') {
                    if (${$fields['fields_name']} == '' || empty(${$fields['fields_name']})) {
                        array_push(
                            $errortxt,
                            '<b>'.$fields['fields_title'].': </b>'.
                            Translate::getModuleTranslation('contactfrm', 'Required field', 'contactfrm')
                        );
                        $error++;
                    }
                }
                //Captcha validation
                if ($fields['fields_type'] == 'captcha') {
                    require_once(_PS_MODULE_DIR_.'contactfrm/library/recaptcha/recaptchalib.php');
                    $repcaptcha = new ReCaptcha(Configuration::get('CONTACTFRM_CAPTCHAPRIVKEY'));
                    $resp = $repcaptcha->verifyResponse(
                        $_SERVER['REMOTE_ADDR'],
                        Tools::getValue('g-recaptcha-response')
                    );
                    if (!$resp->success) {
                        array_push(
                            $errortxt,
                            '<b>'.$fields['fields_title'].': </b>'.
                            Translate::getModuleTranslation(
                                'contactfrm',
                                'Code not matched',
                                'contactfrm'
                            )
                        );
                        $error++;
                    }
                }
                if ($fields['confirmation'] == 1
                    && $fields['fields_type'] != 'captcha'
                    && $fields['fields_type'] != 'password'
                    && $fields['fields_type'] != 'calendar'
                    && $fields['fields_type'] != 'radio'
                    && $fields['fields_type'] != 'checkbox'
                    && $fields['fields_type'] != 'select'
                    && $fields['fields_type'] != 'button'
                    && $fields['fields_type'] != 'imagebtn'
                    && $fields['fields_type'] != 'submitbtn'
                    && $fields['fields_type'] != 'resetbtn'
                    && $fields['fields_type'] != 'fileup'
                    && $fields['fields_type'] != 'separator') {
                    if (${$fields['fields_name']} != Tools::getValue('re_'.$fields['fields_name'])) {
                        array_push(
                            $errortxt,
                            '<b>'.$fields['fields_title'].': </b>'.
                            Translate::getModuleTranslation('contactfrm', 'The value is not identical', 'contactfrm')
                        );
                        $error++;
                    }
                }
                 //File upload verification
                if ($fields['fields_type'] == 'fileup') {
                    if (!empty($_FILES[$fields['fields_name']]['tmp_name'])) {
                        $name_file = $_FILES[$fields['fields_name']]['name'];
                        $acceptedformat = array();

                        $format = Configuration::get('CONTACTFRM_UPFORMAT');
                        $tabformat = explode(',', $format);
                        $ctab = count($tabformat);
                        for ($i = 0; $i < $ctab; $i++) {
                            array_push($acceptedformat, trim($tabformat[$i]));
                        }
                        //Take uploaded file format
                        $tformat = explode('.', $name_file);
                        $fileformat = $tformat[count($tformat) - 1];
                        if (!in_array($fileformat, $acceptedformat)) {
                            array_push(
                                $errortxt,
                                '<b>'.$fields['fields_title'].': </b>'.
                                Translate::getModuleTranslation('contactfrm', 'Invalid format', 'contactfrm')
                            );
                                $error++;
                        }
                    }//end if
                }
            }//End foreach
                //Test if there is error
            if ($error > 0 || count($errortxt) > 0) {
                foreach ($errortxt as $errtxt) {
                    $this->errors[] = $errtxt;
                }
            } else {
            //If no error
                $tabfields = array();
                $tabfields['name'] = array();
                $tabfields['label'] = array();
                $tabfields['value'] = array();
                $tabfields['fields_require'] = array();
                $tabfields['confirmation'] = array();
                $tabfields['fields_type'] = array();
                $tabfields['fields_valid'] = array();
                $tabfields['order'] = array();
                foreach ($listfields as $fields) {
                    ${$fields['fields_name']}            =    Tools::getValue($fields['fields_name']);
                        array_push($tabfields['name'], $fields['fields_name']);
                        array_push($tabfields['label'], $fields['fields_title']);
                        array_push($tabfields['fields_require'], $fields['fields_require']);
                        array_push($tabfields['confirmation'], $fields['confirmation']);
                        array_push($tabfields['fields_type'], $fields['fields_type']);
                        array_push($tabfields['fields_valid'], $fields['fields_valid']);

                    if ($fields['fields_type'] == 'fileup') {
                        if (!empty($_FILES[$fields['fields_name']]['type'])
                            || $_FILES[$fields['fields_name']]['type'] != '') {
                            $typename = '+'.$_FILES[$fields['fields_name']]['type'];
                        } else {
                            $typename = '';
                        }
                        if (!empty($_FILES[$fields['fields_name']]['name'])
                            || $_FILES[$fields['fields_name']]['name'] != '') {
                            $namename = '+'.$_FILES[$fields['fields_name']]['name'];
                        } else {
                            $namename = '';
                        }
                        array_push(
                            $tabfields['value'],
                            $_FILES[$fields['fields_name']]['tmp_name'].$typename.$namename
                        );
                    } else {
                        $t = ${$fields['fields_name']};
                        array_push($tabfields['value'], $t);
                    }
                }//End foreach
                include_once(_PS_MODULE_DIR_.'contactfrm/classes/class.front.php');
                if ((int)Configuration::get('CONTACTFRM_AUTH') == 0) {
                    $this->errors[] = Cfront::sendMailContactfrm(
                        $tabfields,
                        $fid,
                        $idshop,
                        $this->context->language->id
                    );
                } else {
                    if ($this->context->customer->isLogged()) {
                        $this->errors[] = Cfront::sendMailContactfrm(
                            $tabfields,
                            $fid,
                            $idshop,
                            $this->context->language->id
                        );
                    } else {
                        $this->errors[] = Translate::getModuleTranslation(
                            'contactfrm',
                            'You must be logged in to submit form.',
                            'contactfrm'
                        );
                    }
                }
            }//En else
        } //end submit tools
    }
    public function setErrors($error)
    {
        $this->errors[] = $error;
    }
    public function initContent()
    {
        parent::initContent();
        $this->setTemplate(_PS_THEME_DIR_.'cform.tpl');
        $fid = (int)Tools::getValue('fid');
        $idshop = $this->context->controller->context->shop->id;
        if (!$fid || $fid == 0 || empty($fid)) {
            $listforms = Db::getInstance()->ExecuteS('SELECT * 
                FROM `'._DB_PREFIX_.'contactform` WHERE id_shop='.(int)$idshop);
            if (count($listforms) > 0) {
                $fid = $listforms[0]['fid'];
            } else {
                $fid = 0;
            }
        }
                $tabfields = array();
                $tabfields['name'] = array();
                $tabfields['label'] = array();
                $tabfields['value'] = array();
                $tabfields['fields_require'] = array();
                $tabfields['confirmation'] = array();
                $tabfields['fields_type'] = array();
                $tabfields['fields_valid'] = array();
                $tabfields['order'] = array();
        if ($fid != 0) {
            $listfield = Db::getInstance()->ExecuteS('SELECT *
                FROM `'._DB_PREFIX_.'contactform_item` WHERE 
                `fid` = '.(int)$fid.' AND `published` = 1 ORDER BY `order` ASC');
            if (count($listfield) == 0) {
                $this->context->smarty->assign(array(
                    'tplform' => CFtools::ferrFormat('There is no field in this form')
                ));
            } else {
                $checkversion = (version_compare(_PS_VERSION_, '1.6', '>=')) ? 1 : '';
                include_once(_PS_MODULE_DIR_.'contactfrm/classes/class.front.php');
                switch (Configuration::get('CONTACTFRM_FORM')) {
                    case 0:
                        $dataform = Cfront::viewbasicForm(
                            $tabfields,
                            $fid,
                            $this->context->language->id,
                            $idshop,
                            __PS_BASE_URI__.'modules/contactfrm/'
                        );
                        $txt_field = self::getHtmlUploadFieldLang();
                        $this->context->smarty->assign(array(
                            'tplform' => $dataform[0],
                            'meta_title' => $dataform[1].' - '.$this->context->controller->context->shop->name,
                            'path' => $dataform[1],
                            'nofile' => $txt_field[1],
                            'choosefile' => $txt_field[0],
                            'checkversion' => $checkversion
                        ));
                        break;
                    case 1:
                        $dataform = Cfront::viewForm(
                            $tabfields,
                            $fid,
                            $this->context->language->id,
                            $idshop,
                            __PS_BASE_URI__.'modules/contactfrm/'
                        );
                        $txt_field = self::getHtmlUploadFieldLang();
                        $this->context->smarty->assign(array(
                            'tplform' => $dataform[0],
                            'meta_title' => $dataform[1].' - '.$this->context->controller->context->shop->name,
                            'path' => $dataform[1],
                            'nofile' => $txt_field[1],
                            'choosefile' => $txt_field[0],
                            'checkversion' => $checkversion
                        ));
                        break;
                }
            }
        }
    }
    public function getHtmlUploadFieldLang()
    {
        $isolang = $this->context->language->iso_code;
        $txt_field = array();
        switch ($isolang) {
            case 'fr':
                $txt_field[0] = 'Parcourir';
                $txt_field[1] = 'Aucun fichier selectione';
                break;
            case 'en':
                $txt_field[0] = 'Choose file';
                $txt_field[1] = 'No field selected';
                break;
            case 'es':
                $txt_field[0] = 'Examinar';
                $txt_field[1] = 'No hay archivos seleccionados';
                break;
            case 'it':
                $txt_field[0] = 'viaggi';
                $txt_field[1] = 'Nessun file selezionato';
                break;
            case 'de':
                $txt_field[0] = 'Reise';
                $txt_field[1] = 'Keine Dateien ausgewählt';
                break;
            case 'pl':
                $txt_field[0] = 'podróz';
                $txt_field[1] = 'Brak Pliki Wybrane';
                break;
            default:
                $txt_field[0] = 'Choose file';
                $txt_field[1] = 'No field selected';
                break;
        }
        return $txt_field;
    }
}
