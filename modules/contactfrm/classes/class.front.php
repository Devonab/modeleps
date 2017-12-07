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

class Cfront
{
    public static function getForm($tabfields, $fid, $id_lang, $idshop, $default, $mypath)
    {
        require_once dirname(__FILE__).'/../../../config/config.inc.php';
        $forms = Db::getInstance()->ExecuteS('SELECT cf.*, cfl.*
            FROM `'._DB_PREFIX_.'contactform` cf
            LEFT JOIN `'._DB_PREFIX_.'contactform_lang` cfl  ON cf.`fid` = cfl.`fid`
            WHERE cfl.`id_lang`='.(int)$id_lang.' AND  cf.`fid`='.(int)$fid.' AND cf.`id_shop`=
            '.(int)$idshop);
        $output = '';
        
        $output .= '<script type="text/javascript">
        var front1_'.$fid.' = setInterval(function(){ 
            if (typeof($) != "undefined") {

                $(document).ready(function() {
                    $("#signupForm-'.$fid.'").validate({
                        rules: {';
        $listforms = Db::getInstance()->ExecuteS(
            'SELECT cf.*, cfl.* FROM `'._DB_PREFIX_.'contactform_item` cf
            LEFT JOIN `'._DB_PREFIX_.'contactform_item_lang` cfl  ON cf.`fdid` = cfl.`fdid`
            WHERE cfl.`id_lang`='.(int)$id_lang.' AND  cf.`fid`='.
            (int)$fid.' AND cf.`published`=1 ORDER BY cf.`order` ASC'
        );

        $listformsrequired = Db::getInstance()->ExecuteS(
            'SELECT cf.*, cfl.* FROM `'._DB_PREFIX_.'contactform_item` cf
            LEFT JOIN `'._DB_PREFIX_.'contactform_item_lang` cfl  ON cf.`fdid` = cfl.`fdid`
            WHERE cfl.`id_lang`='.(int)$id_lang.' AND  cf.`fid`='.(int)$fid.
            ' AND cf.`published`=1 AND cf.`fields_require`=1
            ORDER BY cf.`order` ASC'
        );

        foreach ($listformsrequired as $required) {
            $output .= $required['fields_name'].': "required",'."\n";
        }

        foreach ($listforms as $form) {
            if ($form['fields_valid'] == 'email' || $form['fields_type'] == 'email') {
                $output .= '"'.$form['fields_name'].'": {
                    '.($form['fields_require'] == 1?'required:true,':'').'
                    email: true
                },'."\n";
            }
        }
        foreach ($listforms as $form) {
            if ($form['confirmation'] == 1
                && $form['fields_type'] != 'calendar'
                && $form['fields_type'] != 'radio'
                && $form['fields_type'] != 'checkbox'
                && $form['fields_type'] != 'select'
                && $form['fields_type'] != 'button'
                && $form['fields_type'] != 'imagebtn'
                && $form['fields_type'] != 'submitbtn'
                && $form['fields_type'] != 'resetbtn'
                && $form['fields_type'] != 'fileup'
                && $form['fields_type'] != 'separator') {
                    $output .= '"re_'.$form['fields_name'].'": {
                            '.($form['fields_require'] == 1 ? 'required:true,' : '').'
                            equalTo: "form.frm-'.$fid.' #'.$form['fields_id'].'"
                        },'."\n";
            }
        }
        $output .= '},';

        foreach ($listforms as $form) {
            if ($form['fields_type'] != 'radio'
                && $form['fields_type'] != 'checkbox'
                && $form['fields_type'] != 'separator'
            ) {
                $output .= '"'.$form['fields_name'].'": "<br>'.
                (!empty($form['error_txt']) ? $form['error_txt'] :
                Translate::getModuleTranslation(
                    'contactfrm',
                    'Fields not properly completed',
                    'contactfrm'
                )).'",'."\n";
            } elseif ($form['fields_type'] == 'checkbox') {
                $output .= '"'.$form['fields_name'].'[]": "<br>'.
                    (!empty($form['error_txt']) ? $form['error_txt'] :
                    Translate::getModuleTranslation(
                        'contactfrm',
                        'Fields not properly completed',
                        'contactfrm'
                    )).'",'."\n";
            }
        }
        foreach ($listforms as $form) {
            if ($form['confirmation'] == 1
                && $form['fields_type'] != 'radio'
                && $form['fields_type'] != 'checkbox'
                && $form['fields_type'] != 'select'
                && $form['fields_type'] != 'button'
                && $form['fields_type'] != 'imagebtn'
                && $form['fields_type'] != 'submitbtn'
                && $form['fields_type'] != 'resetbtn'
                && $form['fields_type'] != 'fileup'
                && $form['fields_type'] != 'separator'
                ) {
                    $output .= '"re_'.$form['fields_name'].'": "<br>'.
                    (!empty($form['error_txt']) ? $form['error_txt'] :
                    Translate::getModuleTranslation(
                        'contactfrm',
                        'Fields not properly completed',
                        'contactfrm'
                    )).'",'."\n";
            }
        }
        //End ready function
        $output .= '});';
        //End $.validator.setDefaults
        $output .= '});';
        $output .= 'clearInterval(front1_'.$fid.');
            }
        }, 500);</script>';
        $styled = ($default) ? 'style="display:block;"' : 'style="display: none;"';
        $output .= '<div class="multiform tab_content" id="frm-'.$fid.'" '.$styled.'>';
        $output .= '<div class="rte">'.Tools::stripslashes($forms[0]['msgbeforeForm']).'</div>';
        $output .= '
        <style type="text/css">
        form.cfsh label.cferror { display: none; }    
        </style>';
        $output .= '<form enctype="multipart/form-data" class="cfsh frm-'.$fid.
        '" id="signupForm-'.$fid.'" method="POST" action="">
        <fieldset>
        <table class="cfsh">';
        foreach ($listforms as $form) {
            if ((int)$form['fields_maxtxt'] != 0) {
                $output .= '<script language="javascript" type="text/javascript">
                function calcultxtlength'.$form['fields_id'].'(nomChamp,indic){
                    var iLongueur, iLongueurRestante;
                    iLongueur = document.getElementById(nomChamp).value.length;
                    if (iLongueur>'.(int)$form['fields_maxtxt'].') {
                        document.getElementById(nomChamp).value = document.getElementById(nomChamp).value.substring(0,'.
                        (int)$form['fields_maxtxt'].');
                        iLongueurRestante = 0;
                    }
                    else {
                        iLongueurRestante = '.(int)$form['fields_maxtxt'].' - iLongueur;
                    }
                   if (iLongueurRestante <= 1)
                      jQuery("#indic").html(iLongueurRestante + "&nbsp;caract&egrave;re&nbsp;disponible");
                   else
                      jQuery("#indic").html(iLongueurRestante + "&nbsp;caract&egrave;res&nbsp;disponibles");

                }
                </script>';
                $textmax = 'onblur="calcultxtlength'.$form['fields_id'].'(\''.$form['fields_id'].'\',\'indic'.
                        $form['fields_id'].'\');" onfocus="calcultxtlength'.$form['fields_id'].'(\''.
                        $form['fields_id'].'\',\'indic'.
                        $form['fields_id'].'\');" onkeydown="calcultxtlength'.
                        $form['fields_id'].'(\''.$form['fields_id'].'\',\'indic'.$form['fields_id'].
                        '\');" onkeyup="calcultxtlength'.
                        $form['fields_id'].'(\''.$form['fields_id'].'\',\'indic'.$form['fields_id'].'\');"';
            } else {
                $textmax = '';
            }
            if ($form['fields_type'] == 'checkbox') {
                $key = array_search($form['fields_name'], $tabfields['name']);
                if (!empty($tabfields['value'][$key])) {
                    $defvalue = $tabfields['value'][$key];
                } else {
                    $defvalue = array();
                }
            } else {
                $key = array_search($form['fields_name'], $tabfields['name']);
                if (!empty($tabfields['value'][$key])) {
                    $defvalue = $tabfields['value'][$key];
                } else {
                    $defvalue = '';
                }
            }
            if ($form['fields_type'] == 'submitbtn'
            || $form['fields_type'] == 'imagebtn'
            || $form['fields_type'] == 'resetbtn'
            || $form['fields_type'] == 'separator') {
                $point = '';
            } else {
                $point = '';
            }
            $output .= '<tr>';

            if ($form['fields_type'] != 'separator'
                && $form['fields_type'] != 'simpletext'
                && $form['fields_type'] != 'textarea'
            ) {
                $output .= '<td valign="top" class="right"><label for="'.$form['fields_name'].'">'.
                $form['fields_title'].$point.
                (!empty($form['fields_desc']) ? self::info(strip_tags($form['fields_desc']), $mypath) : '').
                ' '.($form['fields_require'] == 1 ? '<sup style=" color:red">'.
                Configuration::get('CONTACTFRM_REQUIRED').'</sup>' : '').'</label></td>';
            }
            if ($form['fields_type'] == 'simpletext') {
                $output .= '<td colspan="2" valign="top" ><label class="lab-simpletxt" for="'.
                $form['fields_name'].'">'.$form['fields_title'].'</label><div class="desc-simpletxt" '.
                $form['fields_suppl'].'>'.$form['fields_desc'].'</div></td>';
            }

            if ($form['fields_type'] == 'textarea') {
                $output .= '<td valign="top" class="right"><span class="area-title">'.
                $form['fields_title'].'</span>'.($form['fields_require'] == 1 ? '<sup style=" color:red">'.
                Configuration::get('CONTACTFRM_REQUIRED').'</sup>' : '').'<div class="desc-area">'.
                $form['fields_desc'].'</div></td>';
            }
            switch ($form['fields_type']) {
                case 'separator':
                    $output .= '<td colspan=2 scop="rows"><h3 class="separator">'.$form['fields_default'].'</h3></td>';
                    break;
                case 'text':
                    $output .= '<td>';
                    $maxchar = ((int)$form['fields_maxtxt']) ? 'maxlength="'.(int)$form['fields_maxtxt'].'"' : '';
                    if ((int)Configuration::get('CONTACTFRM_SHOWCAR') == 1 && (int)$form['fields_maxtxt'] != 0) {
                        $output .= '<div class="indic" id="indic'.$form['fields_id'].'">'.(int)$form['fields_maxtxt'].
                        ' '.Translate::getModuleTranslation(
                            'contactfrm',
                            'characters available.',
                            'contactfrm'
                        ).'</div>';
                    }
                    $output .= '<input '.$textmax.' '.$maxchar.' type="text" id="'.$form['fields_id'].'" name="'.
                    $form['fields_name'].'" onblur="javascript:if(this.value==\'\')this.value=\''.
                    ($defvalue != '' ? $defvalue : $form['fields_default']).
                    '\';" onfocus="javascript:if(this.value==\''.
                    ($defvalue != '' ? $defvalue : $form['fields_default']).'\')this.value=\'\';" value="'.
                    ($defvalue != '' ? $defvalue : $form['fields_default']).'"  '.$form['fields_suppl'].' /></td>';
                    break;
                case 'email':
                    $output .= '<td>';
                    if ((int)Configuration::get('CONTACTFRM_SHOWCAR') == 1 && (int)$form['fields_maxtxt'] != 0) {
                        $output .= '<div class="indic" id="indic'.$form['fields_id'].'">'.
                            (int)$form['fields_maxtxt'].' '.
                            Translate::getModuleTranslation('contactfrm', 'characters available.', 'contactfrm').
                            '</div>';
                    }
                    $output .= '<input '.$textmax.' type="text" id="'.$form['fields_id'].'" name="'.
                    $form['fields_name'].'" onblur="javascript:if(this.value==\'\')this.value=\''.
                    ($defvalue != '' ? $defvalue : $form['fields_default']).
                    '\';" onfocus="javascript:if(this.value==\''.
                    ($defvalue != '' ? $defvalue : $form['fields_default']).'\')this.value=\'\';" value="'.
                    ($defvalue != '' ? $defvalue : $form['fields_default']).'"  '.$form['fields_suppl'].'/></td>';
                    break;
                case 'textarea':
                    $output .= '<td>';
                    if ((int)Configuration::get('CONTACTFRM_SHOWCAR') == 1 && (int)$form['fields_maxtxt'] != 0) {
                        $output .= '<div class="indic" id="indic'.$form['fields_id'].'">'.(int)$form['fields_maxtxt'].
                        ' '.Translate::getModuleTranslation(
                            'contactfrm',
                            'characters available.',
                            'contactfrm'
                        ).'</div>';
                    }
                    $output .= '<textarea '.$textmax.'  id="'.$form['fields_id'].'" name="'.
                    $form['fields_name'].'"   '.$form['fields_suppl'].'/>'.
                    ($defvalue != '' ? $defvalue : $form['fields_default']).'</textarea></td>';
                    break;
                case 'password':
                    $output .= '<td><input type="password" id="'.$form['fields_id'].'" name="'.$form['fields_name'].'"
                    onblur="javascript:if(this.value==\'\')this.value=\''.
                    ($defvalue != '' ? $defvalue : $form['fields_default']).'\';"
                    onfocus="javascript:if(this.value==\''.($defvalue != '' ? $defvalue : $form['fields_default']).
                    '\')this.value=\'\';" value="'.($defvalue != '' ? $defvalue : $form['fields_default']).'"  '.
                    $form['fields_suppl'].'></td>';
                    break;
                case 'select':
                    $output .= '<td><select name="'.$form['fields_name'].'" id="'.$form['fields_id'].'"  '.
                    $form['fields_suppl'].'>';
                    $options = explode(';', $form['fields_default']);
                    $options = array_values(array_filter($options));
                    $ctopt = count($options);
                    for ($i = 0; $i < $ctopt; $i++) {
                        $output .= '<option value="'.$options[$i].'" '.($defvalue == $options[$i] ? 'selected' : '').
                        ' >'.$options[$i].'</option>';
                    }
                    $output .= '</select></td>';
                    break;
                case 'country':
                    $states = Db::getInstance()->ExecuteS('SELECT c.*, cl.*
                        FROM `'._DB_PREFIX_.'country` c
                        LEFT JOIN `'._DB_PREFIX_.'country_lang` cl  ON c.`id_country` = cl.`id_country`
                        WHERE cl.`id_lang`='.(int)$id_lang.' ORDER BY cl.`name` ASC ');
                    $output .= '<td><select name="'.$form['fields_name'].'" id="'.
                    $form['fields_id'].'"  '.$form['fields_suppl'].'>';
                    $output .= '<option value=""> ------------------ </option>';
                    foreach ($states as $state) {
                        $output .= '<option value="'.$state['name'].'" '.
                            ($defvalue == $state['name'] ? 'selected' : '').'>'.$state['name'].'</option>';
                    }
                    $output .= '</select></td>';
                    break;
                case 'calendar':
                    $jma = array(
                                0 => 'fr',
                                1 => 'es',
                                2 => 'it',
                                3 => 'de'
                                );
                    if (in_array(Cfront::getIsocode($id_lang), $jma)) {
                        $ifformat = 'd/m/Y';
                    } else {
                        $ifformat = 'Y/m/d';
                    }
                    $output .= '    <script type="text/javascript">
                    var front2 = setInterval(function(){ 
                        if (typeof(jQuery) != "undefined") {

                            jQuery(document).ready(function() {
                                jQuery("#'.$form['fields_id'].'").datetimepicker({
                                    timepicker:false,
                                    lang:"'.Cfront::getIsocode($id_lang).'",
                                    formatDate:"'.$ifformat.'",
                                    format:"'.$ifformat.'",
                                    theme: "'.Configuration::get('CONTACTFRM_CCTPL').'"
                                });
                            });

                            clearInterval(front2);
                        }
                    }, 500);
                    </script>';
                    $output .= '<br><label for="'.$form['fields_name'].'" class="cferror">'.
                    (!empty($form['error_txt']) ? $form['error_txt'] :
                    Translate::getModuleTranslation(
                        'contactfrm',
                        'Fields not properly completed',
                        'contactfrm'
                    )).'</label></td>';
                    $output .= '
                    <td><input style="margin-right:0;" type="text" name="'.$form['fields_name'].
                    '" id="'.$form['fields_id'].'" value="'.
                    ($defvalue != '' ? $defvalue : $form['fields_default']).'"/><br>';
                    break;
                case 'time':
                    $jma = array(
                        0 => 'fr',
                        1 => 'es',
                        2 => 'it',
                        3 => 'de'
                        );
                    if (in_array(Cfront::getIsocode($id_lang), $jma)) {
                        $ifformat = 'd/m/Y';
                    } else {
                        $ifformat = 'Y/m/d';
                    }
                    $output .= '    <script type="text/javascript">
                    var front3 = setInterval(function(){ 
                        if (typeof(jQuery) != "undefined") {

                            jQuery(document).ready(function() {
                                jQuery("#'.$form['fields_id'].'").datetimepicker({
                                    datepicker:false,
                                    lang:"'.Cfront::getIsocode($id_lang).'",
                                    format:"H:i",
                                    step:5,
                                    theme: "'.Configuration::get('CONTACTFRM_CCTPL').'"
                                });
                            });

                            clearInterval(front3);
                        }
                    }, 500);
                    </script>';
                    $output .= '<br><label for="'.$form['fields_name'].'" class="cferror">'.
                        (!empty($form['error_txt']) ? $form['error_txt'] :
                        Translate::getModuleTranslation(
                            'contactfrm',
                            'Fields not properly completed',
                            'contactfrm'
                        )).'</label></td>';
                    $output .= '<td><input style="margin-right:0;" type="text" name="'.$form['fields_name'].'" id="'.
                    $form['fields_id'].'" value="'.($defvalue != '' ? $defvalue : $form['fields_default']).'"/><br>';
                    break;
                case 'calendar-time':
                    $jma = array(
                        0 => 'fr',
                        1 => 'es',
                        2 => 'it',
                        3 => 'de'
                        );
                    if (in_array(Cfront::getIsocode($id_lang), $jma)) {
                        $ifformat = 'd/m/Y';
                    } else {
                        $ifformat = 'Y/m/d';
                    }
                    $output .= '    <script type="text/javascript">
                    var front4 = setInterval(function(){ 
                        if (typeof(jQuery) != "undefined") {

                            jQuery(document).ready(function() {
                                jQuery("#'.$form['fields_id'].'").datetimepicker({
                                    lang:"'.Cfront::getIsocode($id_lang).'",
                                    format:"'.$ifformat.' H:i",
                                    formatDate:"'.$ifformat.'",
                                    step:5,
                                    theme: "'.Configuration::get('CONTACTFRM_CCTPL').'"
                                });
                            });

                            clearInterval(front4);
                        }
                    }, 500);
                    </script>';
                    $output .= '<br><label for="'.$form['fields_name'].'" class="cferror">'.
                    (!empty($form['error_txt']) ? $form['error_txt'] :
                    Translate::getModuleTranslation(
                        'contactfrm',
                        'Fields not properly completed',
                        'contactfrm'
                    )).'</label></td>';
                    $output .= '<td><input style="margin-right:0;" type="text" name="'.$form['fields_name'].'" id="'.
                    $form['fields_id'].'" value="'.($defvalue != '' ? $defvalue : $form['fields_default']).'"/><br>';
                    break;
                case 'radio':
                    $output .= '<td>';
                    $output .= '<br><label  for="'.$form['fields_name'].'" class="cferror">'.
                        (!empty($form['error_txt']) ? $form['error_txt'] :
                        Translate::getModuleTranslation(
                            'contactfrm',
                            'Fields not properly completed',
                            'contactfrm'
                        )).'</label>';
                    $options = explode(';', $form['fields_default']);
                    $options = array_values(array_filter($options));
                    $ctopt = count($options);
                    for ($i = 0; $i < $ctopt; $i++) {
                        $output .= '<input '.($defvalue == $options[$i] ? 'checked' : '').' class="radio" type="radio"
                            id="'.$form['fields_id'].$i.'" value="'.$options[$i].'" name="'.$form['fields_name'].
                            '" /><span class="labsex">'.$options[$i].'</span>';
                    }
                    $output .= '</td>';
                    break;
                case 'checkbox':
                    $options = explode(';', $form['fields_default']);
                    $output .= '<td>';
                    $output .= '<br><label  for="'.$form['fields_name'].'" class="cferror">'.
                        (!empty($form['error_txt']) ? $form['error_txt'] :
                        Translate::getModuleTranslation(
                            'contactfrm',
                            'Fields not properly completed',
                            'contactfrm'
                        )).'</label>';
                    $options = array_values(array_filter($options));
                    $ctopt = count($options);
                    for ($i = 0; $i < $ctopt; $i++) {
                        $output .= '<input type="checkbox" '.
                            (in_array($options[$i], $defvalue) ? 'checked="checked"' : '').
                            ' class="checkbox" id="'.$form['fields_id'].$i.'" value="'.$options[$i].'" name="'.
                            $form['fields_name'].'[]" /><span class="labsex">'.$options[$i].'</span>';
                    }
                    $output .= '</td>';
                    break;
                case 'captcha':
                        $output .= '<td><div id="recaptcha_div'.$fid.'"></div></td>';
                    break;
                case 'fileup':
                    $output .= '<td><input class="file" type="file" id="'.$form['fields_id'].'" name="'.
                        $form['fields_name'].'" value="'.$form['fields_default'].'" '.$form['fields_suppl'].'></td>';
                    break;
                case 'submitbtn':
                    $output .= '<td><input name="submitform" value="'.$form['fields_default'].
                        '" class="submit" type="submit" id="Send" /></td>';
                    break;
                case 'imagebtn':
                    $output .= '<td><input name="submitform" value="'.$form['fields_default'].
                        '" class="submit" type="image" id="Send" /></td>';
                    break;
                case 'resetbtn':
                    $output .= '<td><input name="'.$form['fields_name'].'" value="'.$form['fields_default'].
                        '" class="submit" type="reset" /></td>';
                    break;
            }//End switch
            $output .= '</tr>';
            if ($form['confirmation'] == 1
                && $form['fields_type'] != 'calendar'
                && $form['fields_type'] != 'captcha'
                && $form['fields_type'] != 'radio'
                && $form['fields_type'] != 'checkbox'
                && $form['fields_type'] != 'select'
                && $form['fields_type'] != 'button'
                && $form['fields_type'] != 'imagebtn'
                && $form['fields_type'] != 'submitbtn'
                && $form['fields_type'] != 'resetbtn'
                && $form['fields_type'] != 'fileup'
                && $form['fields_type'] != 'separator') {
                $output .= '<tr>';
                $output .= '<td class="right" valign="top"><label for="'.$form['fields_id'].'">'.
                    $form['confirmation_txt'].' </label></td>';
                $output .= '<td><input '.$form['fields_suppl'].' value="" type="'.
                ($form['fields_type'] == 'password' ? 'password' : 'text').'" name="re_'.$form['fields_name'].
                '" id="re_'.$form['fields_id'].'" /></td>';
                $output .= '</tr>';
            }
        }//End foreach
        $output .= '</table>';
        $output .= '<input class="submit" type="hidden" value="'.$fid.'" name="fid"/>';
        $output .= '    </fieldset>
                    </form>';
        $output .= '<div class="rte">'.Tools::stripslashes($forms[0]['msgafterForm']).'</div>';
        $output .= '</div>';
        return $output;
    }

/*=============================== BASIC FORM ==============================*/

    public static function viewbasicForm($tabfields, $fid, $id_lang, $idshop, $mypath)
    {
        require_once dirname(__FILE__).'/../../../config/config.inc.php';
        $output = '';
        $output .= '<div id="froms">';
        $forms = Db::getInstance()->ExecuteS('SELECT cf.*, cfl.*
            FROM `'._DB_PREFIX_.'contactform` cf
            LEFT JOIN `'._DB_PREFIX_.'contactform_lang` cfl  ON cf.`fid` = cfl.`fid`
            WHERE cfl.`id_lang`='.(int)$id_lang.' AND  cf.`fid`='.(int)$fid.' AND cf.`id_shop`=
            '.(int)$idshop);
        $assocform = DB::getInstance()->getValue(
            'SELECT id FROM `'._DB_PREFIX_.'contactform_relation` WHERE  `default`='.(int)$fid.' AND etat=1'
        );
        if ((int)Configuration::get('CONTACTFRM_MULTIFORM') && $assocform) {
            $displaytype = DB::getInstance()->getValue(
                'SELECT `type` FROM `'._DB_PREFIX_.'contactform_relation` WHERE id='.(int)$assocform
            );
            $myforms = DB::getInstance()->executeS(
                'SELECT fid FROM `'._DB_PREFIX_.'contactform_relation_item` WHERE rid='.
                (int)$assocform.' ORDER BY `order` ASC '
            );
            $formrelationtitle = DB::getInstance()->getValue(
                'SELECT `title` FROM `'._DB_PREFIX_.'contactform_relation_lang` 
                WHERE rid='.(int)$assocform.' AND id_lang='.(int)$id_lang
            );
            $formrelationtitle = ($formrelationtitle) ? $formrelationtitle : Translate::getModuleTranslation(
                'contactfrm',
                'Choose form',
                'contactfrm'
            );
            $output .= '<script type="text/javascript">
                        function chooseForm(idform){
                                switch(idform){';
            foreach ($myforms as $frmmy) {
                $output .= 'case '.$frmmy['fid'].' :';
                foreach ($myforms as $frmy) {
                    if ($frmy['fid'] == $frmmy['fid']) {
                        $output .= '$("#frm-'.$frmy['fid'].'").show();';
                    } else {
                        $output .= '$("#frm-'.$frmy['fid'].'").hide();';
                    }
                }
                $output .= 'break;';
            }
            $output .= '        default:';
            foreach ($myforms as $frmy) {
                if ($frmy['fid'] == $frmmy['fid']) {
                    $output .= '$("#frm-'.$frmy['fid'].'").show();';
                } else {
                    $output .= '$("#frm-'.$frmy['fid'].'").hide();';
                }
            }
            $output .= '
            break;
            }
            }
            var front5 = setInterval(function(){ 
                if (typeof($) != "undefined") {

                    $(".formError").remove();

                    clearInterval(front5);
                }
            }, 500);
            </script>';
            $output .= '<div class="lab-choice">'.$formrelationtitle.'</div>';
            $output .= '<div class="formchoice">';
            switch ((int)$displaytype) {
                case 0: //Type select
                    $output .= '<div class="sel-choice">';
                    $output .= '<select onchange="chooseForm(this.options[this.selectedIndex].value);"
                        id="formchoice">';
                    foreach ($myforms as $myform) {
                        $myfrmdata = DB::getInstance()->getRow(
                            'SELECT `fid`,`formtitle` FROM `'.
                            _DB_PREFIX_.'contactform_lang`
                            WHERE fid='.(int)$myform['fid'].' AND id_lang='.(int)$id_lang
                        );
                        $selected = ((int)$myform['fid'] == (int)$fid) ? 'selected="selected"' : '';
                        $output .= '<option '.$selected.' value="'.$myfrmdata['fid'].'">'.
                            $myfrmdata['formtitle'].'</option>';
                    }
                    $output .= '</select>';
                    $output .= '</div>';
                    break;
                case 1: //Type radio
                    $output .= '<ul class="radchoice">';
                    $myfrms = DB::getInstance()->executeS(
                        'SELECT cfr.rid as cfrrid,cfrl.* FROM `'._DB_PREFIX_.
                        'contactform_relation_item` as cfr
                        LEFT JOIN `'._DB_PREFIX_.'contactform_relation_item_lang` as cfrl ON cfrl.fid=cfr.fid
                        AND cfrl.id_lang='.(int)$id_lang.' AND cfrl.rid='.(int)$assocform.'
                        WHERE cfr.rid='.(int)$assocform.' ORDER BY cfr.order'
                    );
                    $nrbfrm = count($myfrms);
                    $chwidth = 100 / $nrbfrm;
                    $chwidth = $chwidth - 5;
                    $i = 1;
                    $nbrchoice = count($myfrms);
                    foreach ($myfrms as $myfrm) {
                        $checked = ((int)$myfrm['fid'] == (int)$fid) ? 'checked="checked"' : '';
                        $myfrmdata = DB::getInstance()->getRow(
                            'SELECT `fid`,`formtitle` FROM `'._DB_PREFIX_.'contactform_lang` 
                            WHERE fid='.(int)$myfrm['fid'].' AND id_lang='.(int)$id_lang
                        );
                        $last_class = ($nbrchoice == $i) ? 'last-choice' : '';
                        $output .= '<li class="frm-choice choice-'.$i.' '.$last_class.'" style="width:'.$chwidth.'%;">
                            <div class="frm-ch-title">'.$myfrmdata['formtitle'].'</div>
                            <input type="radio" '.$checked.' name="chform" onchange="chooseForm('.
                            $myfrm['fid'].');" value="'.$myfrm['fid'].'" />
                            <div class="txt-choice">'.$myfrm['txtsuppl'].'</div>
                            </li>';
                        $i++;
                    }
                    $output .= '</ul>';
                    break;
                case 2: // Type Tab
                    $myfrms = DB::getInstance()->executeS(
                        'SELECT cfr.rid as cfrrid,cfrl.* FROM `'._DB_PREFIX_.
                        'contactform_relation_item` as cfr
                        LEFT JOIN `'._DB_PREFIX_.'contactform_relation_item_lang` as cfrl ON cfrl.fid=cfr.fid
                        AND cfrl.id_lang='.(int)$id_lang.' AND cfrl.rid='.(int)$assocform.'
                        WHERE cfr.rid='.(int)$assocform.' ORDER BY cfr.order'
                    );
                    $nrbfrm = count($myfrms);
                    $chwidth = 100 / $nrbfrm;
                    $output .= '<link rel="stylesheet" type="text/css" href="'.__PS_BASE_URI__.
                        'modules/contactfrm/views/css/tabs2.css" />';
                    $output .= '<style type="text/css">
                                    ul.tabs li {
                                        width: '.$chwidth.'%;
                                    }
                                </style>
                            ';
                    $output .= '
                    <script>
                        var front6 = setInterval(function(){ 
                            if (typeof($) != "undefined") {

                                $(document).ready(function() {
                                    //When page loads...
                                    $(".tab_content").hide(); //Hide all content
                                    $("ul.tabs li:first").addClass("active").show(); //Activate first tab
                                    $(".tab_content:first").show(); //Show first tab content
                                    //On Click Event
                                    $("ul.tabs li").click(function() {
                                        $("ul.tabs li").removeClass("active"); //Remove any "active" class
                                        $(this).addClass("active"); //Add "active" class to selected tab
                                        $(".tab_content").hide(); //Hide all tab content
                                        $(".formError").remove();
                                        var activeTab = $(this).find("a").attr("href");
                                        $(activeTab).fadeIn(); //Fade in the active ID content
                                        return false;
                                    });
                                });

                                clearInterval(front6);
                            }
                        }, 500);

                    </script>';
                    $output .= '<ul class="tabs">';
                    $j = 1;
                    foreach ($myfrms as $myfrm) {
                        $checked = ((int)$myfrm['fid'] == (int)$fid) ? 'checked="checked"' : '';
                        $myfrmdata = DB::getInstance()->getRow(
                            'SELECT `fid`,`formtitle` FROM `'._DB_PREFIX_.'contactform_lang` WHERE fid='.
                            (int)$myfrm['fid'].' AND id_lang='.(int)$id_lang
                        );
                        $output .= '<li class="tabslli tab-li_'.$j.'"><a title="'.strip_tags($myfrm['txtsuppl']).
                        '" href="#frm-'.$myfrm['fid'].'"><span class="CFtitle">'.$myfrmdata['formtitle'].
                        '</span><span class="CFaddtext">'.strip_tags($myfrm['txtsuppl']).'</span></a></li>';
                        $j++;
                    }
                    $output .= '</ul>';
                    break;
            }
            $output .= '</div>';
        }
        $output .= Cfront::getForm($tabfields, $fid, $id_lang, $idshop, 1, $mypath);
        $scriptre = '<script type="text/javascript">
                    var onloadCallback = function() {';
        switch (Configuration::get('CONTACTFRM_CAPTCHATHEME')) {
            case '1':
                $thm = 'light';
                break;
            case '2':
                $thm = 'dark';
                break;
            default:
                $thm = 'light';
                break;
        }
        $scriptre .= Cfront::getRecaptchaScript($fid, $id_lang, $thm);
        if ((int)Configuration::get('CONTACTFRM_MULTIFORM') && $assocform) {
            $formes = DB::getInstance()->executeS(
                'SELECT fid FROM `'._DB_PREFIX_.'contactform_relation_item` WHERE rid='.
                (int)$assocform.' AND fid!='.(int)$fid
            );
            foreach ($formes as $frme) {
                $output .= Cfront::getForm($tabfields, $frme['fid'], $id_lang, $idshop, 0, $mypath);
                $scriptre .= Cfront::getRecaptchaScript($frme['fid'], $id_lang, $thm);
            }
        }
        $scriptre .= '};
            </script>';
        $output .= $scriptre;
        $output .= '</div>';
        /* English en
        Dutch    nl
        French    fr
        German    de
        Portuguese    pt
        Russian    ru
        Spanish    es
        Turkish    tr
        Italian    it */
        $caplang = array(
                0 => 'en',
                1 => 'nl',
                2 => 'fr',
                3 => 'de',
                4 => 'pt',
                5 => 'ru',
                6 => 'es',
                7 => 'tr',
                8 => 'it'
                );
        $langcap = Language::getLanguage($id_lang);
        $captchalang = (in_array($langcap['iso_code'], $caplang)) ? $langcap['iso_code'] : 'en';
        $output .= '<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit&hl='.
        $captchalang.'" async defer></script>';
        $dataform = array();
        $dataform[0] = $output;
        $dataform[1] = $forms[0]['formtitle'];
        return $dataform;
    }
    public static function checkForm($tabfields, $fid, $id_lang, $idshop, $default, $mypath, $in_column = 0)
    {
        require_once dirname(__FILE__).'/../../../config/config.inc.php';
        $forms = Db::getInstance()->ExecuteS('SELECT cf.*, cfl.*
            FROM `'._DB_PREFIX_.'contactform` cf
            LEFT JOIN `'._DB_PREFIX_.'contactform_lang` cfl  ON cf.`fid` = cfl.`fid`
            WHERE cfl.`id_lang`='.(int)$id_lang.' AND  cf.`fid`='.(int)$fid.' AND cf.`id_shop`=
            '.(int)$idshop);
        $output = '<script>
            var front7'.$fid.' = setInterval(function(){ 
                if (typeof(jQuery) != "undefined") {

                    jQuery(document).ready(function(){
                        jQuery("#form'.$fid.'").validationEngine();
                    });

                    clearInterval(front7'.$fid.');
                }
            }, 500);
        </script>';
        $actpost = '';
        if ($in_column) {
            $ctrlr = (Configuration::get('CONTACTFRM_FILENAME') == 'contact.php') ? 'contact' : 'cform';
            $link = new Link();
            $actpost = $link->getPageLink($ctrlr);
        }
        $styled = ($default) ? 'style="display:block;"' : 'style="display: none;"';
        $output .= '<div class="multiform tab_content" id="frm-'.$fid.'" '.$styled.'>';
        $output .= '<div class="rte">'.(!empty($forms[0]['msgbeforeForm']) ? $forms[0]['msgbeforeForm'] : '').'</div>';
        $output .= '<form enctype="multipart/form-data" id="form'.$fid.'" class="formular"
        method="post" action="'.$actpost.'" style="width:'.Configuration::get('CONTACTFRM_WIDTH').'%">';
        $listforms = Db::getInstance()->ExecuteS('SELECT cf.*, cfl.*
            FROM `'._DB_PREFIX_.'contactform_item` cf
            LEFT JOIN `'._DB_PREFIX_.'contactform_item_lang` cfl  ON cf.`fdid` = cfl.`fdid`
            WHERE cfl.`id_lang`='.(int)$id_lang.' AND  cf.`fid`='.(int)$fid.' AND cf.`published`=1
            ORDER BY cf.`order` ASC
            ');
        $i = 1;
        foreach ($listforms as $form) {
            $post = Tools::getValue($form['fields_name']);
            if ($post && $post != '') {
                $bypost = $post;
            } else {
                $bypost = false;
            }
            if ($form['fields_type'] == 'checkbox') {
                $key = array_search($form['fields_name'], $tabfields['name']);
                if (!empty($tabfields['value'][$key])) {
                    $defvalue = $tabfields['value'][$key];
                } else {
                    $defvalue = array();
                }
            } else {
                $key = array_search($form['fields_name'], $tabfields['name']);
                if (!empty($tabfields['value'][$key])) {
                    $defvalue = $tabfields['value'][$key];
                } else {
                    $defvalue = '';
                }
            }
            if ($form['fields_type'] == 'submitbtn'
                || $form['fields_type'] == 'imagebtn'
                || $form['fields_type'] == 'resetbtn') {
                $point = '';
            } else {
                $point = '';
            }
            if ($form['fields_type'] != 'radio' && $form['fields_type'] != 'checkbox') {
                $output .= '<div class="field-'.$i.' cfrow">';
            }

            if ($form['fields_type'] != 'separator'
                && $form['fields_type'] != 'simpletext'
                && $form['fields_type'] != 'textarea') {
                $classcaptcha = ($form['fields_type'] == 'captcha') ? 'class="capt-class"' : '';
                $output .= '<span '.$classcaptcha.'>'.$form['fields_title'].
                $point.(!empty($form['fields_desc']) ? self::info(strip_tags($form['fields_desc']), $mypath) : '').
                '</span>'.($form['fields_require'] == 1 ? '<sup style=" color:red">'.
                Configuration::get('CONTACTFRM_REQUIRED').'</sup>' : '');
            }
            if ($form['fields_type'] == 'textarea') {
                $output .= '<span class="area-title">'.
                $form['fields_title'].'</span>'.
                ($form['fields_require'] == 1 ? '<sup style=" color:red">'.
                Configuration::get('CONTACTFRM_REQUIRED').'</sup>' : '').
                '<div class="desc-area">'.$form['fields_desc'].'</div>';
            }
            if ($form['fields_type'] == 'simpletext') {
                $output .= '<span class="title-simple">'.$form['fields_title'].'</span>';
            }
            if ($form['fields_type'] == 'radio' || $form['fields_type'] == 'checkbox') {
                $output .= '<br>';
            }
            $class = 'validate[';
            //Prepare class
            if ($form['fields_require'] == 1) {
                $class .= 'required';
            } else {
                $class .= 'optional';
            }

            if ($form['fields_valid'] == 'email' || $form['fields_type'] == 'email') {
                $class .= ',custom[email]';
            } elseif ($form['fields_valid'] == 'url') {
                $class .= ',custom[url]';
            } elseif ($form['fields_valid'] == 'numeric') {
                $class .= ',custom[integer]';
            } elseif ($form['fields_valid'] == 'alpha') {
                $class .= ',custom[onlyLetterSp]';
            }
            //End preparing classes
            $class .= ']';
            switch ($form['fields_type']) {
                case 'separator':
                    $output .= '<div class="separator">'.$form['fields_default'].'</div>';
                    break;
                case 'simpletext':
                    $output .= '<div '.$form['fields_suppl'].' class="simpletext">'.$form['fields_desc'].'</div>';
                    break;
                case 'text':
                    $class .= ' text-input';
                    $maxchar = ((int)$form['fields_maxtxt']) ? 'maxlength="'.(int)$form['fields_maxtxt'].'"' : '';
                    $output .= '<input type="text" id="'.$form['fields_id'].$fid.'" '.
                    $maxchar.' name="'.$form['fields_name'].'" placeholder="'.
                    ($defvalue != '' ? $defvalue : $form['fields_default']).'" value="'.
                    ($bypost ? $bypost : '').'"  class="'
                    .$class.'"  '.$form['fields_suppl'].' >';
                    break;
                case 'password':
                    $class .= ' text-input';
                    $output .= '<input type="password" id="'.$form['fields_id'].$fid.'" name="'.
                    $form['fields_name'].'" placeholder="'.
                    ($defvalue != '' ? $defvalue : $form['fields_default']).'" value="'.
                    ($bypost ? $bypost : '').'"  class="'.
                    $class.'"  '.$form['fields_suppl'].'>';
                    break;
                case 'select':
                    $class .= ' text-select';
                    $output .= '<select name="'.$form['fields_name'].'" id="'.$form['fields_id'].$fid.
                        '" class="'.$class.'" '.$form['fields_suppl'].'>';
                    $options = explode(';', $form['fields_default']);
                    $options = array_values(array_filter($options));
                    $ctoptn = count($options);
                    for ($i = 0; $i < $ctoptn; $i++) {
                        $output .= '<option value="'.$options[$i].'" '.
                        ($bypost == $options[$i] ? 'selected="selected"' :
                            ($defvalue == $options[$i] ? 'selected' : '')).'>'.
                        $options[$i].'</option>';
                    }
                    $output .= '</select>';
                    break;
                case 'country':
                    $class .= ' text-select';
                    $states = Db::getInstance()->ExecuteS('SELECT c.*, cl.*
                        FROM `'._DB_PREFIX_.'country` c
                        LEFT JOIN `'._DB_PREFIX_.'country_lang` cl  ON c.`id_country` = cl.`id_country`
                        WHERE cl.`id_lang`='.(int)$id_lang.' ORDER BY cl.`name` ASC ');
                    $output .= '<select name="'.$form['fields_name'].'" id="'.$form['fields_id'].$fid.'" class="'.
                    $class.'" '.$form['fields_suppl'].'>';
                    $output .= '<option value=""> ------------------ </option>';
                    foreach ($states as $state) {
                        $output .= '<option value="'.$state['name'].'"  '.
                            ($bypost == $state['name'] ? 'selected="selected"' :
                            ($defvalue == $state['name'] ? 'selected' : '')).'>'
                            .$state['name'].'</option>';
                    }
                    $output .= '</select>';
                    break;
                case 'email':
                    $class .= ' text-input';
                    $output .= '<input type="text" id="'.$form['fields_id'].$fid.'" name="'.
                    $form['fields_name'].'"  class="'.$class.'"  '.$form['fields_suppl'].
                    ' placeholder="'.
                    ($defvalue != '' ? $defvalue : $form['fields_default']).'" value="'.
                    ($bypost ? $bypost : '').'" />';
                    break;
                case 'textarea':
                    $output .= '<textarea '.$form['fields_suppl'].'  class="'.$class.'"  id="'.
                    $form['fields_id'].$fid.'" name="'.$form['fields_name'].'" >'.
                    ($bypost ? $bypost : ($defvalue != '' ? $defvalue : $form['fields_default'])).'</textarea>';
                    break;
                case 'calendar':
                    $class .= ' text-input';
                    $jma = array(
                        0 => 'fr',
                        1 => 'es',
                        2 => 'it',
                        3 => 'de'
                        );
                    if (in_array(Cfront::getIsocode($id_lang), $jma)) {
                        $ifformat = 'd/m/Y';
                    } else {
                        $ifformat = 'Y/m/d';
                    }

                    $output .= '    <script type="text/javascript">
                        var front8'.$fid.' = setInterval(function(){ 
                            if (typeof(jQuery) != "undefined") {

                                jQuery(document).ready(function() {
                                    jQuery("#'.$form['fields_id'].$fid.'").datetimepicker({
                                        timepicker:false,
                                        lang:"'.Cfront::getIsocode($id_lang).'",
                                        formatDate:"'.$ifformat.'",
                                        format:"'.$ifformat.'",
                                        theme: "'.Configuration::get('CONTACTFRM_CCTPL').'"
                                    });
                                });

                                clearInterval(front8'.$fid.');
                            }
                        }, 500);
                    </script>';
                    $output .= '<br><input class="'.$class.'" type="text" name="'.
                    $form['fields_name'].'" id="'.$form['fields_id'].$fid.'" value="'.
                    ($bypost ? $bypost : ($defvalue != '' ? $defvalue : $form['fields_default'])).'"/><br>';
                    break;
                case 'time':
                    $class .= ' text-input';
                    $jma = array(
                        0 => 'fr',
                        1 => 'es',
                        2 => 'it',
                        3 => 'de'
                        );
                    if (in_array(Cfront::getIsocode($id_lang), $jma)) {
                        $ifformat = 'd/m/Y';
                    } else {
                        $ifformat = 'Y/m/d';
                    }
                    $output .= '    <script type="text/javascript">
                    var front9'.$fid.' = setInterval(function(){ 
                        if (typeof(jQuery) != "undefined") {

                            jQuery(document).ready(function() {
                                jQuery("#'.$form['fields_id'].$fid.'").datetimepicker({
                                    datepicker:false,
                                    lang:"'.Cfront::getIsocode($id_lang).'",
                                    format:"H:i",
                                    step:5,
                                    theme: "'.Configuration::get('CONTACTFRM_CCTPL').'"
                                });
                            });

                            clearInterval(front9'.$fid.');
                        }
                    }, 500);

                    </script>';
                    $output .= '<br><input class="'.$class.'" type="text" name="'.
                    $form['fields_name'].'" id="'.$form['fields_id'].$fid.'" value="'.
                    ($bypost ? $bypost : ($defvalue != '' ? $defvalue : $form['fields_default'])).'"/><br>';
                    break;
                case 'calendar-time':
                    $class .= ' text-input';
                    $jma = array(
                                0 => 'fr',
                                1 => 'es',
                                2 => 'it',
                                3 => 'de'
                                );
                    if (in_array(Cfront::getIsocode($id_lang), $jma)) {
                        $ifformat = 'd/m/Y';
                    } else {
                        $ifformat = 'Y/m/d';
                    }
                    $output .= '    <script type="text/javascript">
                        var front10'.$fid.' = setInterval(function(){ 
                            if (typeof(jQuery) != "undefined") {

                                jQuery(document).ready(function() {
                                    jQuery("#'.$form['fields_id'].$fid.'").datetimepicker({
                                        lang:"'.Cfront::getIsocode($id_lang).'",
                                        format:"'.$ifformat.' H:i",
                                        formatDate:"'.$ifformat.'",
                                        step:5,
                                        theme: "'.Configuration::get('CONTACTFRM_CCTPL').'"
                                    });
                                });

                                clearInterval(front10'.$fid.');
                            }
                        }, 500);
                    </script>';
                    $output .= '<br><input class="'.$class.'" type="text" name="'.
                    $form['fields_name'].'" id="'.$form['fields_id'].$fid.'" value="'.
                    ($bypost ? $bypost : ($defvalue != '' ? $defvalue : $form['fields_default'])).'"/><br>';
                    break;
                case 'captcha':
                        $output .= '<div id="recaptcha_div'.$fid.'"></div>';
                    break;
                case 'radio':
                    $class .= ' radio';
                    $output .= '<div class="group">';
                    $options = explode(';', $form['fields_default']);
                    $options = array_values(array_filter($options));
                    $ctpo = count($options);
                    for ($i = 0; $i < $ctpo; $i++) {
                        $output .= '<div class="rdclass" style="display:'.
                        (Configuration::get('CONTACTFRM_CFGRADIO') == 1 ? 'inline' : 'block').'"><input  '.
                        ($bypost == $options[$i] ? 'checked="checked"' :
                            ($defvalue == $options[$i] ? 'checked="checked"' : '')).' '.$form['fields_suppl'].' id="'.
                        $form['fields_id'].$fid.$i.'" class="'.$class.'" type="radio" value="'.$options[$i].
                        '" name="'.$form['fields_name'].'">'.$options[$i].'</div>';
                    }
                    $output .= '</div>';
                    $output .= '<br>';
                    break;
                case 'checkbox':
                    $class .= ' checkbox';
                    $options = explode(';', $form['fields_default']);
                    $options = array_values(array_filter($options));
                    $output .= '<div class="group">';
                    $ctopt = count($options);
                    for ($i = 0; $i < $ctopt; $i++) {
                        $output .= '<div class="rdclass" style="display:'.
                        (Configuration::get('CONTACTFRM_CFGCKBOX') == 1 ? 'inline' : 'block').
                        '"><input type="checkbox"  '.(in_array($options[$i], $defvalue) ? 'checked="checked"' : '').' '.
                        $form['fields_suppl'].'  id="'.$form['fields_id'].$fid.$i.'" class="'.
                        $class.'"  value="'.$options[$i].'" name="'.$form['fields_name'].
                        '[]">'.$options[$i].'</div>';
                    }
                    $output .= '</div>';
                    $output .= '<br>';
                    break;
                case 'fileup':
                    $class .= ' text-input';
                    $output .= '<input style=" display:block" type="file" id="'.$form['fields_id'].$fid.'" name="'.
                    $form['fields_name'].'" value="'.$form['fields_default'].'"  class="'.$class.'"  '.
                    $form['fields_suppl'].'>';
                    $output .= '<br>';
                    break;
                case 'submitbtn':
                    $output .= '<input name="submitform" value="'.$form['fields_default'].'" class="submit"
                    type="submit" id="Send" />';
                    break;
                case 'imagebtn':
                    $output .= '<input name="submitform" value="'.$form['fields_default'].
                        '" class="submit" type="image"  id="Send"/>';
                    break;
                case 'resetbtn':
                    $output .= '<input name="'.$form['fields_name'].'" value="'.
                        $form['fields_default'].'" class="submit" type="reset" />';
                    break;
            }
            if ($form['fields_type'] != 'radio' && $form['fields_type'] != 'checkbox') {
                $output .= '</div>';
            }

            if ($form['confirmation'] == 1
                && $form['fields_type'] != 'captcha'
                && $form['fields_type'] != 'password'
                && $form['fields_type'] != 'calendar'
                && $form['fields_type'] != 'radio'
                && $form['fields_type'] != 'checkbox'
                && $form['fields_type'] != 'select'
                && $form['fields_type'] != 'button'
                && $form['fields_type'] != 'imagebtn'
                && $form['fields_type'] != 'submitbtn'
                && $form['fields_type'] != 'resetbtn'
                && $form['fields_type'] != 'fileup'
                && $form['fields_type'] != 'separator') {
                $output .= '<div class="field-'.$i.'-a">';
                $output .= '<span>'.$form['confirmation_txt'].' :</span>';
                $output .= '<input '.$form['fields_suppl'].'  value="" class="validate[required,equals['.
                $form['fields_name'].$fid.']] text-input" type="text" name="re_'.$form['fields_name'].
                '" id="re_'.$form['fields_name'].$fid.'" />';
                $output .= '</div>';
            }
            if ($form['confirmation'] == 1 && $form['fields_type'] == 'password') {
                $output .= '<div class="field-'.$i.'-a">';
                $output .= '<span>'.$form['confirmation_txt'].' :</span>';
                $output .= '<input value="" '.$form['fields_suppl'].' class="validate[required,equals['.
                $form['fields_id'].$fid.']] text-input" type="password" name="re_'.
                $form['fields_name'].'" id="re_'.$form['fields_name'].$fid.'" />';
                $output .= '</div>';
            }
            $i++;
        }//End foreach
        $output .= '<input class="submit" type="hidden" value="'.$fid.'" name="fid"/><hr/>';
        $output .= '<hr/></form>';
        $output .= '<div class="rte">'.(!empty($forms[0]['msgafterForm']) ? $forms[0]['msgafterForm'] : '').'</div>';
        $output .= '</div>';
        return $output;
    }
/*============================== HIGH LEVEL FORM =================================*/
    public static function viewForm($tabfields, $fid, $id_lang, $idshop, $mypath)
    {
        require_once dirname(__FILE__).'/../../../config/config.inc.php';
        $output = '';
        $output .= '
        <div id="froms">';
        $forms = Db::getInstance()->ExecuteS('SELECT cf.*, cfl.*
            FROM `'._DB_PREFIX_.'contactform` cf
            LEFT JOIN `'._DB_PREFIX_.'contactform_lang` cfl  ON cf.`fid` = cfl.`fid`
            WHERE cfl.`id_lang`='.(int)$id_lang.' AND  cf.`fid`='.(int)$fid.' AND cf.`id_shop`=
            '.(int)$idshop);
        $assocform = DB::getInstance()->getValue(
            'SELECT id FROM `'._DB_PREFIX_.'contactform_relation` WHERE  `default`='.(int)$fid.' AND etat=1'
        );

        if ((int)Configuration::get('CONTACTFRM_MULTIFORM') && $assocform) {
            $displaytype = DB::getInstance()->getValue(
                'SELECT `type` FROM `'._DB_PREFIX_.'contactform_relation` WHERE id='.(int)$assocform
            );
            $myforms = DB::getInstance()->executeS(
                'SELECT cri.fid FROM `'._DB_PREFIX_.'contactform_relation_item` as cri WHERE rid='.
                (int)$assocform.' ORDER BY cri.order'
            );
            $formrelationtitle = DB::getInstance()->getValue(
                'SELECT `title` FROM `'._DB_PREFIX_.'contactform_relation_lang`
                WHERE rid='.(int)$assocform.' AND id_lang='.(int)$id_lang
            );
            $formrelationtitle = ($formrelationtitle) ? $formrelationtitle : Translate::getModuleTranslation(
                'contactfrm',
                'Choose form',
                'contactfrm'
            );
            $output .= '<script type="text/javascript">
                        function chooseForm(idform){
                            var fidform = parseInt(idform);
                                switch(fidform){';
            foreach ($myforms as $frmmy) {
                $output .= 'case '.$frmmy['fid'].' :';
                foreach ($myforms as $frmy) {
                    if ($frmy['fid'] == $frmmy['fid']) {
                        $output .= '$("#frm-'.$frmy['fid'].'").show();';
                    } else {
                        $output .= '$("#frm-'.$frmy['fid'].'").hide();';
                    }
                }
                $output .= 'break;';
            }
            $output .= 'default:';
            foreach ($myforms as $frmy) {
                if ($frmy['fid'] == $frmmy['fid']) {
                    $output .= '$("#frm-'.$frmy['fid'].'").show();';
                } else {
                    $output .= '$("#frm-'.$frmy['fid'].'").hide();';
                }
            }
            $output .= 'break;
                }
                    }
                var front11 = setInterval(function(){ 
                    if (typeof($) != "undefined") {

                        $(".formError").remove();

                        $(document).ready(function() {
                            $("div#froms ul.tabs li.tabslli").each(function(){
                                if ($(this).width() < 125) {
                                    $(this).css("font-size", "small");
                                }
                            });
                        });

                        clearInterval(front11);
                    }
                }, 500);
            </script>';
            $output .= '<div class="lab-choice">'.$formrelationtitle.'</div>';
            $output .= '<div class="formchoice">';
            switch ((int)$displaytype) {
                case 0: //Type select
                    $output .= '<div class="sel-choice">';
                    $output .= '<select onchange="chooseForm(this.options[this.selectedIndex].value);"
                        id="formchoice">';
                    foreach ($myforms as $myform) {
                        $myfrmdata = DB::getInstance()->getRow(
                            'SELECT `fid`,`formtitle` FROM `'
                            ._DB_PREFIX_.'contactform_lang`
                            WHERE fid='.(int)$myform['fid'].' AND id_lang='.(int)$id_lang
                        );
                        $selected = ((int)$myform['fid'] == (int)$fid) ? 'selected="selected"' : '';
                        $output .= '<option '.$selected.' value="'.$myfrmdata['fid'].'">'.
                            $myfrmdata['formtitle'].'</option>';
                    }
                    $output .= '</select>';
                    $output .= '</div>';
                    break;
                case 1: //Type radio
                    $output .= '<ul class="radchoice">';
                    $myfrms = DB::getInstance()->executeS(
                        'SELECT cfr.rid as cfrrid,cfrl.* FROM `'._DB_PREFIX_.
                        'contactform_relation_item` as cfr
                        LEFT JOIN `'._DB_PREFIX_.'contactform_relation_item_lang` as cfrl
                        ON cfrl.fid=cfr.fid AND cfrl.id_lang='.(int)$id_lang.' AND cfrl.rid='.(int)$assocform.'
                        WHERE cfr.rid='.(int)$assocform.' ORDER BY cfr.order'
                    );
                    $nrbfrm = count($myfrms);
                    $chwidth = 100 / $nrbfrm;
                    $chwidth = $chwidth - 5;
                    $i = 1;
                    $nbrchoice = count($myfrms);
                    foreach ($myfrms as $myfrm) {
                        $checked = ((int)$myfrm['fid'] == (int)$fid) ? 'checked="checked"' : '';
                        $myfrmdata = DB::getInstance()->getRow(
                            'SELECT `fid`,`formtitle` FROM `'.
                            _DB_PREFIX_.'contactform_lang` WHERE fid='.(int)$myfrm['fid'].
                            ' AND id_lang='.(int)$id_lang
                        );
                        $last_class = ($nbrchoice == $i) ? 'last-choice' : '';
                        $output .= '<li class="frm-choice choice-'.$i.' '.$last_class.'" style="width:'.$chwidth.'%;">
                                    <div class="frm-ch-title">'.$myfrmdata['formtitle'].'</div>
                                    <input type="radio" '.$checked.' name="chform" onchange="chooseForm('.
                                    $myfrm['fid'].');" value="'.$myfrm['fid'].'" />
                                    <div class="txt-choice">'.$myfrm['txtsuppl'].'</div>
                            </li>';
                            $i++;
                    }
                    $output .= '</ul>';
                    break;
                case 2: // Type Tab
                    $myfrms = DB::getInstance()->executeS(
                        'SELECT cfr.rid as cfrrid,cfrl.* FROM `'._DB_PREFIX_.
                        'contactform_relation_item` as cfr
                        LEFT JOIN `'._DB_PREFIX_.'contactform_relation_item_lang` as cfrl
                        ON cfrl.fid=cfr.fid AND cfrl.id_lang='.(int)$id_lang.' AND cfrl.rid='.(int)$assocform.'
                        WHERE cfr.rid='.(int)$assocform.' ORDER BY cfr.order'
                    );
                    $nrbfrm = count($myfrms);
                    $chwidth = 100 / $nrbfrm;
                    $output .= '<link rel="stylesheet" type="text/css" href="'.__PS_BASE_URI__.
                        'modules/contactfrm/views/css/tabs2.css" />';
                    $output .= '<style type="text/css">
                                    ul.tabs li {
                                        width: '.$chwidth.'%;
                                    }
                                </style>';
                    $output .= '
                        <script>
                        var front12 = setInterval(function(){ 
                            if (typeof($) != "undefined") {

                                $(document).ready(function() {
                                    //When page loads...
                                    $(".tab_content").hide(); //Hide all content
                                    $("ul.tabs li:first").addClass("active").show(); //Activate first tab
                                    $(".tab_content:first").show(); //Show first tab content
                                    //On Click Event
                                    $("ul.tabs li").click(function() {                
                                        $("ul.tabs li").removeClass("active"); //Remove any "active" class
                                        $(this).addClass("active"); //Add "active" class to selected tab
                                        $(".tab_content").hide(); //Hide all tab content
                                        $(".formError").remove();
                                        var activeTab = $(this).find("a").attr("href");
                                        $(activeTab).fadeIn(); //Fade in the active ID content
                                        return false;
                                    });
                                });

                                clearInterval(front12);
                            }
                        }, 500);
                        </script>';
                    $output .= '<ul class="tabs">';
                    $j = 1;
                    foreach ($myfrms as $myfrm) {
                        $checked = ((int)$myfrm['fid'] == (int)$fid) ? 'checked="checked"' : '';
                        $myfrmdata = DB::getInstance()->getRow(
                            'SELECT `fid`,`formtitle` FROM `'.
                            _DB_PREFIX_.'contactform_lang`
                            WHERE fid='.(int)$myfrm['fid'].' AND id_lang='.(int)$id_lang
                        );
                        $output .= '<li class="tabslli tab-li_'.$j.'"><a title="'.strip_tags($myfrm['txtsuppl']).
                        '" href="#frm-'.$myfrm['fid'].'"><span class="CFtitle">'.$myfrmdata['formtitle'].
                        '</span><span class="CFaddtext">'.strip_tags($myfrm['txtsuppl']).'</span></a></li>';
                        $j++;
                    }
                    $output .= '</ul>';
                    break;
            }

            $output .= '</div>';
        }
        $output .= Cfront::checkForm($tabfields, $fid, $id_lang, $idshop, 1, $mypath);
        $scriptre = '<script type="text/javascript">
                    var onloadCallback = function() {';
        switch (Configuration::get('CONTACTFRM_CAPTCHATHEME')) {
            case '1':
                $thm = 'light';
                break;
            case '2':
                $thm = 'dark';
                break;
            default:
                $thm = 'light';
                break;
        }
        $scriptre .= Cfront::getRecaptchaScript($fid, $id_lang, $thm);
        if ((int)Configuration::get('CONTACTFRM_MULTIFORM') && $assocform) {
            $formes = DB::getInstance()->executeS(
                'SELECT fid FROM `'._DB_PREFIX_.'contactform_relation_item` WHERE rid='.
                (int)$assocform.' AND fid!='.(int)$fid
            );
            foreach ($formes as $frme) {
                $output .= Cfront::checkForm($tabfields, $frme['fid'], $id_lang, $idshop, 0, $mypath);
                $scriptre .= Cfront::getRecaptchaScript($frme['fid'], $id_lang, $thm);
            }
        }
        $scriptre .= '};
            </script>
        ';
        $output .= $scriptre;
        $output .= '</div>';
        /* English en
        Dutch    nl
        French    fr
        German    de
        Portuguese    pt
        Russian    ru
        Spanish    es
        Turkish    tr
        Italian    it */
        $caplang = array(
            0 => 'en',
            1 => 'nl',
            2 => 'fr',
            3 => 'de',
            4 => 'pt',
            5 => 'ru',
            6 => 'es',
            7 => 'tr',
            8 => 'it'
            );
        $langcap = Language::getLanguage($id_lang);
        $captchalang = (in_array($langcap['iso_code'], $caplang)) ? $langcap['iso_code'] : 'en';
        $output .= '<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit&hl='.
        $captchalang.'" async defer></script>';
        $dataform = array();
        $dataform[0] = $output;
        $dataform[1] = $forms[0]['formtitle'];
        return $dataform;
    }

    public static function getRecaptchaScript($fid, $id_lang, $thm)
    {
        require_once dirname(__FILE__).'/../../../config/config.inc.php';
        $rescript = '';
        $listforms = Db::getInstance()->ExecuteS('SELECT cf.*, cfl.*
            FROM `'._DB_PREFIX_.'contactform_item` cf
            LEFT JOIN `'._DB_PREFIX_.'contactform_item_lang` cfl  ON cf.`fdid` = cfl.`fdid`
            WHERE cfl.`id_lang`='.(int)$id_lang.' AND  cf.`fid`='.(int)$fid.' AND cf.`published`=1
            ORDER BY cf.`order` ASC
            ');

        foreach ($listforms as $form) {
            if ($form['fields_type'] == 'captcha') {
                $rescript .= 'grecaptcha.render("recaptcha_div'.$fid.'", {
                  "sitekey" : "'.Configuration::get('CONTACTFRM_CAPTCHAPUBKEY').'",
                "theme" : "'.$thm.'"
                        });
                ';
            }
        }
        return $rescript;
    }

    public static function getIsocode($id_lang)
    {
        require_once dirname(__FILE__).'/../../../config/config.inc.php';
        /* English en
        Dutch    nl
        French    fr
        German    de
        Portuguese    pt
        Russian    ru
        Spanish    es
        Turkish    tr
        Italian    it
        */
        $caplang = array(
            0 => 'en',
            1 => 'nl',
            2 => 'fr',
            3 => 'de',
            4 => 'pt',
            5 => 'ru',
            6 => 'es',
            7 => 'tr',
            8 => 'it'
            );

        $languages = Language::getLanguages();
        $ctln = count($languages);
        $id = 0;
        for ($i = 0; $i < $ctln; $i++) {
            if ($languages[$i]['id_lang'] == $id_lang) {
                $iso_code = $languages[$i]['iso_code'];
                $id = $i;
            }
        }

        if (!in_array($iso_code, $caplang)) {
            // default
            $iso_code = "en";
            $langs = explode('-', $languages[$id]["language_code"]);
            foreach ($langs as $value) {
                if (in_array($value, $caplang)) {
                    $iso_code = $value;
                }
            }
        }
        return $iso_code;
    }
    public static function info($info, $mypath)
    {
        $output = '<link rel="stylesheet" type="text/css" href="'.$mypath.'views/css/vtip.css" />';
        $output .= '<script type="text/javascript" src="'.$mypath.'views/js/info/vtip.js"></script>';
        $output .= '<img src="'.$mypath.'views/img/info.png" title="'.$info.'" class="vtip" />';
        return $output;
    }
    public static function displayError($error)
    {
        require_once dirname(__FILE__).'/../../../config/config.inc.php';
        $output = '<div id="bgblack" style="background: none repeat scroll 0 0 rgba(0, 0, 0, 0.5);
        position: absolute;left:0;top:0;width:100%;height: 100%;z-index: 1000;"></div>
        <div id="errorblock" style="border:2px solid red; padding:10px; margin:10px; background:#FAE2E3; color:red;
        left:40%; height: 40%;z-index: 1001; position: absolute;">';

        if (is_array($error)) {
            foreach ($error as $err) {
                $output .= '<img src="'.__PS_BASE_URI__.
                    'modules/contactfrm/views/img/unchecked.gif" alt="X" title="" /> '.$err.'<br>';
            }
        } else {
            $output .= '<img src="'.__PS_BASE_URI__.
                'modules/contactfrm/views/img/unchecked.gif" alt="X" title="" /> '.
            $error.'<br>';
        }

        $output .= '</div>';
        return $output;
    }
    public static function sendMailContactfrm($tabfields, $fid, $idshop, $id_lang)
    {
        require_once dirname(__FILE__).'/../../../config/config.inc.php';
        $shop_name = Configuration::get('PS_SHOP_NAME');
        $curr_date = date('dmYHis');
        $layout = Db::getInstance()->ExecuteS(' SELECT * FROM `'._DB_PREFIX_.'contactform` c
            LEFT JOIN `'._DB_PREFIX_.'contactform_lang` cl ON (cl.fid=c.fid)
            WHERE c.`fid`='.(int)$fid.' AND cl.id_lang='.(int)$id_lang);
        $defaultlayoutseller = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
            "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>'.
                    Translate::getModuleTranslation(
                        'contactfrm',
                        'Message from your shop',
                        'contactfrm'
                    ).' {shop_name}</title>
            </head>
            <body>
                <table style="font-family:Verdana,sans-serif; font-size:11px; color:#374953; width: 550px;">
                    <tr>
                        <td align="left">
                            <a href="{shop_url}" title="{shop_name}">{shop_logo}</a>
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td align="left" style="background-color:#DB3484; color:#FFF; font-size: 12px; font-weight:bold;
                        padding: 0.5em 1em;">{contactfrm_in}  {form_name}</td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td>
                        {here_msg} :</br>
                            {message}
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td align="center" style="font-size:10px; border-top: 1px solid #D9DADE;">
                            <a href="{shop_url}" style="color:#DB3484; font-weight:bold; text-decoration:none;">
                                {shop_name}
                            </a>
                            powered with 
                            <a href="'._PS_BASE_URL_.'" style="text-decoration:none; color:#374953;">ContactForm</a>
                        </td>
                    </tr>
                </table>
            </body>
            </html>';
        $customermail = array();
        $msgform = '';
        $logomail = (Configuration::get('PS_LOGO_MAIL', null, null, $idshop)) ?
        Configuration::get('PS_LOGO_MAIL', null, null, $idshop) : 'logo.jpg';
        //Retrieve all parameters to send by mail
        $cttabf = count($tabfields['name']);
        for ($i = 0; $i < $cttabf; $i++) {
            if ($tabfields['fields_valid'][$i] == 'email' || $tabfields['fields_type'][$i] == 'email') {
                array_push($customermail, $tabfields['value'][$i]);
            }
        }
        $cttblb = count($tabfields['label']);
        for ($i = 0; $i < $cttblb; $i++) {
            if (strpos($tabfields['name'][$i], 're-') === false
                && $tabfields['fields_type'][$i] != 'separator'
                && $tabfields['fields_type'][$i] != 'submitbtn'
                && $tabfields['fields_type'][$i] != 'simpletext'
                && $tabfields['fields_type'][$i] != 'button'
                && $tabfields['fields_type'][$i] != 'imagebtn'
                && $tabfields['fields_type'][$i] != 'resetbtn'
                && !empty($tabfields['value'][$i])
                && $tabfields['fields_type'][$i] != 'captcha') {
                if ($tabfields['fields_type'][$i] == 'checkbox') {
                    $nbofck = count($tabfields['value'][$i]);
                    $msgform .= '<b>'.$tabfields['label'][$i].'</b> : ';
                    $msgform .= '<ul>';
                    for ($k = 0; $k < $nbofck; $k++) {
                        $msgform .= '<li>'.$tabfields['value'][$i][$k].'</li>';
                    }
                    $msgform .= '</ul><br>';
                } elseif ($tabfields['fields_type'][$i] == 'textarea') {
                    $msgform .= '<b>'.$tabfields['label'][$i].'</b> : <br>'.nl2br($tabfields['value'][$i]).'<br><br>';
                } else {
                    $msgform .= '<b>'.$tabfields['label'][$i].'</b> : '.nl2br($tabfields['value'][$i]).'<br><br>';
                }
            }
        }
        //Purge $msg_seller;
        if (preg_match('#http://#', $_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = 'http://'.$_SERVER['HTTP_HOST'];
        }
        $shoplogo = '<img alt="'.Configuration::get('PS_SHOP_NAME').'" src="'.$host.
            __PS_BASE_URI__.'img/'.$logomail.'" style="border:none;" />';
        if ($layout[0]['layout'] == '' || empty($layout[0]['layout'])) {
            $layoutseller = $defaultlayoutseller;
        } else {
            $layoutseller = '<html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>'.
                        Translate::getModuleTranslation(
                            'contactfrm',
                            'Message from your shop',
                            'contactfrm'
                        ).' {shop_name}</title>
                </head>
                <body>';
            $layoutseller .= $layout[0]['layout'];
        }

        $layoutseller = str_replace('{shop_name}', $shop_name, $layoutseller);
        $layoutseller = str_replace('{message}', $msgform, $layoutseller);
        $layoutseller = str_replace('{form_name}', $layout[0]['formname'], $layoutseller);
        $layoutseller = str_replace(
            '{message_from}',
            Translate::getModuleTranslation(
                'contactfrm',
                'Message from',
                'contactfrm'
            ),
            $layoutseller
        );
        $layoutseller = str_replace(
            '{contactfrm_in}',
            Translate::getModuleTranslation(
                'contactfrm',
                'CONTACT ON YOUR FORM',
                'contactfrm'
            ),
            $layoutseller
        );
        $layoutseller = str_replace(
            '{intro}',
            Translate::getModuleTranslation(
                'contactfrm',
                'A visitor has sent a message from your form',
                'contactfrm'
            ),
            $layoutseller
        );
        $layoutseller = str_replace('{shop_logo}', $shoplogo, $layoutseller);
        $layoutseller = str_replace(
            '{copyright}',
            Translate::getModuleTranslation(
                'contactfrm',
                'Mail generated by',
                'contactfrm'
            ),
            $layoutseller
        );
        $layoutseller = str_replace('{shop_url}', $host, $layoutseller);
        $layoutseller = str_replace(
            '{here_msg}',
            Translate::getModuleTranslation(
                'contactfrm',
                'Here is the message sent',
                'contactfrm'
            ),
            $layoutseller
        );
        if ($layout[0]['layout']) {
            $layoutseller .= '</body></html>';
        }

        $template_vars = array('{message}' => $msgform, '{form_name}' => $layout[0]['formname']);
        $mail_seller = $layout[0]['email'];
        $allmails = explode(';', $mail_seller);
        $mail_gen = $allmails[0];
        $i = 0;
        $bcc = array();
        // $mail_gen = array();
        foreach ($allmails as $allmails2) {
            if ($i != 0) {
                $bcc[] = $allmails2;
            }
            $i++;
            // $mail_gen[] = $allmails2;
        }
        $template_name = 'cf_mail'; //Specify the template file name
        $title = ($layout[0]['subject'] != '' ? $layout[0]['subject'] :
        Translate::getModuleTranslation(
            'contactfrm',
            'CONTACT FORM ON',
            'contactfrm'
        ).' '.$shop_name);
        $to_name = Translate::getModuleTranslation(
            'contactfrm',
            'Customer',
            'contactfrm'
        );   //Sender's email
        $from_name = Configuration::get('PS_SHOP_NAME'); //Sender's name
        $mail_dir = _PS_MODULE_DIR_.'contactfrm/mails/'; //Directory with message templates
        $from = Configuration::get('PS_SHOP_EMAIL');
        $file_attachment = array();
        //Attachement
        $attach = 0;
        $ctbn = count($tabfields['name']);
        for ($i = 0; $i < $ctbn; $i++) {
            if ($tabfields['fields_type'][$i] == 'fileup' && $tabfields['value'][$i] != '') {
                $attach++;
                $files_params = explode('+', $tabfields['value'][$i]);
                $type = $files_params[1];
                if ($type == 'application/x-download') {
                    $type = 'application/pdf';
                }
                $filetocopy = _PS_MODULE_DIR_.'contactfrm/upload/'.date('Y').'_'.
                    date('m').'_'.date('d').'_'.$fid.'_'.$files_params[2];
                $file_attachment[$i] = Tools::fileAttachment($tabfields['name'][$i]);
                rename($files_params[0], $filetocopy);
            }
            //Test if send copy to customer
            if ((int)Configuration::get('CONTACTFRM_MAILTYPE') == 1) {
                if ($tabfields['fields_valid'][$i] == 'email' || $tabfields['fields_type'][$i] == 'email') {
                    $pos  = strrpos($tabfields['name'][$i], 're_');
                    if ($pos === false) {
                        array_push($bcc, $tabfields['value'][$i]);
                    }
                }
            }
        }
        if ($attach == 0) {
            $file_attachment = null;
        }
        $curr_file_seller = '';
        if ($layout[0]['pdf'] == 1) {
            if (((version_compare(_PS_VERSION_, '1.6', '>=')) === true)
                && ((version_compare(_PS_VERSION_, '1.7', '<')) === true)) {
                require_once(_PS_TCPDF_PATH_.'tcpdf.php');
            }
            if ((version_compare(_PS_VERSION_, '1.7', '>=')) === true) {
                require_once(_PS_ROOT_DIR_.'/vendor/tecnickcom/tcpdf/tcpdf.php');
            }
            
            $pdf = new TCPDF;
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->setPageUnit('px');
            $pdf->SetMargins(30, 30);
            $pdf->setFooterMargin(30);
            $pdf->SetAuthor($shop_name);
            $pdf->SetSubject('');
            $pdf->SetKeywords('');
            $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->AddPage();
            $pdf->writeHTML($layoutseller, true, false, true, false, '');
            $curr_file_seller = _PS_MODULE_DIR_.'contactfrm/'.$layout[0]['formname'].'_'.$curr_date.'.pdf';
            $pdf->Output($curr_file_seller, 'F');
            $file_attachment[] = array(
                'content' =>  Tools::file_get_contents($curr_file_seller),
                'name' => $layout[0]['formname'].'_'.$curr_date.'.pdf',
                'mime' => 'application/pdf'
            );
        }
        $send = Mail::Send(
            (int)$id_lang,
            $template_name,
            $title,
            $template_vars,
            $mail_gen,
            $to_name,
            $from,
            $from_name,
            $file_attachment,
            null,
            $mail_dir,
            false,
            $idshop,
            $bcc,
            null
        );
        if ($send && ($curr_file_seller && file_exists($curr_file_seller))) {
            unlink($curr_file_seller);
        }

        self::upDataInfo(
            $tabfields,
            $fid,
            ($send ? 'mail' : 'notmail')
        );
        $defaultlayoutcustomer = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
                    "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>{notification} {shop_name}</title>
            </head>
            <body>
                <table style="font-family:Verdana,sans-serif; font-size:11px; color:#374953; width: 550px;">
                    <tr>
                        <td align="left">
                            <a href="{shop_url}" title="{shop_name}">{shop_logo}</a>
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td align="left" style="background-color:#DB3484; color:#FFF;
                        font-size: 12px; font-weight:bold; padding: 0.5em 1em;">'.
                        Translate::getModuleTranslation(
                            'contactfrm',
                            'Notification message from.',
                            'contactfrm'
                        ).' {shop_name}</td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td>
                            {message}
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td align="center" style="font-size:10px; border-top: 1px solid #D9DADE;">
                            <a href="{shop_url}" style="color:#DB3484; font-weight:bold; text-decoration:none;">
                                {shop_name}
                            </a>
                            powered with <a href="'._PS_BASE_URL_.
                            '" style="text-decoration:none; color:#374953;">ContactForm</a>
                        </td>
                    </tr>
                </table>
            </body>
            </html>';
        if ($layout[0]['clayout'] == '' || empty($layout[0]['clayout'])) {
            $layoutcustomer = $defaultlayoutcustomer;
        } else {
            $layoutcustomer = $layout[0]['clayout'];
        }
        $layoutcustomer = str_replace('{shop_name}', $shop_name, $layoutcustomer);
        $layoutcustomer = str_replace(
            '{message}',
            Tools::stripslashes($layout[0]['automailresponse']),
            $layoutcustomer
        );
        $layoutcustomer = str_replace('{shop_logo}', $shoplogo, $layoutcustomer);
        $layoutcustomer = str_replace(
            '{copyright}',
            Translate::getModuleTranslation(
                'contactfrm',
                'Mail generated by',
                'contactfrm'
            ),
            $layoutcustomer
        );
        $layoutcustomer = str_replace(
            '{notification}',
            Translate::getModuleTranslation(
                'contactfrm',
                'Notification message from',
                'contactfrm'
            ),
            $layoutcustomer
        );
        $layoutcustomer = str_replace('{shop_url}', $host, $layoutcustomer);
        $layoutcustomer = str_replace('{message_from}', '', $layoutcustomer);
        $layoutcustomer = str_replace('{contactfrm_in}', '', $layoutcustomer);
        $layoutcustomer = str_replace('{here_msg}', '', $layoutcustomer);
        $layoutcustomer = str_replace('{form_name}', '', $layoutcustomer);
        $layoutcustomer = str_replace('{messageform}', $msgform, $layoutcustomer);
        /* =================== NOTIFICATION MAIL CUSTOMER ============================= */
        if ((int)Configuration::get('CONTACTFRM_NOTIF') == 1) {
            $notif_file_attachment = array();
            $curr_file_customer2 = '';
            if ($layout[0]['notif_pdf'] == 1) {
                if (((version_compare(_PS_VERSION_, '1.6', '>=')) === true)
                    && ((version_compare(_PS_VERSION_, '1.7', '<')) === true)) {
                    require_once(_PS_TCPDF_PATH_.'tcpdf.php');
                }
                if ((version_compare(_PS_VERSION_, '1.7', '>=')) === true) {
                    require_once(_PS_ROOT_DIR_.'/vendor/tecnickcom/tcpdf/tcpdf.php');
                }
                $pdf2 = new TCPDF;
                $pdf2->SetCreator(PDF_CREATOR);
                $pdf2->setPageUnit('px');
                $pdf2->SetMargins(30, 30);
                $pdf2->setFooterMargin(30);
                $pdf2->SetAuthor($shop_name);
                $pdf2->SetSubject('');
                $pdf2->SetKeywords('');
                $pdf2->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                $pdf2->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
                $pdf2->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
                $pdf2->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf2->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
                $pdf2->setImageScale(PDF_IMAGE_SCALE_RATIO);
                $pdf2->AddPage();
                $pdf2->writeHTML($layoutcustomer, true, false, true, false, '');

                $curr_file_customer2 = _PS_MODULE_DIR_.'contactfrm/'.$shop_name.'_'.$curr_date.'.pdf';
                $pdf->Output($curr_file_customer2, 'F');
                $notif_file_attachment[] = array(
                'content' => Tools::file_get_contents($curr_file_customer2),
                'name' => $shop_name.'_'.$curr_date.'.pdf',
                'mime' => 'application/pdf'
                );
            }
            $allcustmail = array();
            $ctbna = count($tabfields['name']);
            for ($i = 0; $i < $ctbna; $i++) {
                if ($tabfields['fields_valid'][$i] == 'email' || $tabfields['fields_type'][$i] == 'email') {
                    $pos  = strrpos($tabfields['name'][$i], 're_');
                    if ($pos === false) {
                        array_push($allcustmail, $tabfields['value'][$i]);
                    }
                }
            }
            if (count($allcustmail) > 0) {
                $template_vars = array(
                    '{message}' => $layout[0]['automailresponse'],
                    '{form_name}' => Tools::stripslashes($layout[0]['formname'])
                );
                $template_name = 'cf_notif';
                $title = $layout[0]['subject_notif'];
                $from = Configuration::get('PS_SHOP_EMAIL');
                $from_name = Configuration::get('PS_SHOP_NAME');
                $mail_dir = _PS_MODULE_DIR_.'contactfrm/mails/';
                $to_name = Translate::getModuleTranslation('contactfrm', 'Customer', 'contactfrm');
                $send_notif = Mail::Send(
                    (int)$id_lang,
                    $template_name,
                    $title,
                    $template_vars,
                    $allcustmail,
                    $to_name,
                    $from,
                    $from_name,
                    $notif_file_attachment,
                    null,
                    $mail_dir,
                    false,
                    null,
                    null,
                    null
                );
            }
            if ($send_notif && ($curr_file_customer2 && file_exists($curr_file_customer2))) {
                unlink($curr_file_customer2);
            }
        }
        /* ========================== END NOTIFICATION MAIL TO THE CUSTOME ===================================== */
        if ($send) {
            $linkredirect = 'index.php';
            if ((int)$layout[0]['idcms']) {
                $array_idcms = array( 0 => $layout[0]['idcms']);
                $linkcms = CMS::getLinks($id_lang, $array_idcms);
                $linkredirect = $linkcms[0]['link'];
            }
            Tools::redirect($linkredirect);
        } else {
            $error = array();
            $error[] = Translate::getModuleTranslation(
                'contactfrm',
                'Errors occurred when sending mail. Please contact the',
                'contactfrm'
            )
            .' <a class="link" href="mailto:'.
            Configuration::get('PS_SHOP_EMAIL').'">'.
            Translate::getModuleTranslation('contactfrm', 'the administrator', 'contactfrm').'</a> '.
            Translate::getModuleTranslation('contactfrm', 'of the site or try later', 'contactfrm');
            echo self::displayError($error);
        }
    }

    public static function upDataInfo($tabfields, $fid, $statut)
    {
        require_once dirname(__FILE__).'/../../../config/config.inc.php';
        $ip = self::getIp();
        $params = Db::getInstance()->ExecuteS(' SELECT * FROM `'._DB_PREFIX_.'contactform` WHERE `fid`='.(int)$fid);
        $mail_seller = $params[0]['email'];
        $cttabl = count($tabfields['name']);
        for ($i = 0; $i < $cttabl; $i++) {
            if ($tabfields['fields_valid'][$i] == 'email' || $tabfields['fields_type'][$i] == 'email') {
                $mail_customer = $tabfields['value'][$i];
            }
        }
        $msg_seller = '';
        $tabf = count($tabfields['label']);
        for ($i = 0; $i < $tabf; $i++) {
            if (strpos($tabfields['name'][$i], 're-') === false) {
                if ($tabfields['fields_type'][$i] == 'checkbox') {
                    $nbofck = count($tabfields['value'][$i]);
                    $msg_seller .= '<b>'.addslashes($tabfields['label'][$i]).'</b> : ';
                    $msg_seller .= '<ul>';
                    for ($k = 0; $k < $nbofck; $k++) {
                        $msg_seller .= '<li>*'.addslashes($tabfields['value'][$i][$k]).'</li>';
                    }
                    $msg_seller .= '</ul>';
                } else {
                    if ($tabfields['fields_type'][$i] != 'separator'
                        &&    $tabfields['fields_type'][$i] != 'fileup'
                        && $tabfields['fields_type'][$i] != 'submitbtn'
                        && $tabfields['fields_type'][$i] != 'resetbtn'
                        &&    $tabfields['fields_type'][$i] != 'imagebtn'
                        && $tabfields['fields_type'][$i] != 'button') {
                        $msg_seller .= '<b>'.$tabfields['label'][$i].'</b> : '.addslashes($tabfields['value'][$i]).
                        '<br>';
                    }
                }
            }
        }

        Db::getInstance()->insert('contactform_data', array(
        'ip'=>$ip,
        'date'=>date('m/d/Y').'-'.date('H:i'),
        'toemail'=>$mail_seller,
        'foremail'=>(empty($mail_customer) ? '' : $mail_customer),
        'info'=>$msg_seller,
        'statut_mail'=>$statut,
        'comment'=>time(),        'fid'=>(int)$fid
        ));
    }

    public static function greeckToHtml($msg_seller)
    {
        $msg_seller = str_replace('Á', '&#193;', $msg_seller);
        $msg_seller = str_replace('á', '&#225;', $msg_seller);
        $msg_seller = str_replace('Ą', '&#260;', $msg_seller);
        $msg_seller = str_replace('ą', '&#261;', $msg_seller);
        $msg_seller = str_replace('Ä', '&#196;', $msg_seller);
        $msg_seller = str_replace('ä', '&#228;', $msg_seller);
        $msg_seller = str_replace('É', '&#201;', $msg_seller);
        $msg_seller = str_replace('Ę', '&#280;', $msg_seller);
        $msg_seller = str_replace('ę', '&#281;', $msg_seller);
        $msg_seller = str_replace('Ě', '&#282;', $msg_seller);
        $msg_seller = str_replace('ě', '&#283;', $msg_seller);
        $msg_seller = str_replace('Í', '&#205;', $msg_seller);
        $msg_seller = str_replace('í', '&#237;', $msg_seller);
        $msg_seller = str_replace('Ó', '&#211;', $msg_seller);
        $msg_seller = str_replace('ó', '&#243;', $msg_seller);
        $msg_seller = str_replace('Ô', '&#212;', $msg_seller);
        $msg_seller = str_replace('ô', '&#244;', $msg_seller);
        $msg_seller = str_replace('Ú', '&#218;', $msg_seller);
        $msg_seller = str_replace('ú', '&#250;', $msg_seller);
        $msg_seller = str_replace('Ů', '&#366;', $msg_seller);
        $msg_seller = str_replace('ů', '&#367;', $msg_seller);
        $msg_seller = str_replace('Ý', '&#221;', $msg_seller);
        $msg_seller = str_replace('ý', '&#253;', $msg_seller);
        $msg_seller = str_replace('Č', '&#268;', $msg_seller);
        $msg_seller = str_replace('č', '&#269;', $msg_seller);
        $msg_seller = str_replace('Ď', '&#270;', $msg_seller);
        $msg_seller = str_replace('ď', '&#271;', $msg_seller);
        $msg_seller = str_replace('ť', '&#357;', $msg_seller);
        $msg_seller = str_replace('Ĺ', '&#313;', $msg_seller);
        $msg_seller = str_replace('ĺ', '&#314;', $msg_seller);
        $msg_seller = str_replace('Ň', '&#327;', $msg_seller);
        $msg_seller = str_replace('ň', '&#328;', $msg_seller);
        $msg_seller = str_replace('Ŕ', '&#340;', $msg_seller);
        $msg_seller = str_replace('ŕ', '&#341;', $msg_seller);
        $msg_seller = str_replace('Ř', '&#344;', $msg_seller);
        $msg_seller = str_replace('ř', '&#345;', $msg_seller);
        $msg_seller = str_replace('Š', '&#352;', $msg_seller);
        $msg_seller = str_replace('š', '&#353;', $msg_seller);
        $msg_seller = str_replace('Ž', '&#381;', $msg_seller);
        $msg_seller = str_replace('ž', '&#382;', $msg_seller);
        $msg_seller = str_replace('Ľ', '&#317;', $msg_seller);
        $msg_seller = str_replace('Ľ', '&#317;', $msg_seller);
        $msg_seller = str_replace('ľ', '&#318;', $msg_seller);
        $msg_seller = str_replace('Ť', '&#356;', $msg_seller);

        $msg_seller = str_replace('à', '&agrave;', $msg_seller);
        $msg_seller = str_replace('é', '&eacute;', $msg_seller);
        $msg_seller = str_replace('ê', '&ecirc;', $msg_seller);
        $msg_seller = str_replace('è', '&egrave;', $msg_seller);
        $msg_seller = str_replace('ç', '&ccedil;', $msg_seller);
        $msg_seller = str_replace('ù', '&ugrave;', $msg_seller);
        $msg_seller = str_replace('î', '&icirc;', $msg_seller);

        $msg_seller = str_replace('Γ', '&Gamma;', $msg_seller);
        $msg_seller = str_replace('Δ', '&Delta;', $msg_seller);
        $msg_seller = str_replace('Θ', '&Theta;', $msg_seller);
        $msg_seller = str_replace('Λ', '&Lambda;', $msg_seller);
        $msg_seller = str_replace('Ξ', '&Xi;', $msg_seller);
        $msg_seller = str_replace('Π', '&Pi;', $msg_seller);
        $msg_seller = str_replace('Σ', '&Sigma;', $msg_seller);
        $msg_seller = str_replace('Υ', '&Upsilon;', $msg_seller);
        $msg_seller = str_replace('Φ', '&Phi;', $msg_seller);
        $msg_seller = str_replace('Ψ', '&Psi;', $msg_seller);
        $msg_seller = str_replace('Ω', '&Omega;', $msg_seller);
        $msg_seller = str_replace('α', '&alpha;', $msg_seller);
        $msg_seller = str_replace('β', '&beta;', $msg_seller);
        $msg_seller = str_replace('γ', '&gamma;', $msg_seller);
        $msg_seller = str_replace('δ', '&delta;', $msg_seller);
        $msg_seller = str_replace('ε', '&epsilon;', $msg_seller);
        $msg_seller = str_replace('ζ', '&zeta;', $msg_seller);
        $msg_seller = str_replace('η', '&eta;', $msg_seller);
        $msg_seller = str_replace('θ', '&theta;', $msg_seller);
        $msg_seller = str_replace('ι', '&iota;', $msg_seller);
        $msg_seller = str_replace('κ', '&kappa;', $msg_seller);
        $msg_seller = str_replace('λ', '&lambda;', $msg_seller);
        $msg_seller = str_replace('μ', '&mu;', $msg_seller);
        $msg_seller = str_replace('ν', '&nu;', $msg_seller);
        $msg_seller = str_replace('ξ', '&xi;', $msg_seller);
        $msg_seller = str_replace('π', '&pi;', $msg_seller);
        $msg_seller = str_replace('ρ', '&rho;', $msg_seller);
        $msg_seller = str_replace('σ', '&sigma;', $msg_seller);
        $msg_seller = str_replace('τ', '&tau;', $msg_seller);
        $msg_seller = str_replace('υ', '&upsilon;', $msg_seller);
        $msg_seller = str_replace('φ', '&phi;', $msg_seller);
        $msg_seller = str_replace('χ', '&chi;', $msg_seller);
        $msg_seller = str_replace('ψ', '&psi;', $msg_seller);
        $msg_seller = str_replace('ω', '&omega;', $msg_seller);

        $msg_seller = str_replace('Ώ', '&#911;', $msg_seller);
        $msg_seller = str_replace('Ϋ', '&#939;', $msg_seller);
        $msg_seller = str_replace('ή', '&#942;', $msg_seller);
        $msg_seller = str_replace('ή', '&#942;', $msg_seller);
        $msg_seller = str_replace('ϊ', '&#970;', $msg_seller);
        $msg_seller = str_replace('ύ', '&#973;', $msg_seller);
        $msg_seller = str_replace('ΐ', '&#912;', $msg_seller);
        $msg_seller = str_replace('ά', '&#940;', $msg_seller);
        $msg_seller = str_replace('ί', '&#943;', $msg_seller);
        $msg_seller = str_replace('ί', '&#943;', $msg_seller);
        $msg_seller = str_replace('ϋ', '&#971;', $msg_seller);
        $msg_seller = str_replace('ώ', '&#974;', $msg_seller);
        $msg_seller = str_replace('Ϊ', '&#938;', $msg_seller);
        $msg_seller = str_replace('έ', '&#941;', $msg_seller);
        $msg_seller = str_replace('έ', '&#941;', $msg_seller);
        $msg_seller = str_replace('ΰ', '&#944;', $msg_seller);
        $msg_seller = str_replace('ό', '&#972;', $msg_seller);
        return $msg_seller;
    }

    public static function getIp()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function purgeBadcharInfo($chaine)
    {
        $chaine = preg_replace(
            '/[^a-zA-Z0-9\s,\'.@_-������������������]/i',
            '',
            $chaine
        );
        return addslashes($chaine);
    }

    public static function cleanTmp()
    {
        require_once dirname(__FILE__).'/../../../config/config.inc.php';
        $dir = opendir(_PS_MODULE_DIR_.'contactfrm/upload/');
        while ($file = readdir($dir)) {
            $ttf = explode('.', $file);
            if ($file != '.' && $file != '..' && $ttf[1] != 'db' && $file != 'index.php') {
                unlink(_PS_MODULE_DIR_.'contactfrm/upload/'.$file);
            }
        }
    }

    public static function supprAccents($texte)
    {
        $texte = strtr(
            $texte,
            '@ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
            'aAAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy'
        );
        $texte = str_replace(' ', '-', $texte);
        $texte = str_replace("'", '-', $texte);
        return $texte;
    }
}
