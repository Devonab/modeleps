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

class CFutils
{
    public static function exportForm($mypath, $id_lang)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $tables = array();
        $output2 = '';
        $eofl = "\n";
        //Taables used by COntactform
        array_push($tables, _DB_PREFIX_.'contactform');
        array_push($tables, _DB_PREFIX_.'contactform_item');
        array_push($tables, _DB_PREFIX_.'contactform_lang');
        array_push($tables, _DB_PREFIX_.'contactform_item_lang');
        array_push($tables, _DB_PREFIX_.'contactform_relation');
        array_push($tables, _DB_PREFIX_.'contactform_relation_lang');
        array_push($tables, _DB_PREFIX_.'contactform_relation_item');
        array_push($tables, _DB_PREFIX_.'contactform_relation_item_lang');
        //Navigation bar
        $output = '<form method="POST" action="'.$_SERVER['REQUEST_URI'].'">';

        $output .= CFtoolsbar::toolbar('exportform', $mypath, $id_lang);
        //Affichage des résultats
        $output .= '<div class="panel">
        <h3 class="tab"><i class="icon-info">
        </i> '
        .Translate::getModuleTranslation('contactfrm', 'Save your form', 'contactfrm').
        '</h3>
        <p style="margin-bottom:10px">'.
        Translate::getModuleTranslation(
            'contactfrm',
            'Please don not remove the ContaCtfotm tag : -- [CF_tag]',
            'contactfrm'
        ).
        '</p><br><br>';
        $output .= '<p>'.Translate::getModuleTranslation(
            'contactfrm',
            'Note',
            'contactfrm'
        )
        .':<font color="red">'.
        Translate::getModuleTranslation(
            'contactfrm',
            'To restore a database
            from a backup contactform, it is advisable to use the specific 
            contactform restore interface  via "Restore your form" menu.
            If you want to use <b>phpMyAdmin</b>,
            you should firstly clear the bases of existing contactform 
            except "contactfrm_cfg" table, then after
            you can proceed with the restoration.',
            'contactfrm'
        ).
        '</font></p><br>';
        $output .= '<textarea  style="width: 100%; height: 456px;" name="sqldump" cols="50" rows="30" wrap="OFF">';
        $output2 .= $eofl.$eofl;
        $output2 .= $eofl.$eofl;
        $output2 .= 'SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";';
        $output2 .= $eofl.$eofl;
        $output2 .= $eofl.$eofl;
        $output2 .= '-- [CF_tag]';
        $output2 .= $eofl.$eofl;
        $cttab = count($tables);
        for ($i = 0; $i < $cttab; $i++) {
            $output2 .= $eofl.$eofl;
            //Key
            $tableskey = Db::getInstance()->ExecuteS('SHOW KEYS FROM '.$tables[$i]);
            //Fields definition
            $fieldsdef = Db::getInstance()->ExecuteS('SHOW FIELDS FROM '.$tables[$i]);
            $output2 .= 'CREATE TABLE IF NOT EXISTS `'.$tables[$i].'` ('.$eofl;
            $compteur = 0;
            foreach ($fieldsdef as $field) {
                if ($field['Null'] != 'YES') {
                    $null = ' NOT NULL ';
                } else {
                    if ($field['Default'] = 'NULL') {
                        $null = ' default NULL';
                    } else {
                        $null = ' default '.$field['Default'];
                    }
                }
                if ($field['Extra'] != '') {
                    $extra = $field['Extra'];
                } else {
                    $extra = '';
                }
                if ($compteur < count($fieldsdef) - 1) {
                    $output2 .= '`'.$field['Field'].'`  '.$field['Type'].'  '.$null.'  '.$extra.','.$eofl;
                } else {
                    if (!empty($tableskey[0]['Column_name'])) {
                        $output2 .= '`'.$field['Field'].'`  '.$field['Type'].'  '.$null.'  '.$extra.','.$eofl;
                    } else {
                        $output2 .= '`'.$field['Field'].'`  '.$field['Type'].'  '.$null.'  '.$extra.$eofl;
                    }
                }
                $compteur++;
            }
            if (!empty($tableskey[0]['Column_name'])) {
                $output2 .= 'PRIMARY KEY  (`'.$tableskey[0]['Column_name'].'`)'.$eofl;
            }

            $output2 .= ') ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1;';
            $output2 .= $eofl;

            $output2 .= $eofl.$eofl;
            $output2 .= $eofl.$eofl;
            //INSERTION
            $tablecontents = Db::getInstance()->ExecuteS('SELECT *  FROM '.$tables[$i]);
            if (count($tablecontents) > 0) {
                $output2 .= '-- [CF_tag]';
                $compteur = 0;
                $comma = ',';
                $output2 .= 'INSERT INTO `'.$tables[$i].'` (';

                foreach ($fieldsdef as $field) {
                    if ($compteur == count($fieldsdef) - 1) {
                        $comma = ' ';
                    }
                    $output2 .= ' `'.$field['Field'].'` '.$comma.' ';
                    $compteur++;
                }
                $output2 .= ') VALUES'.$eofl;
                //Lists ou values
                $pcomma = ',';
                $pcompteur = 0;
                $comma = ',';
                $compteur = 0;
                foreach ($tablecontents as $tablecontent) {
                    if ($pcompteur == count($tablecontents) - 1) {
                        $pcomma = ';';
                    }

                    $output2 .= '(';
                    foreach ($fieldsdef as $field) {
                        if ($compteur == count($fieldsdef) - 1) {
                            $comma = '';
                        }
                        $apost = str_replace("'", '´', $tablecontent[$field['Field']]);
                        $output2 .= '\''.$apost.'\''.$comma;
                        $compteur++;
                    }
                    $output2 .= ')';
                    $output2 .= $pcomma;
                    $output2 .= $eofl;
                    $comma = ',';
                    $compteur = 0;
                    $pcompteur++;
                }
            }//end of if
            //END OF INSERTION
            $output2 .= $eofl;
            $output2 .= '-- [CF_tag]';
            $output2 .= $eofl.$eofl;
        }
        $output .= $output2.'</textarea>';
        $output .= '</div></form>';
        //Write backup file: Alternative
        //Change file permission
        chmod(dirname(__FILE__).'/../library/sql/contactfrm.sql.txt', 0666);
        $fp2 = fopen(dirname(__FILE__).'/../library/sql/contactfrm.sql.txt', 'w');
        fputs($fp2, $output2);
        fclose($fp2);
        return $output;
    }

    public static function importForm($mypath, $id_lang)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $output =    '<form class="defaultForm form-horizontal" method="POST" 
        action="'.$_SERVER['REQUEST_URI'].'" enctype="multipart/form-data">';
        $output .= CFtoolsbar::toolbar('restoreform', $mypath, $id_lang);
        $output .=    '<div class="panel"><h3 class="tab"> <i class="icon-info"></i> '.
        Translate::getModuleTranslation('contactfrm', 'Import data', 'contactfrm').'</h3>';
        $output .= '<div class="form-group">
        <div class="col-lg-3"><input  onclick="return(confirm(\''.
        Translate::getModuleTranslation(
            'contactfrm',
            'Caution, this will clear your forms and replace them with those in your backup',
            'contactfrm'
        ).
        '\'));" type="file" name="txtimportsql" size="30" class="file">
        <div class="input-group col-xs-12">
            <span class="input-group-btn">
                <button class="form-control browse btn btn-default" type="button">'.
                Translate::getModuleTranslation('contactfrm', 'Browse', 'contactfrm').'</button>
            </span>
            <input type="text" class="form-control " disabled="" 
            placeholder="'.Translate::getModuleTranslation('contactfrm', 'Upload file', 'contactfrm').'" style="">
        </div>
        </div>
        <div class="col-lg-2">
        <input class="button btn btn-primary" type="submit" name="subimportsql"
        value="'.Translate::getModuleTranslation('contactfrm', 'Import', 'contactfrm').'">
        </div>
        </div>';
        $output .= '</div>';
        $output .= '</form>';
        $output .= '<style type="text/css">
                        .file {
                          visibility: hidden;
                          position: absolute;
                        }
                        button.browse.btn.btn-default {
                            line-height: 18px !important;
                        }
                    </style>';
        $output .= '
<script type="text/javascript">
var utils_import1 = setInterval(function(){
if (typeof($) != "undefined") {
    $(document).on(\'click\', \'.browse\', function(){
        var file = $(this).parent().parent().parent().find(\'.file\');
        file.trigger(\'click\');
    });
    $(document).on(\'change\', \'.file\', function(){
        var fullPath = $(this).val();
        if (fullPath) {
            if (fullPath.indexOf(\'\\\\\') >= 0) {
                var startIndex = fullPath.lastIndexOf(\'\\\\\');
            } else {
                var startIndex = fullPath.lastIndexOf(\'/\');
            }
            var filename = fullPath.substring(startIndex);
            if (filename.indexOf(\'\\\\\') === 0 || filename.indexOf(\'/\') === 0) {
                filename = filename.substring(1);
            }
        }
      $(this).parent().find(\'.form-control\').val(filename);
    });
    clearInterval(utils_import1);
    }
}, 500);
</script>'
        ;

        return $output;
    }
    public static function saveAs($filename, $outputname)
    {
        header('Content-disposition: attachment; filename='.$outputname);
        header('Content-Type: application/force-download');
        header('Content-Transfer-Encoding: binary');
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        ob_clean();
        flush();
        readfile($filename);
        exit;
    }
    public static function truncateAllTable()
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'contactform`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'contactform_item`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'contactform_lang`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'contactform_item_lang`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'contactform_relation`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'contactform_relation_lang`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'contactform_relation_item`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'contactform_relation_item_lang`');
    }
    public static function settings($mypath, $id_lang)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $mytoken = Tools::getValue('token');
        $url = 'index.php?tab=AdminModules&configure=contactfrm&token='.$mytoken;
        $output = CFtoolsbar::toolbar('settings', $mypath, $id_lang);
        $output .= self::useSlider($mypath);
        $output .= '<link rel="stylesheet" type="text/css" href="'.$mypath.'views/css/tabs.css" />';
        $output .= '
            <script>
                var utils1 = setInterval(function(){ 
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
                                var activeTab = $(this).find("a").attr("href");
                                //Find the href attribute value to identify the active tab + content
                                $(activeTab).fadeIn(); //Fade in the active ID content
                                return false;
                            });
                        });

                        clearInterval(utils1);
                    }
                }, 500);

            </script>';
    
        $output .= '<div class="bootstrap">
        <form class="form-horizontal" name="objForm" method="post" 
        action="'.$_SERVER['REQUEST_URI'].'" enctype="multipart/form-data">';
        $output .= '<div class="pspage panel"> <h3 class="tab"><i class="icon-info"></i> '.
        Translate::getModuleTranslation('contactfrm', 'Settings', 'contactfrm').'</h3>';
        $output .= '
            <ul class="tabs">
            <li><a href="#tab1">'.
                Translate::getModuleTranslation('contactfrm', 'General settings', 'contactfrm')
            .'</a></li>
            <li><a href="#tab1-a">'.
                Translate::getModuleTranslation('contactfrm', 'Mail', 'contactfrm')
            .'</a></li>
            <li><a href="#tab2">'.
                Translate::getModuleTranslation('contactfrm', 'Form settings', 'contactfrm')
            .'</a></li>
            <li><a href="#tab3">'.
                Translate::getModuleTranslation('contactfrm', 'Captcha settings', 'contactfrm')
            .'</a></li>
        </ul>';
        /* ========================================= TAB 1 ====================================== */
        $output .= '<div id="tab1" class="tab_content">';
            $output .= '<div class="form-group">
                <label class="control-label col-lg-3">
                    <span data-original-title="'.
                        Translate::getModuleTranslation(
                            'contactfrm',
                            'If yes, User must be logged in to submit form',
                            'contactfrm'
                        )
                    .'" class="label-tooltip" data-toggle="tooltip" title="">
                    '.Translate::getModuleTranslation('contactfrm', 'Login required', 'contactfrm').'
                    </span>
                </label>
                <div class="col-lg-9">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input '.(Configuration::get('CONTACTFRM_AUTH') == 1 ? 'checked="checked"' : '' )
                        .' name="cfauth" id="cfauth_on" type="radio" value="1">
                        <label class="radioCheck" for="cfauth_on">'.
                        Translate::getModuleTranslation('contactfrm', 'Yes', 'contactfrm').'
                        </label>
                        <input '.(Configuration::get('CONTACTFRM_AUTH') == 0 ? 'checked="checked"' : '' )
                        .' name="cfauth" id="cfauth_off" type="radio" value="0">
                        <label class="radioCheck" for="cfauth_off">
                            '.Translate::getModuleTranslation('contactfrm', 'No', 'contactfrm').'
                        </label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>';
        $output .= '<div class="form-group">
                        <label for="cfgrequired" class="control-label col-lg-3">'.
                            Translate::getModuleTranslation(
                                'contactfrm',
                                'Character displayed after required fields',
                                'contactfrm'
                            )
                        .'</label>
                        <div class="col-lg-3">
                            <input size="6" type="text" name="cfgrequired" id="cfgrequired"
                            value="'.Configuration::get('CONTACTFRM_REQUIRED').'" >
                        </div>
                    </div>';
        $output .= '<div class="form-group">
                        <label for="cfgupload" class="control-label col-lg-3">
                            <span data-original-title="'.
                                Translate::getModuleTranslation(
                                    'contactfrm',
                                    'Separate format with ","',
                                    'contactfrm'
                                )
                            .'" class="label-tooltip" data-toggle="tooltip" title="">'.
                                Translate::getModuleTranslation(
                                    'contactfrm',
                                    'Accepted file format for upload',
                                    'contactfrm'
                                )
                            .'</span>
                        </label>
                        <div class="col-lg-3">
                            <input type="text" size=60 name="cfgupload" 
                            id="cfgupload" value="'.Configuration::get('CONTACTFRM_UPFORMAT').'">
                        </div>
                    </div>';
        $listforms = Db::getInstance()->ExecuteS('SELECT cf.`formname`, cfl.`fid`
                                                 FROM `'._DB_PREFIX_.'contactform` cf 
                                                 LEFT JOIN `'._DB_PREFIX_.'contactform_lang` cfl  
                                                 ON cf.`fid` = cfl.`fid`
                                                 WHERE cfl.`id_lang`='.(int)$id_lang);
        $output .= '<div class="form-group">
                        <label for="cfgupload" class="control-label col-lg-3">
                            <span data-original-title="'.
                                Translate::getModuleTranslation(
                                    'contactfrm',
                                    'This option is valid only if you have chosen to ContactForm 
                                    default contact form instead of that of PrestaShop.',
                                    'contactfrm'
                                )
                            .'" class="label-tooltip" data-toggle="tooltip" title="">'.
                                Translate::getModuleTranslation(
                                    'contactfrm',
                                    'Default form',
                                    'contactfrm'
                                )
                            .'</span>
                        </label>
                        <div class="col-lg-3">
                            <select name="defaultform">';
                            $output .= '<option value="0">'.
                                Translate::getModuleTranslation(
                                    'contactfrm',
                                    'None',
                                    'contactfrm'
                                )
                            .'</option>';
        foreach ($listforms as $listform) {
            $selected = ((int)Configuration::get('CONTACTFRM_DEFAULTFORM') == (int)$listform['fid'])
                        ? 'selected="selected"' : '';
            $output .= '<option '.$selected.' value="'.$listform['fid'].'">'.$listform['formname'].'</option>';
        }
        $output .= '
                            
                            </select>
                        </div>
                    </div>';
        $output .= '<div class="form-group">
                        <label for="cfgupload" class="control-label col-lg-3">'.
                            Translate::getModuleTranslation(
                                'contactfrm',
                                'Activation bouton',
                                'contactfrm'
                            )
                        .'</label>
                        <div class="col-lg-3">';
        $output .= '<table class="homepage1" border="1">';
        $output .= '<tr align="center">';
        if (Configuration::get('CONTACTFRM_ACTIVE') == 0) {
            $output .= '<td ><a href="'.$url.'&task=btnActivecf&mode=1">
            <img src="'.$mypath.'views/img/activate-grey.png">
            <br>'.
                Translate::getModuleTranslation(
                    'contactfrm',
                    'Enable ContactForm activation button',
                    'contactfrm'
                )
            .'</a></td>';
        } else {
            $output .= '<td ><a href="'.$url.'&task=btnActivecf&mode=0"><img src="'.$mypath.'views/img/activate.png">
            <br>'.
                Translate::getModuleTranslation(
                    'contactfrm',
                    'Disable Contactform activation button',
                    'contactfrm'
                )
            .'</a></td>';
        }
    
        if (Configuration::get('CONTACTFRM_DEACTIVE') == 0) {
            $output .= '<td><a href="'.$url.'&task=btnDeactivecf&mode=1">
            <img src="'.$mypath.'views/img/diasable-grey.png">
            <br>'.
                Translate::getModuleTranslation(
                    'contactfrm',
                    'Enable Restoring Prestashop form',
                    'contactfrm'
                )
            .'</a></td>';
        } else {
            $output .= '<td><a href="'.$url.'&task=btnDeactivecf&mode=0">
            <img src="'.$mypath.'views/img/diasable.png">
            <br>'.
                Translate::getModuleTranslation(
                    'contactfrm',
                    'Disable Restoring Prestashop form button',
                    'contactfrm'
                )
            .'</a></td>';
        }
        $output .= '</tr>';
        $output .= '</table>';
        $output .= '</div></div>';
        $output .= '</div>';
        /* ========================================= END TAB 1 ====================================== */
        $output .= '<div id="tab1-a" class="tab_content">';
        $output .= '<div class="form-group">
                        <label class="control-label col-lg-3">'.
                            Translate::getModuleTranslation(
                                'contactfrm',
                                'Sent a copy of mail to customers',
                                'contactfrm'
                            )
                        .'</label>
                        <div class="col-lg-9">
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input '.(Configuration::get('CONTACTFRM_MAILTYPE') == 1 ? 'checked="checked"' : '' ).
                                ' name="mailtype" id="mailtype_on" type="radio" value="1">
                                <label class="radioCheck" for="mailtype_on">'.
                                Translate::getModuleTranslation('contactfrm', 'Yes', 'contactfrm').'
                                </label>
                                <input '.(Configuration::get('CONTACTFRM_MAILTYPE') == 0 ? 'checked="checked"' : '' ).
                                ' name="mailtype" id="mailtype_off" type="radio" value="0">
                                <label class="radioCheck" for="mailtype_off">'.
                                    Translate::getModuleTranslation('contactfrm', 'No', 'contactfrm').'
                                </label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>
        </div>';
        $output .= '<div class="form-group">
                        <label class="control-label col-lg-3">'.
                            Translate::getModuleTranslation(
                                'contactfrm',
                                'Active customer notification',
                                'contactfrm'
                            )
                       .'</label>
                        <div class="col-lg-9">
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input '.(Configuration::get('CONTACTFRM_NOTIF') == 1 ? 'checked="checked"' : '' ).'
                                name="notif" id="notif_on" type="radio" value="1">
                                <label class="radioCheck" for="notif_on">'.
                                Translate::getModuleTranslation('contactfrm', 'Yes', 'contactfrm').'
                                </label>
                                <input '.(Configuration::get('CONTACTFRM_NOTIF') == 0 ? 'checked="checked"' : '' ).'
                                name="notif" id="notif_off" type="radio" value="0">
                                <label class="radioCheck" for="notif_off">
                                    '.Translate::getModuleTranslation('contactfrm', 'No', 'contactfrm').'
                                </label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>
                </div>';
        $output .= '</div>';
        /* ========================================= TAB 2 ====================================== */
        $output .= '<div id="tab2" class="tab_content">';
        $output .= '<div class="form-group">
                            <label for="cfgrequired" class="control-label col-lg-3">
                        <span data-original-title="'.
                        Translate::getModuleTranslation(
                            'contactfrm',
                            'Place your mouse over the text to see the preview',
                            'contactfrm'
                        )
                        .'" class="label-tooltip" data-toggle="tooltip" title="">
                                '.Translate::getModuleTranslation('contactfrm', 'Form style', 'contactfrm').'
                        </span>
                        </label>
                        <div class="col-lg-4">
                            <ul style="list-style-type:none;padding-left: 0;">
                                <li><input '.(Configuration::get('CONTACTFRM_FORM') == 0 ? 'checked="checked"' : '' ).
                                ' name="cfstyle" type="radio" value="0">'.
                                self::imgpreview(
                                    $mypath,
                                    'Basic',
                                    'Style Basic',
                                    $mypath.'views/img/sample/modele1.jpg',
                                    0
                                )
                                .'</li>
                                <li><input '.(Configuration::get('CONTACTFRM_FORM') == 1 ? 'checked="chekecd"' : '' ).
                                ' name="cfstyle" type="radio" value="1">'.self::imgpreview(
                                    $mypath,
                                    'Advanced',
                                    'Style Basic',
                                    $mypath.'views/img/sample/modele2.jpg',
                                    1
                                ).' ('.Translate::getModuleTranslation(
                                    'contactfrm',
                                    'Only for prestashop prestashop 1.4 ou higher',
                                    'contactfrm'
                                ).')</li>
                            </ul>
                        </div>
                    </div>';
        $output .= '<div class="form-group">
                        <label for="id_fid" class="control-label col-lg-3">'.
                        Translate::getModuleTranslation('contactfrm', 'Form width', 'contactfrm')
                        .'(<span style="font-weight: bold;color:#FF0000;">%</span>)
                        </label>
                        <div class="col-lg-3">
                            <input type="text" size=6 name="cfgfwidth" value="'.
                Configuration::get('CONTACTFRM_WIDTH').'">
                        </div>
                    </div>
            <div class="form-group">    
                <label for="cfgradio" class="control-label col-lg-3">'.
                Translate::getModuleTranslation(
                    'contactfrm',
                    'Displaying radio button',
                    'contactfrm'
                )
                .'</label>
                <div class="col-lg-3">
                    <select id="cfgradio" name="cfgradio">
                        <option value="1" '.(Configuration::get('CONTACTFRM_CFGRADIO') == 1 ?
                        'selected="selected"' : '').' >'.
                        Translate::getModuleTranslation('contactfrm', 'Horizontal', 'contactfrm')
                        .'</option>
                        <option value="0" '.(Configuration::get('CONTACTFRM_CFGRADIO') == 0 ?
                        'selected="selected"' : '').'>'.
                        Translate::getModuleTranslation('contactfrm', 'Vertical', 'contactfrm')
                        .'</option>
                    </select>
                </div>
            </div>
            <div class="form-group">    
                <label for="cfgckbox" class="control-label col-lg-3">'.
                Translate::getModuleTranslation('contactfrm', 'Displaying checkbox', 'contactfrm')
                .'</label>
                <div class="col-lg-3">
                    <select id="cfgckbox" name="cfgckbox">
                        <option value="1" '.(Configuration::get('CONTACTFRM_CFGCKBOX') == 1 ?
                        'selected="selected"' : '').' >'.
                        Translate::getModuleTranslation('contactfrm', 'Horizontal', 'contactfrm')
                        .'</option>
                        <option value="0" '.(Configuration::get('CONTACTFRM_CFGCKBOX') == 0 ?
                        'selected="selected"' : '').'>'.
                        Translate::getModuleTranslation('contactfrm', 'Vertical', 'contactfrm')
                        .'</option>
                    </select>
                </div>
            </div>';
            $output .= '<div class="form-group">
                        <label class="control-label col-lg-3">'.
                        Translate::getModuleTranslation(
                            'contactfrm',
                            'Show number of remaining characters',
                            'contactfrm'
                        )
                        .'</label>
                        <div class="col-lg-9">
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input '.(Configuration::get('CONTACTFRM_SHOWCAR') == 1 ?
                                'checked="checked"' : '' ).'
                                name="showcar" id="showcar_on" type="radio" value="1">
                                <label class="radioCheck" for="showcar_on">'.
                                Translate::getModuleTranslation('contactfrm', 'Yes', 'contactfrm').'
                                </label>
                                <input '.(Configuration::get('CONTACTFRM_SHOWCAR') == 0 ?
                                'checked="checked"' : '' ).'
                                name="showcar" id="showcar_off" type="radio" value="0">
                                <label class="radioCheck" for="showcar_off">
                                    '.Translate::getModuleTranslation('contactfrm', 'No', 'contactfrm').'
                                </label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>
                </div>
                <div class="form-group">    
                <label for="cctpl" class="control-label col-lg-3">
                    '.Translate::getModuleTranslation('contactfrm', 'Calendar template', 'contactfrm').'
                </label>
                <div class="col-lg-3">
                    <select id="cctpl" name="cctpl">
                        <option value="default" '.(Configuration::get('CONTACTFRM_CCTPL') == 'default' ?
                        'selected' : '').' >'.
                        Translate::getModuleTranslation('contactfrm', 'Default', 'contactfrm')
                        .'</option>
                        <option value="dark" '.
                        (Configuration::get('CONTACTFRM_CCTPL') == 'dark' ? 'selected' : '').
                        ' >'.Translate::getModuleTranslation('contactfrm', 'Dark', 'contactfrm')
                        .'</option>
                    </select>
                </div>
            </div>';
            $output .= '
            <div class="form-group">
                        <label class="control-label col-lg-3">'.
                            Translate::getModuleTranslation(
                                'contactfrm',
                                'Enable multi-form system',
                                'contactfrm'
                            )
                        .'</label>
                        <div class="col-lg-9">
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input '.(Configuration::get('CONTACTFRM_MULTIFORM') == 1 ? 'checked="checked"' : '' ).
                                ' name="multiform" id="multiform_on" type="radio" value="1">
                                <label class="radioCheck" for="multiform_on">'.
                                Translate::getModuleTranslation('contactfrm', 'Yes', 'contactfrm').'
                                </label>
                                <input '.(Configuration::get('CONTACTFRM_MULTIFORM') == 0 ? 'checked="checked"' : '' ).
                                ' name="multiform" id="multiform_off" type="radio" value="0">
                                <label class="radioCheck" for="multiform_off">
                                    '.Translate::getModuleTranslation('contactfrm', 'No', 'contactfrm').'
                                </label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>
                </div>
                ';
    
        $output .= '<table cellpadding="5" cellspacing="5">';
        $output .= '<tr><td style="color: rgb(0, 0, 0); 
        background: none repeat scroll 0px 0px rgb(235, 237, 244);" colspan="2">'.
        Translate::getModuleTranslation(
            'contactfrm',
            'The multi-form system is a system that can display multiple forms on a page.
            You can manage the relations between the forms by clicking',
            'contactfrm'
        )
        .'<a style="font-weight: bold; background: none repeat scroll 0% 0% rgb(255, 0, 0);
        margin-left: 5px;" href="index.php?tab=AdminModules&configure=contactfrm&task=formrelation&token='.
        Tools::getValue('token').'">&nbsp'.
        Translate::getModuleTranslation('contactfrm', 'here', 'contactfrm')
        .'</a></td></tr>';
        $output .= '</table>';
        
        $output .= '</div>';
        /* ========================================= END TAB 2 ====================================== */
        /* ========================================= TAB 3 ====================================== */
        $output .= '<div id="tab3" class="tab_content">';
        $output .= '<div class="form-group">
                        <label for="captchapubkey" class="control-label col-lg-3 required">
                                '.Translate::getModuleTranslation('contactfrm', 'Captcha public key', 'contactfrm').'
                        </label>
                        <div class="col-lg-3">
                            <input type="text" name="captchapubkey" id="captchapubkey" value="'.
        Configuration::get('CONTACTFRM_CAPTCHAPUBKEY').'" size="40" />
                        </div>
                    </div>';
    
        $output .= '<div class="form-group">
                        <label for="captchaprivkey" class="control-label col-lg-3 required">
                                '.Translate::getModuleTranslation('contactfrm', 'Captcha private key', 'contactfrm').'
                        </label>
                        <div class="col-lg-3">
                            <input type="text" name="captchaprivkey" id="captchaprivkey" value="'.
        Configuration::get('CONTACTFRM_CAPTCHAPRIVKEY').'" size="40" />
                        </div>
                    </div>';
    
        $output .= '<table cellpadding="2" cellspacing="2">';
        $output .= "<tr><td colspan='2' style='font-style: italic; 
        color: rgb(153, 153, 153); font-size: 12px;'><br /><sup style='color:#FF0000;'>*</sup>: ".
        Translate::getModuleTranslation(
            'contactfrm',
            'Click the following link to get the keys (public and private) of captcha',
            'contactfrm'
        )
        .": <a href='https://www.google.com/recaptcha/admin#whyrecaptcha' 
        target='_blank'>https://www.google.com/recaptcha/admin#whyrecaptcha</a></td></tr>";
        $output .= '<tr><td colspan="2" >'.
        Translate::getModuleTranslation(
            'contactfrm',
            'Captcha theme',
            'contactfrm'
        )
        .'</td></tr>';
        $output .= '<tr><td><input type="radio" name="captchatheme" value="1"';
        if (Configuration::get('CONTACTFRM_CAPTCHATHEME') == '1') {
            $output .= 'checked="checked"';
        }
        $output .= '/><img src="'.$mypath.'views/img/recaptcha/light.jpg" alt="'.
        Translate::getModuleTranslation('contactfrm', 'Light', 'contactfrm').'" /></td>
                        <td><input type="radio" name="captchatheme" value="2"  ';
        if (Configuration::get('CONTACTFRM_CAPTCHATHEME') == '2') {
            $output .= 'checked="checked"';
        }
        $output .= '/><img src="'.$mypath.'views/img/recaptcha/dark.jpg" alt="'.
        Translate::getModuleTranslation('contactfrm', 'Dark', 'contactfrm').
        '" /></td></tr>';
    
        $output .= '<tr><td colspan="2"><div style="margin-top: 20px; font-weight: bold;
        background: none repeat scroll 0px 0px rgb(255, 223, 146); padding: 5px;">
        </div></td></tr>';
        $output .= '</table>';
        $output .= '</div>';
        /* ========================================= END TAB 3 ====================================== */
        $output .= '
            <div class="panel-footer">
                <a class="btn btn-default pull-left"
                href="index.php?tab=AdminModules&configure=contactfrm&token='.Tools::getValue('token').
                '"><i class="process-icon-cancel"></i>
                '.Translate::getModuleTranslation('contactfrm', 'Cancel', 'contactfrm').'</a>
                <button class="btn btn-success pull-right" name="submitsettings" type="submit">
                <i class="process-icon-save"></i> '.
                Translate::getModuleTranslation('contactfrm', '    Save    ', 'contactfrm').'</button>
            </div>';
        $output .= '</div></form>';
        $output .= '</div>';
        return $output;
    }
    public static function imgpreview($mypath, $linktxt, $titletxt, $img, $theme)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $output = '<link rel="stylesheet" type="text/css" href="'.$mypath.'views/css/imgpreview.css" />';
        $output .= '<script type="text/javascript" src="'.$mypath.'views/js/imgpreview/imgpreview.js">
        </script>';
        $output .= '<a '.(
                    Configuration::get('CONTACTFRM_FORM') == (int)$theme ?
                    'style="text-decoration:underline padding:5px;
                    border: 1px solid green; background: lightgreen"' : '').
        ' href="#" rel="'.$img.'" class="screenshot" title="'.$titletxt.'">'.
        Translate::getModuleTranslation('contactfrm', $linktxt, 'contactfrm').'</a>';
        $output .= (Configuration::get('CONTACTFRM_FORM') == (int)$theme ? '
        <img style="padding-left:10px;" src="'.$mypath.'views/img/ok2.png">' : '');
        return $output;
    }
    public static function activateForm3($mypath)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $output = CFtoolsbar::toolbar('classic', $mypath);
        $output .= '
            <form method="POST" action="'.$_SERVER['REQUEST_URI'].'">
                    <fieldset>
                        <legend>'.
                            Translate::getModuleTranslation(
                                'contactfrm',
                                'Step 1 : Define php file name',
                                'contactfrm'
                            )
                        .'</legend>
                        <input type="text" value="'.
                        Configuration::get('CONTACTFRM_FILENAME').'" name="phpfile">'.
                        CFtools::info(
                            $mypath,
                            'Exemple: contact.php, myform-contact.php,
                            contact-us.php. <br>If you tape contact-form.php, it will
                            replace the original Prestashop contact form'
                        )
                        .'<input class="button btn btn-primary" type="submit" name="submitphpfile" value="'.
                        Translate::getModuleTranslation('contactfrm', 'Next', 'contactfrm').' >>">
                        <br><p style=" font-size:10px">'.
                        Translate::getModuleTranslation(
                            'contactfrm',
                            'This the name of the php file used by the module. For exemple, if you tape myform.php
                            , Your form will be accessible from the url http://www.yourdomain.com/myform.php',
                            'contactfrm'
                        )
                        .'</p>
                   </fieldset>
            </form>       
            ';
        return $output;
    }
    public static function activateForm($mypath, $id_lang)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $output = CFtoolsbar::toolbar('classic', $mypath, $id_lang);
        $output .= '    
            <script type="text/javascript">
                function checkFileName(){
                    var phpfile = $("#phpfile").val();'.
                    '
                    if(phpfile == "contact-form.php"){
                        if(confirm("Voulez-vous définir le formulaire de contact du
                        module contactform comme formulaire de contact par défaut de votre boutique?"))'.
                        '    return true;
                        else{return false;}
                    }
                    else{return true;}
                }
            </script>
            <style type="text/css">
                a.lnk_choice{
                    background: none repeat scroll 0 0 #222222;
                    color: #fff;
                    padding: 5px;
                }
                a.lnk_choice:hover{
                    background: none repeat scroll 0 0 #FFFFFF;
                    color: #222222;
                }
            </style>
            <div class="bootstrap panel">
            <form class="defaultForm form-horizontal" method="POST" action="'.$_SERVER['REQUEST_URI'].'">
                    <div class="pspage panel"><h3 class="tab"><i class="icon-info"></i> '.
                    Translate::getModuleTranslation('contactfrm', 'Step 1 : Define form type', 'contactfrm').'</h3>
                        
                        <div class="form-group">    
            <label for="cfshop" class="control-label col-lg-3">
                '.Translate::getModuleTranslation('contactfrm', 'Step 1 : Define form type', 'contactfrm').'
            </label>
            <div class="col-lg-3">
                <select id="phpfile" name="phpfile">
                    <option value="contact-form.php">'.
                        Translate::getModuleTranslation('contactfrm', 'Default form', 'contactfrm')
                    .'</option>
                    <option value="cform.php">'.
                        Translate::getModuleTranslation('contactfrm', 'New form', 'contactfrm')
                    .'</option>
                </select>
            </div>
        </div>';
    
        $output .= '<div class="form-group"><div class="col-lg-12">
            <p style="font-size:12px;">'.
                Translate::getModuleTranslation('contactfrm', 'Choose', 'contactfrm').' "'.
                        Translate::getModuleTranslation('contactfrm', 'New form', 'contactfrm').'" '.
                        Translate::getModuleTranslation(
                            'contactfrm',
                            'if you want to create another form other than that defines default prestashop.',
                            'contactfrm'
                        )
                        .'<span style=\'color:#FF0000;font-weight:bold;font-size:12px;\'>*
                        </span></p><p style=" font-size:12px">'.
                        Translate::getModuleTranslation('contactfrm', 'Choose', 'contactfrm').' "'.
                        Translate::getModuleTranslation('contactfrm', 'Default form', 'contactfrm').'" '.
                        Translate::getModuleTranslation(
                            'contactfrm',
                            'if you want to set the form generated by 
                             the module contactform as the default form of prestashop.',
                            'contactfrm'
                        )
                        .'</p><p style="font-size:12px">
                        <span style=\'color:#FF0000;font-weight:bold;font-size:12px;\'>*</span> '.
                        Translate::getModuleTranslation(
                            'contactfrm',
                            'You should manually edit the contact links on your store
                            by the link of contact generated by the contactform module.',
                            'contactfrm'
                        ).
                        ' ('.
                        Translate::getModuleTranslation('contactfrm', 'Replace', 'contactfrm')
                        .'"contact" '.Translate::getModuleTranslation('contactfrm', 'with', 'contactfrm')
                        .' "cform")</p>
        </div>
        </div>
                   </div>
        <div class="panel-footer">
            <a class="btn btn-default pull-left"
            href="index.php?tab=AdminModules&configure=contactfrm&token='.Tools::getValue('token').
            '"><i class="process-icon-cancel"></i> '.
            Translate::getModuleTranslation('contactfrm', 'Cancel', 'contactfrm').'</a>
            <button class="btn btn-default pull-right" name="submitphpfile" type="submit">
            <i class="process-icon-save"></i> '.
            Translate::getModuleTranslation('contactfrm', '    Save    ', 'contactfrm').'</button>
        </div>
                   </form>
                   </div>
            ';
        return $output;
    }
    public static function activateForm2($mypath, $phpfile, $id_lang)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        if (!$phpfile || $phpfile == '') {
            $phpfile = 'cform.php';
        }
            $mytoken = Tools::getValue('token');
            $output = CFtoolsbar::toolbar('classic', $mypath, $id_lang);
        switch ($phpfile) {
            case 'contact-form.php':
                $originalcontroller = _PS_ROOT_DIR_.'/controllers/front/ContactController.php';
                $backupcontroller   = _PS_MODULE_DIR_.'contactfrm/bkp/original/ContactController.php';
                if (!file_exists($backupcontroller)) {
                        $cp2 = Tools::copy($originalcontroller, $backupcontroller);
                    if (!$cp2) {
                            $output .= CFtools::errFormat(
                                Translate::getModuleTranslation(
                                    'contactfrm',
                                    'Backup process error (copying file failed:
                                    ContactController.php to modules/bkp/original/ContactController.php',
                                    'contactfrm'
                                ),
                                0,
                                false
                            );
                    }
                }
                    $orignaltpl = _PS_ROOT_DIR_.'/themes/'._THEME_NAME_.'/templates/contact.tpl';
                    $backuptpl  = _PS_MODULE_DIR_.'contactfrm/bkp/original/contact.tpl';
                if (((version_compare(_PS_VERSION_, '1.6', '>=')) === true)
                    && ((version_compare(_PS_VERSION_, '1.7', '<')) === true)) {
                    $orignaltpl = _PS_ROOT_DIR_.'/themes/'._THEME_NAME_.'/contact-form.tpl';
                    $backuptpl  = _PS_MODULE_DIR_.'contactfrm/bkp/original/contact-form.tpl';
                }
                if (!file_exists($backuptpl)) {
                        $cp3 = Tools::copy($orignaltpl, $backuptpl);
                    if (!$cp3) {
                            $output .= CFtools::errFormat(
                                Translate::getModuleTranslation(
                                    'contactfrm',
                                    'Backup process error (copying file failed: 
                                    contact.tpl to modules/bkp/original/contact.tpl',
                                    'contactfrm'
                                ),
                                0,
                                false
                            );
                    }
                }
                /*Copy contactfrm files*/
                if ((version_compare(_PS_VERSION_, '1.7', '>=')) === true) {
                    $cp4 = Tools::copy(
                        _PS_MODULE_DIR_.'contactfrm/install/controllers/ContactController.php',
                        _PS_ROOT_DIR_.'/override/controllers/front/ContactController.php'
                    );
                } elseif (((version_compare(_PS_VERSION_, '1.6', '>=')) === true)
                    && ((version_compare(_PS_VERSION_, '1.7', '<')) === true)) {
                    $cp4 = Tools::copy(
                        _PS_MODULE_DIR_.'contactfrm/install/controllers/1.6/ContactController.php',
                        _PS_ROOT_DIR_.'/override/controllers/front/ContactController.php'
                    );
                }
                

                if ((version_compare(_PS_VERSION_, '1.7', '>=')) === true) {
                    $cp5 = Tools::copy(
                        _PS_MODULE_DIR_.'contactfrm/views/templates/front/install/themes/contact.tpl',
                        _PS_ROOT_DIR_.'/themes/'._THEME_NAME_.'/templates/contact.tpl'
                    );
                } elseif (((version_compare(_PS_VERSION_, '1.6', '>=')) === true)
                    && ((version_compare(_PS_VERSION_, '1.7', '<')) === true)) {
                    $cp5 = Tools::copy(
                        _PS_MODULE_DIR_.'contactfrm/views/templates/front/install/themes/1.6/contact-form.tpl',
                        _PS_ROOT_DIR_.'/themes/'._THEME_NAME_.'/contact-form.tpl'
                    );
                }
                if (!$cp4 || !$cp5) {
                    $output .= CFtools::errFormat(
                        Translate::getModuleTranslation(
                            'contactfrm',
                            'The copy of the file failed. Please try again. 
                            If the problem persists, please manually copy 
                            (or via FTP) the file in the directory 
                            <b>modules/contactfrm/install/controllers/ContactController.php
                            </b> to directory <b>/override/controllers/front/</b> and files in <b>
                            modules/contactfrm/views/templates/front/install/themes/
                            contact.tpl</b> to <b>themes/yourtheme/</b>',
                            'contactfrm'
                        ),
                        0,
                        false
                    );
                } else {
                    /* DELETE cache file*/
                    if ((version_compare(_PS_VERSION_, '1.7', '>=')) === true) {
                        if (file_exists(_PS_ROOT_DIR_.'/app/cache/prod/class_index.php')) {
                            unlink(_PS_ROOT_DIR_.'/app/cache/prod/class_index.php');
                        }
                    } elseif (((version_compare(_PS_VERSION_, '1.6', '>=')) === true)
                        && ((version_compare(_PS_VERSION_, '1.7', '<')) === true)) {
                        if (file_exists(_PS_ROOT_DIR_.'/cache/class_index.php')) {
                            unlink(_PS_ROOT_DIR_.'/cache/class_index.php');
                        }
                    }
                    Configuration::updateValue('CONTACTFRM_ACTIVE', 0);
                    $output .= self::validRes(
                        Translate::getModuleTranslation(
                            'contactfrm',
                            'The copy of all files were finished.
                            If you still encounter problems after this process, 
                            please manually copy (or via FTP) the file in the directory
                            modules/contactfrm/install/controllers/ to directory /override/controllers/front/
                            and files in modules/contactfrm/views/templates/front/install/themes/ 
                            to themes/yourtheme/.',
                            'contactfrm'
                        )
                    );
                }
                break;
            case 'cform.php':
                if ((version_compare(_PS_VERSION_, '1.7', '>=')) === true) {
                    $cp6 = Tools::copy(
                        _PS_MODULE_DIR_.'contactfrm/install/controllers/CformController.php',
                        _PS_ROOT_DIR_.'/override/controllers/front/CformController.php'
                    );
                } elseif (((version_compare(_PS_VERSION_, '1.6', '>=')) === true)
                    && ((version_compare(_PS_VERSION_, '1.7', '<')) === true)) {
                    $cp6 = Tools::copy(
                        _PS_MODULE_DIR_.'contactfrm/install/controllers/1.6/CformController.php',
                        _PS_ROOT_DIR_.'/override/controllers/front/CformController.php'
                    );
                }
                

                if ((version_compare(_PS_VERSION_, '1.7', '>=')) === true) {
                    $cp7 = Tools::copy(
                        _PS_MODULE_DIR_.'contactfrm/views/templates/front/install/themes/cform.tpl',
                        _PS_ROOT_DIR_.'/themes/'._THEME_NAME_.'/templates/cform.tpl'
                    );
                } elseif (((version_compare(_PS_VERSION_, '1.6', '>=')) === true)
                    && ((version_compare(_PS_VERSION_, '1.7', '<')) === true)) {
                    $cp7 = Tools::copy(
                        _PS_MODULE_DIR_.'contactfrm/views/templates/front/install/themes/1.6/cform.tpl',
                        _PS_ROOT_DIR_.'/themes/'._THEME_NAME_.'/cform.tpl'
                    );
                }
                
                if (!$cp6 || !$cp7) {
                            $output .= CFtools::errFormat(
                                Translate::getModuleTranslation(
                                    'contactfrm',
                                    'The copy of the file failed.
                        Please try again. If the problem persists, 
                                please manually copy (or via FTP) the file in the directory
                        modules/contactfrm/install/controllers/ to directory /override/controllers/front/ and files in
                        modules/contactfrm/views/templates/front/install/themes/ to themes/yourtheme/templates/',
                                    'contactfrm'
                                ),
                                0,
                                false
                            );
                } else {
                            /* DELETE cache file*/
                    if ((version_compare(_PS_VERSION_, '1.7', '>=')) === true) {
                        if (file_exists(_PS_ROOT_DIR_.'/app/cache/prod/class_index.php')) {
                            unlink(_PS_ROOT_DIR_.'/app/cache/prod/class_index.php');
                        }
                    } elseif (((version_compare(_PS_VERSION_, '1.6', '>=')) === true)
                        && ((version_compare(_PS_VERSION_, '1.7', '<')) === true)) {
                        if (file_exists(_PS_ROOT_DIR_.'/cache/class_index.php')) {
                            unlink(_PS_ROOT_DIR_.'/cache/class_index.php');
                        }
                    }
                    Configuration::updateValue('CONTACTFRM_ACTIVE', 0);
                    $output .= self::validRes(
                        Translate::getModuleTranslation(
                            'contactfrm',
                            'The copy of all files were finished.
                    If you still encounter problems after this process,
                    please manually copy (or via FTP) the file in the directory
                    modules/contactfrm/install/controllers/ 
                    to directory /override/controllers/front/ and files in 
                    modules/contactfrm/views/templates/front/install/themes/
                    to themes/yourtheme/templates/.',
                            'contactfrm'
                        )
                    );
                }
                break;
        }
            $output .= '<a style="text-decoration:blink;" 
            href="index.php?tab=AdminModules&configure=contactfrm&token='.
        $mytoken.'">&lt;&lt;'.
        Translate::getModuleTranslation('contactfrm', 'Home', 'contactfrm')
        .'&gt;&gt;</a>';
            /*FIN Modif*/
            return $output;
    }
    public static function disableForm()
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $mytoken = Tools::getValue('token');
        $url = 'index.php?tab=AdminModules&configure=contactfrm&token='.$mytoken;
        $output = '';
        $file = _PS_MODULE_DIR_.'contactfrm/bkp/original/contact-form.php';
        $destination = _PS_ROOT_DIR_.'/contact-form.php';
        $controlleroriginal = _PS_MODULE_DIR_.'contactfrm/bkp/original/ContactController.php';
        $controllercf = _PS_ROOT_DIR_.'/controllers/front/ContactController.php';
        $tploriginal = _PS_MODULE_DIR_.'contactfrm/bkp/original/contact.tpl';
        $tplcf = _PS_ROOT_DIR_.'/themes/'._THEME_NAME_.'/templates/contact.tpl';
        
        if (Configuration::get('CONTACTFRM_FILENAME') == 'contact-form.php') {
            if (!Tools::copy($file, $destination)
                || !Tools::copy($controlleroriginal, $controllercf)
                || !Tools::copy($tploriginal, $tplcf)) {
                    $output .= CFtools::errFormat(
                        Translate::getModuleTranslation(
                            'contactfrm',
                            'The copy of the file failed. Please try again. If 
                            the problem persists, rename the file 
                            <b>modules/contactfrm/bkp/site/contact-form-orig.php</b>
                            to <b>contact-form.php</b> 
                            and  put it in the directory root of prestashop site.',
                            'contactfrm'
                        )
                    );
            } else {
                Configuration::updateValue('CONTACTFRM_DEACTIVE', 0);
                $output .= self::validRes(
                    Translate::getModuleTranslation(
                        'contactfrm',
                        'The restore process is completed successfully.
                        If you encounter problems after the restore process,
                        please copy manually via FTP, the file
                        /modules/contactfrm/bkp/original/ContactController.php 
                        in /override/controllers/front/ and
                        the file /modules/contactfrm/bkp/original/contact.tpl 
                        in /themes/your-theme.',
                        'contactfrm'
                    )
                );
            }
        } else {
            $output .= self::validRes(
                Translate::getModuleTranslation(
                    'contactfrm',
                    'Contactfrm was disabled successfully.',
                    'contactfrm'
                )
            );
        }
        $output .= '<a style="text-decoration:blink;" href="'.$url.'">&lt;&lt;'.
        Translate::getModuleTranslation('contactfrm', 'Home', 'contactfrm')

        .'&gt;&gt;</a>';
        return $output;
    }
    public static function btnActivecf($mode)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        switch ($mode) {
            case 1:
                Configuration::updateValue('CONTACTFRM_ACTIVE', 1);
                break;
            case 0:
                Configuration::updateValue('CONTACTFRM_ACTIVE', 0);
                break;
        }
    }
    public static function btnDeactivecf($mode)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        switch ($mode) {
            case 1:
                Configuration::updateValue('CONTACTFRM_DEACTIVE', 1);
                break;
            case 0:
                Configuration::updateValue('CONTACTFRM_DEACTIVE', 0);
                break;
        }
    }
    public static function seedata($link, $asc, $orderby, $pagelimit = 10, $start = 0, $id_lang = 1)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $lastpage = 0;
        $mytoken = Tools::getValue('token');
        $datalists = Db::getInstance()->ExecuteS(
            'SELECT * FROM '
            ._DB_PREFIX_.'contactform_data ORDER BY `'.$orderby.
            '` '.$asc.' LIMIT '.$start.','.$pagelimit.'  '
        );
        $output = '';
        $url = 'index.php?tab=AdminModules&configure=contactfrm&task=seedata&token='.$mytoken;
        $url2 = 'index.php?tab=AdminModules&configure=contactfrm&task=seedatadetails&token='.$mytoken;
        $url3 = 'index.php?tab=AdminModules&configure=contactfrm&task=deldata&token='.$mytoken;
        if ($asc == 'ASC') {
            $asc = 'DESC';
        } elseif ($asc == 'DESC') {
            $asc = 'ASC';
        }
        $output .= CFtoolsbar::toolbar('seedata', $link, $id_lang);
        $output .= '
        <style type="text/css">
        table {
            border-collapse:separate;
            border-spacing:0pt;
        }
        caption, th, td {
            font-weight:normal;
            text-align:left;
        }
        a{
            cursor: pointer;
            text-decoration:none;
        }
        br.both{
            clear:both;
        }
        #backgroundPopup{
            display:none;
            position:fixed;
            _position:absolute; /* hack for internet explorer 6*/
            height:100%;
            width:100%;
            top:0;
            left:0;
            background:#000000;
            border:1px solid #cecece;
            z-index:1;
        }
        #popupContact{
            display:none;
            position:fixed;
            _position:absolute; /* hack for internet explorer 6*/
            height:384px;
            width:408px;
            background:#FFFFFF;
            border:2px solid #cecece;
            z-index:2;
            padding:12px;
            font-size:13px;
        }
        #popupContact h1{
            text-align:left;
            color:#6FA5FD;
            font-size:22px;
            font-weight:700;
            border-bottom:1px dotted #D3D3D3;
            padding-bottom:2px;
            margin-bottom:20px;
        }
        #popupContactClose{
            font-size:14px;
            line-height:14px;
            right:6px;
            top:4px;
            position:absolute;
            color:#6fa5fd;
            font-weight:700;
            display:block;
        }
        </style>
        <script>
        var popupStatus = 0;
        //loading popup with jQuery magic!
        function loadPopup(){
        //loads popup only if it is disabled
        if(popupStatus==0){
            $("#backgroundPopup").css({
                "opacity": "0.7"
            });
            $("#backgroundPopup").fadeIn("slow");
            $("#popupContact").fadeIn("slow");
            popupStatus = 1;
        }
        }
        //disabling popup with jQuery magic!
        function disablePopup(){
        //disables popup only if it is enabled
        if(popupStatus==1){
            $("#backgroundPopup").fadeOut("slow");
            $("#popupContact").fadeOut("slow");
            popupStatus = 0;
        }
        }
        //centering popup
        function centerPopup(){
        //request data for centering
        var windowWidth = document.documentElement.clientWidth;
        var windowHeight = document.documentElement.clientHeight;
        var popupHeight = $("#popupContact").height();
        var popupWidth = $("#popupContact").width();
        //centering
        $("#popupContact").css({
            "position": "absolute",
            "top": windowHeight/2-popupHeight/2,
            "left": windowWidth/2-popupWidth/2
        });
        //only need force for IE6
        
        $("#backgroundPopup").css({
            "height": windowHeight
        });
        
        }
        //CONTROLLING EVENTS IN jQuery
        var utils2 = setInterval(function(){ 
            if (typeof($) != "undefined") {

                $(document).ready(function(){
                    $("#popupContactClose").click(function(){
                        disablePopup();
                    });
                    //Click out event!
                    $("#backgroundPopup").click(function(){
                        disablePopup();
                    });
                    //Press Escape event!
                    $(document).keypress(function(e){
                        if(e.keyCode==27 && popupStatus==1){
                            disablePopup();
                        }
                    });
                
                });

                clearInterval(utils2);
            }
        }, 500);
        </script>
        <script>
        function addscript(id,foremail, toemail){
            var mailadress = document.getElementById("mailadress");    
            var mailsender = document.getElementById("mailsender");    
            mailadress.value = foremail;
            mailsender.value = toemail;
            
            $(document).ready(function(){
                    $("#button"+id).click(function(){
                    
                    //centering with css
                    centerPopup();
                    //load popup
                    loadPopup();
                });
            });
        }
        </script>
        <div style="position: absolute; top: 71.5px; left: 476px;
        display: none; background:#EEF2F7" id="popupContact">
            <a id="popupContactClose"><img src="'.$link.'views/img/close.png" alt="X" /></a>
            <h1>'.
                Translate::getModuleTranslation('contactfrm', 'INSTANT MAIL RESPONSE', 'contactfrm')
            .'</h1>
            <p id="contactArea">
                <form  method="post" action="'.$_SERVER['REQUEST_URI'].'">
                <table cellpadding="5" cellspacing="5">
                    <tr>
                    <td>'.Translate::getModuleTranslation('contactfrm', 'Recipient', 'contactfrm').'</td>
                    <td><input size=40 id="mailadress" type="text" name="mailadress" value="" /></td>
                    </tr>
                    <tr>
                    <td>'.Translate::getModuleTranslation('contactfrm', 'Subject', 'contactfrm').':
                    </td><td><input size=40 type="text" name="mailsubject" value="" /></td>
            </tr>
                <tr valign="top">
                    <td valign="top">Message:</td><td><textarea cols="45"
                    rows="10" name="mailmessage"/></textarea></td>
                </tr>
                <tr>
                    <td>'.Translate::getModuleTranslation('contactfrm', 'Sender', 'contactfrm').'</td>
                    <td><input size=40 id="mailsender" type="text" name="mailsender" value="" /></td>
                </tr>
                <tr>
                    <td></td><td><input type="submit" name="mailsubmit" value="'.
                    Translate::getModuleTranslation('contactfrm', 'Envoyer', 'contactfrm')
                    .'" /></td>
                </tr>
                </table>
            </form>
            </p>
        </div>
        <div style="height: 527px; opacity: 0.7; display: none;" id="backgroundPopup"></div>';
        $output .= '<div class="panel"><h3 class="tab"> <i class="icon-info"></i> '.
        Translate::getModuleTranslation('contactfrm', 'Data list', 'contactfrm').'</h3>';
        $output .= '<div id="itemList" class="itemList">';
        //All data
        $alldata = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'contactform_data 
        ORDER BY `'.$orderby.'` '.$asc);
        $output .= '<ul class="pagination pull-right">';
        $output .= '<li>
<a class="pagination-link" data-list-id="order" data-page="1" href="'.$url.'&pagelimit='.$pagelimit.'&start=0">
<i class="icon-double-angle-left"></i>
</a>
</li>';
        $ctalld = count($alldata);
        for ($i = 0; $i < $ctalld / $pagelimit; $i++) {
             $output .= '<li>
                <a href="'.$url.'&pagelimit='.$pagelimit.'&start='.$pagelimit * $i.
                '" data-page="'.($i + 1).'" class="pagination-link" >
                    '.($i + 1).'
                </a>
            </li>';
            $lastpage = $pagelimit * $i;
        }
        
        $output .= '<script type="text/javascript">
        var utils01 = setInterval(function(){
            if (typeof($) != "undefined") {

                $("input[name^=\'actlink[\'][name$=\']\']").each(function(i){
                    $(this).click(function(){
                        if (!$(this).is(\':checked\') ) {
                            $("input[name=\'checkall\']").attr(\'checked\', false);
                        }
                    });
                });

                clearInterval(utils01);
            }
        }, 500);

        checked=false;
        function checkedAll (frm1) {
        var aa= document.getElementById("frm1");
        if (checked == false)
            {
            checked = true
            }
        else
          {
          checked = false
          }
        for (var i =0; i < aa.elements.length; i++) 
        {
            aa.elements[i].checked = checked;
        }
      }
    </script>';
        $output .= '<li>
<a class="pagination-link" data-list-id="order" data-page="'.$lastpage.'" href="'
        .$url.'&pagelimit='.$pagelimit.'&start='.$lastpage.'">
<i class="icon-double-angle-right"></i>
</a>
</li>';
        $output .= '</ul>';
        $output .= '<form id ="frm1" name="frm1" method="post" action="'.$_SERVER['REQUEST_URI'].'" >';
        $output .= '<table width="100%" class="table" cellspacing="0" cellpadding="0">';
        $output .= '<thead><tr class="nodrag nodrop" >
        <th><input type="checkbox" name="checkall" onclick="checkedAll(frm1);"></th>
        <th><a href="'.$url.'&asc='.$asc.'&orderby=data_id">ID</a></th>
        <th><a href="'.$url.'&asc='.$asc.'&orderby=ip">'.
            Translate::getModuleTranslation('contactfrm', 'Ip address', 'contactfrm')
        .'</a></th>
        <th><a href="'.$url.'&asc='.$asc.'&orderby=date">'.
            Translate::getModuleTranslation('contactfrm', 'Date', 'contactfrm')
        .'</a></th>
        <th><a href="'.$url.'&asc='.$asc.'&orderby=toemail">'.
            Translate::getModuleTranslation('contactfrm', 'Mail to', 'contactfrm')
        .'</a></th>
        <th><a href="'.$url.'&asc='.$asc.'&orderby=foremail">'.
            Translate::getModuleTranslation('contactfrm', 'Mail From', 'contactfrm')
        .'</a></th>
        <th><a href="'.$url.'&asc='.$asc.'&orderby=info">'.
            Translate::getModuleTranslation('contactfrm', 'Message sent', 'contactfrm')
        .'</a></th>
        <th><a href="'.$url.'&asc='.$asc.'&orderby=statut_mail">'.
            Translate::getModuleTranslation('contactfrm', 'Mail Statut', 'contactfrm').'</a></th>
        <th width="15%">'.
            Translate::getModuleTranslation('contactfrm', 'Actions', 'contactfrm')
        .'</th></tr></thead>';
        foreach ($datalists as $datalist) {
            $output .= '<tr valign="top">';
            $output .= '<td align="left"><input type="checkbox" 
            name="actlink['.$datalist['data_id'].']" value="1"></td>';
            $output .= '<td><a href="'.$url2.'&data_id='.$datalist['data_id'].'">'.
            $datalist['data_id'].'</a></td>';
            $output .= '<td><a href="'.$url2.'&data_id='.$datalist['data_id'].'">'.
            $datalist['ip'].'</a></td>';
            $tabdate = explode('/', $datalist['date']);
            if (CFtools::getIsocode($id_lang) == 'fr') {
                $mdate = $tabdate[1].'/'.$tabdate[0].'/'.$tabdate[2];
            } else {
                $mdate = $datalist['date'];
            }
            $output .= '<td><a href="'.$url2.'&data_id='.$datalist['data_id'].'">'.
            $mdate.'</a></td>';
            $output .= '<td><a href="'.$url2.'&data_id='.$datalist['data_id'].'">'.
            $datalist['toemail'].'</a></td>';
            $output .= '<td><a href="'.$url2.'&data_id='.$datalist['data_id'].'">'.
            $datalist['foremail'].'</a></td>';
            $output .= '<td><a href="'.$url2.'&data_id='.$datalist['data_id'].'">'.
            CFtools::substrStr(50, $datalist['info']).'</a></td>';
            if ($datalist['statut_mail'] == 'mail') {
                $output .= '<td><a class="list-action-enable action-enabled" href="'.$url2.'&data_id='.
                $datalist['data_id'].'"><i class="icon-check"></i></a>
                <a href="'.$url2.'&data_id='.$datalist['data_id'].'">'.
                Translate::getModuleTranslation('contactfrm', 'Mail sent', 'contactfrm').'</a></td>';
            } else {
                $output .= '<td><a class="list-action-enable action-disabled" href="'.$url2.
                '&data_id='.$datalist['data_id'].'"><i class="icon-remove"></i></a>
                <a href="'.$url2.'&data_id='.$datalist['data_id'].'">'.
                Translate::getModuleTranslation('contactfrm', 'Mail not sent', 'contactfrm').'</a></td>';
            }
            $output .= '<td>
            <div style="display: none;" id="button'.
            $datalist['data_id'].'"><a onclick="addscript('.$datalist['data_id'].',\''.
            $datalist['foremail'].'\',\''.$datalist['toemail'].'\')" >Repondre</a></div>
            

            <div class="btn-group-action">              <div class="btn-group pull-right">
                    <a class="edit btn btn-default" onclick="clickCustum('.$datalist['data_id'].')" title="Repondre" >
    <i class="icon-reply"></i> Repondre
</a>
                                        <button data-toggle="dropdown" class="btn btn-default dropdown-toggle">
                        <i class="icon-caret-down"></i>&nbsp;
                    </button>
                        <ul class="dropdown-menu">
                        <li>
                                <a title="'.Translate::getModuleTranslation('contactfrm', 'Preview', 'contactfrm').
                                '" href="'.$url2.'&data_id='.$datalist['data_id'].'">
    <i class="icon-eye"></i> '.
                Translate::getModuleTranslation('contactfrm', 'Preview', 'contactfrm').'
</a>
                            </li>
<li class="divider"></li>
                            <li>
            <a class="delete" title="'.Translate::getModuleTranslation('contactfrm', 'Delete', 'contactfrm').
            '" onclick="return(confirm(\''.
            Translate::getModuleTranslation(
                'contactfrm',
                'Do you really want to delete this message?',
                'contactfrm'
            )
            .'\'));" href="'.$url3.'&data_id='.$datalist['data_id'].'">
    <i class="icon-trash"></i> '.Translate::getModuleTranslation('contactfrm', 'Delete', 'contactfrm').'
</a>                            </li>
                                                                            </ul>
                                    </div>
                </div>

            </td>';
            $output .= '</tr>';
        }
        $output .= '</table>';
        $output .= '<input class="button btn btn-danger" type="submit" name="deleteselectdata" value="'.
        Translate::getModuleTranslation('contactfrm', 'Delete selected', 'contactfrm')
        .'" onclick="return(confirm(\''.
        Translate::getModuleTranslation(
            'contactfrm',
            'Do you really want  to deleted data?',
            'contactfrm'
        )
        .'\'));" style="margin:10px;">';
        $output .= '</form>';
        $output .= '</div>';
        $output .= '</div>
        <style type="text/css">
        .table tr td a{
            color: #656565;
        }
        </style>
        <script type="text/javascript">
        function clickCustum(id_cl){
            jQuery("#button"+id_cl+" a").trigger("click");
        }
        </script>
        ';
        return $output;
    }
    public static function addsample($mypath, $id_lang)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $mytoken = Tools::getValue('token');
        $url = 'index.php?tab=AdminModules&configure=contactfrm&token='.$mytoken;
    
        $output = CFtoolsbar::toolbar('classic', $mypath, $id_lang);
    
        $output .= '<div class="panel"><h3 class="tab"> <i class="icon-info"></i> '.
        Translate::getModuleTranslation('contactfrm', 'Add sample', 'contactfrm').'</h3>';
        $output .= '<center><table class="homepage" border="1">';
        $output .= '<tr>';
        $output .= '<td>';
        $output .= '<table class="homepage1" border="1" >';
        $output .= '<tr>';
        $output .= '<td valign="top"><a href="'.$url.'&task=importsample&model=1">
        <img src="'.$mypath.'views/img/sample/modele1.jpg"><br>'.
        Translate::getModuleTranslation('contactfrm', 'BASIC FORM', 'contactfrm').'</a></td>';
        $output .= '<td  valign="top"><a href="'.$url.'&task=importsample&model=2"><img src="'.
        $mypath.'views/img/sample/modele2.jpg"><br>'.
        Translate::getModuleTranslation('contactfrm', 'INSCRIPTION FORM', 'contactfrm')
        .'</a></td>';
        $output .= '</tr>';
        $output .= '</table>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '</table></center>';
        $output .= '</div>';
        return $output;
    }
    public static function importSample($model, $mypath, $id_lang)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $ctnx = Context::getContext();
        $defaultlayout = '<html>
    <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
        <title>{message_from} {shop_name}</title>
    </head>
    <body>
        <table style=\"font-family:Verdana,sans-serif; font-size:11px; color:#374953; width: 550px;\">
            <tr>
                <td align=\"left\">
                    <a href=\"{shop_url}\" title=\"{shop_name}\">
                    {shop_logo}</a>
                </td>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr>
                <td align=\"left\" style=\"background-color:#DB3484; color:#FFF; font-size: 12px;'.
                ' font-weight:bold; padding: 0.5em 1em;\">{contactfrm_in}  {form_name}</td>
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
                <td align=\"center\" style=\"font-size:10px; border-top: 1px solid #D9DADE;\">
                    <a href=\"{shop_url}\" style=\"color:#DB3484; font-weight:bold; text-decoration:none;\">
                    {shop_name}</a> powered with <a href=\"http://www.mydomain.com/\" style=\"text-decoration:none;
                    color:#374953;\">Contactfrm</a>
                </td>
            </tr>
        </table>
    </body>
    </html>';
        $customerlayout = '
    <html>
    <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
        <title>{notification} {shop_name}</title>
    </head>
    <body>
        <table style=\"font-family:Verdana,sans-serif; font-size:11px; color:#374953; width: 550px;\">
            <tr>
                <td align=\"left\">
                    <a href=\"{shop_url}\" title=\"{shop_name}\">{shop_logo}</a>
                </td>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr>
                <td align=\"left\" style=\"background-color:#DB3484; color:#FFF; font-size: 12px;
                font-weight:bold; padding: 0.5em 1em;\">{notification} {shop_name}</td>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr>
                <td>
                    {message}
                </td>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr>
                <td align=\"center\" style=\"font-size:10px; border-top: 1px solid #D9DADE;\">
                    <a href=\"{shop_url}\" style=\"color:#DB3484; font-weight:bold; text-decoration:none;\">
                    {shop_name}</a> powered with 
                    <a href=\"http://www.mydomain.com/\" style=\"text-decoration:none; color:#374953;\">Contactfrm</a>
                </td>
            </tr>
        </table>
    </body>
    </html>';

        switch ($model) {
            case 1:
                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'contactform` (`formname`,
        `email`,
        `mailtype`,
        `layout`,
        `clayout`,
        `idcms`,
        `id_shop`) VALUES
        ("BasicForm", "admin@admin.com", "0","'.$defaultlayout.'","'.$customerlayout.'", "0", "1");');

                $cte_id = (int)Db::getInstance()->Insert_ID();

                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_lang` (
        `id_lang`,
        `fid`, `alias`, `formtitle`, `thankyou`, `msgbeforeForm`,
        `msgafterForm`, `toname`, `subject`, `subject_notif`, `automailresponse`, `returnurl`)
        VALUES
        ('.CFtools::getIdlangFromiso('en').', "'.(int)$cte_id.'", "contact-form", "Contact Form",
        "#",
        "<p><strong>Sample</strong> html Text <em>before</em> form </p>
        <center><img src=\"'.__PS_BASE_URI__.'img/logo.jpg\"></center>",
        "<p><strong>Sample</strong> html Text <em>after</em> form </p>
        <center><img src=\"'.__PS_BASE_URI__.'img/logo.jpg\"></center>",
        "Administrator", "Contact Prestashop", "Contact Prestashop Notification",
        "<p>Thank you for your request. 
        We will respond shortly to the email you just send us. Sincerely.<br />
        <br />Team.</p>", "#"),
        ('.CFtools::getIdlangFromiso('fr').', "'.(int)$cte_id.'",
        "formulaire-de-contact", "Formulaire de contact", "#", "<p>
        <strong>Exemple</strong> de texte <em>html</em> avant le formulaire </p><center><img src=\"'
                    .__PS_BASE_URI__.'img/logo.jpg\"></center>",
                    "<p><strong>Exemple</strong> de texte <em>html</em> après le formulaire </p><center>
        <img src=\"'.__PS_BASE_URI__.'img/logo.jpg\"></center>", 
        "Administrateur", "Contact Prestashop", "Notification Contact Prestashop", "<p>
        Merci pour votre demande. Nous répondrons très prochainement
        au mail que vous venez de nous faire parvenir. 
        Bien cordialement.<br /><br />L\'équipe.</p>", "#"),

        ('.CFtools::getIdlangFromiso('es').', "'.(int)$cte_id.'", 
        "formulario-de-contacto", "Formulario de contacto", "#", 
        "<p><strong>Muestra</strong> html Texto <em>antes</em>
        de formulario</p><center><img src=\"'
                    .__PS_BASE_URI__.'img/logo.jpg\"></center>", "<p>
                    <strong>Muestra</strong> html html texto después de la formulario</p><center><img src=\"'
                    .__PS_BASE_URI__.'img/logo.jpg\"></center>", "Administrador", 
                    "Póngase en contacto con PrestaShop",
        "Póngase en contacto con PrestaShop notificación", "<p>
        Gracias por su solicitud. Nosotros responderemos a la brevedad
        al correo electrónico que nos acaba de enviar. 
        Atentamente.<br /><br />Equipo.</p>", "#"),

        ('.CFtools::getIdlangFromiso('de').', "'.(int)$cte_id.'", "kontaktformular", "Kontaktformular", "#", 
        "<p><strong>Voorbeeld</strong> html tekst <em>voordat</em>
        het formulier</p><center><img src=\"'.
                    __PS_BASE_URI__.'img/logo.jpg\"></center>", "<p><strong>Voorbeeld</strong>
                    html text <em>html</em> nadat het formulier</p>
        <center><img src=\"'.__PS_BASE_URI__.'img/logo.jpg\"></center>", 
        "Administrator", "Kontakt PrestaShop", "Kontakt PrestaShop Benachrichtigung", "
        <p>Vielen Dank für Ihre Anfrage. Wir werden in Kürze Antwort
        auf die E-Mail senden Sie uns einfach. 
        Mit freundlichen Grüßen.<br /><br />Team.</p>", "#"),

        ('.CFtools::getIdlangFromiso('it').', "'.(int)$cte_id.'", "modulo-di-contatto", "Modulo di contatto",
        "#",
        "<p><strong>Testo</strong> di esempio html <em>prima</em>
        che il modulo</p><center><img src=\"'.__PS_BASE_URI__.'img/logo.jpg\"></center>",
        "<p><strong>Testo</strong> di esempio html <em>prima</em>
        che il modulo</p>
        <p><strong>Esempio</strong> di testo html <em>html</em> dopo il form</p><center><img src=\"'.
                    __PS_BASE_URI__.'img/logo.jpg\"></center>","Administrator", 
                    "Contatta PrestaShop", "Contatta PrestaShop notifica",
        "<p>Grazie per la vostra richiesta. 
        Ci sarà presto una risposta alle e-mail 
        è sufficiente inviare. Cordiali saluti.<br /><br />Team.</p>",
        "#");'
                );
        //========================= ITEM ==========================
                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'contactform_item` (
        `fid`, `fields_id`, `fields_name`, `confirmation`, 
        `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`,
        `fields_maxtxt`, `fields_require`, `order`, `published`) 
        VALUES('.(int)$cte_id.', "title", "title", 0, "none", "select", "", "", "",250,1, 1, 1);');
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (
                    `fdid`, `id_lang`, `fields_title`, 
                    `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en').',
                    "Title", "", "", ";Mrs;Ms;Mr", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').',
                    "Titre", "", "", ";Mme;Mlle;Mr", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').',
                    "Título", "", "", ";Sra.; El Sr.", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').',
                    "Titel", "", "", "Frau;Fräulein;Herr", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it').',
                    "Titolo", "", "", "Ms.; Miss; Sig.", "", "");'
                );
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item` (
                    `fid`, `fields_id`, `fields_name`, `confirmation`, 
                    `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`,
                    `fields_maxtxt`, `fields_require`, `order`, `published`) 
                    VALUES ('.(int)$cte_id.', "name", "name", 0, "none",
                    "text", "", "", "",250, 1, 2, 1);'
                );
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (
                    `fdid`, `id_lang`, `fields_title`,
                    `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en')
                    .', "Your full name", "", "", "Your full name ...", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').',
                    "Votre nom et prenom", "", "", "Votre nom ...", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').',
                    "Su nombre", "", "", "Su nombre ..", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').',
                    "Ihr vollständiger Name", "", "", "Ihr Name ...", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it').', 
                    "Il tuo nome completo", "", "", "Il tuo nome completo", "", "");'
                );
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item` (
                    `fid`, `fields_id`, `fields_name`, `confirmation`, 
                    `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`,
                    `fields_maxtxt`, `fields_require`, `order`, `published`) 
                    VALUES ( '.(int)$cte_id.', "myemail", "myemail", 1,
                    "email", "email", "", "", "",250, 1, 3, 1);'
                );
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (
                    `fdid`, `id_lang`, `fields_title`, 
                    `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en').',
                    "Your e-mail", "", "Confirm your email", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').',
                    "Votre e-mail", "", "Confirmer votre email", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').',
                    "Tu e-mail", "", "Confirme su correo electrónico", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').', 
                    "Ihre E-Mail", "", "Bestätigen Sie Ihre E-Mail", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it').', 
                    "Il tuo indirizzo e-mail", "", "Conferma la tua email", "", "", "");'
                );
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item` (
                    `fid`, `fields_id`, `fields_name`, `confirmation`, 
                    `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, 
                    `fields_maxtxt`, `fields_require`, `order`, `published`) 
                    VALUES ('.(int)$cte_id.', "subject", "subject", 0,
                    "none", "text", "", "", "",250, 1, 4, 1);'
                );
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (
                    `fdid`, `id_lang`, `fields_title`, 
                    `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en').',  "Subject", "", "", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').', "Sujet", "", "", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').', "Tema", "", "", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').',  "Über", "", "", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it').', "Soggetto", "", "", "", "", "");'
                );
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item` (
                    `fid`, `fields_id`, `fields_name`, `confirmation`, 
                    `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`,
                    `fields_maxtxt`, `fields_require`, `order`, `published`) 
                    VALUES ('.(int)$cte_id.', "message", "message", 0, "none",
                    "textarea", "", "", "rows=\"8\" cols=\"40\"", 250,1, 5, 1);'
                );
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (
                    `fdid`, `id_lang`, `fields_title`, 
                    `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en').',  "Message", "", "", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').', "Message", "", "", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').', "Mensaje", "", "", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').',  "Nachricht", "", "", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it').', "Messaggio", "", "", "", "", "");'
                );
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item` (
                    `fid`, `fields_id`, `fields_name`, `confirmation`, 
                    `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`,
                    `fields_maxtxt`, `fields_require`, `order`, `published`)
                    VALUES ('.(int)$cte_id.', "captcha", "captcha", 1, "none", "captcha", "", "", "", 250,0, 6, 1);'
                );
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (
                    `fdid`, `id_lang`, `fields_title`, 
                    `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en').',
                    "Verification code", "", "Retape code here", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').',
                    "Code de sécurity", "", "Recopier le code ici", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').',
                    "Código de verificación", "", "Copia el código aquí", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').',
                    "Bestätigungs-Code", "", "Kopieren Sie den Code hier", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it').',
                    "Codice di verifica", "", "Riscrivi il codice qui", "", "", "");'
                );
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item` (
                    `fid`, `fields_id`, `fields_name`, `confirmation`, 
                    `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`,
                    `fields_maxtxt`, `fields_require`, `order`, `published`) 
                    VALUES ('.(int)$cte_id.', "submit", "submit", 0, "none", "submitbtn", "", "", "", 250,0, 7, 1);'
                );
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (`fdid`,
                    `id_lang`,`fields_title`,`fields_desc`, `confirmation_txt`, 
                    `fields_default`, `error_txt`, `error_txt2`) VALUES
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en').', "", "", "", "Send", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').', "", "", "", "Envoyer", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').', "", "", "", "Enviar", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').', "", "", "", "Senden", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it').', "", "", "", "Invia", "", "");'
                );
                break;
        //============================ MODEL 2 ===============================================
            case 2:
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.
                    'contactform` (`formname`, `email`, `mailtype`, `layout`, `clayout`, `idcms`, `id_shop`) VALUES
                    ("InscriptionForm", "admin@admin.com", "0","'.$defaultlayout.
                    '","'.$customerlayout.'", "0", "1");'
                );
                $cte_id = (int)Db::getInstance()->Insert_ID();

                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'contactform_lang` (
        `id_lang`, `fid`, `alias`, `formtitle`, 
        `thankyou`, `msgbeforeForm`, `msgafterForm`, `toname`, `subject`,
        `subject_notif`, `automailresponse`, `returnurl`) 
        VALUES ('.CFtools::getIdlangFromiso('en').', "'.(int)$cte_id.'",
        "contact-form", "Contact Form", "#", "<p><strong>Sample</strong> html Text <em>
        before</em> form </p><center><img src=\"'.__PS_BASE_URI__.'img/logo.jpg\"></center>",
        "<p><strong>Sample</strong> html Text <em>
        after</em> form </p><center><img src=\"'.__PS_BASE_URI__.'img/logo.jpg\"></center>",
        "Administrator", "Contact Prestashop",
        "Contact Prestashop Notification", "<p>
        Thank you for your request. We will respond shortly to the 
        email you just send us. Sincerely.<br /><br />Team.</p>", "#"),

        ('.CFtools::getIdlangFromiso('fr').', "'.(int)$cte_id.'", 
        "formulaire-de-contact", "Formulaire de contact", "#", "<p>
        <strong>Exemple</strong> de texte <em>html</em> avant le formulaire </p><center>
        <img src=\"'.__PS_BASE_URI__.'img/logo.jpg\"></center>", "<p>
        <strong>Exemple</strong> de texte <em>html</em> après le formulaire </p>
        <center><img src=\"'.__PS_BASE_URI__.'img/logo.jpg\"></center>",
        "Administrateur", "Contact Prestashop", "Notification Contact Prestashop", "<p>
        Merci pour votre demande. Nous répondrons très prochainement
        au mail que vous venez de nous faire parvenir. Bien cordialement.
        <br /><br />L\'équipe.</p>", "#"),

        ('.CFtools::getIdlangFromiso('es').', "'.(int)$cte_id.'",
        "formulario-de-contacto", "Formulario de contacto", "#","","", "Administrador",
        "Póngase en contacto con PrestaShop", "Póngase en contacto
        con PrestaShop notificación", "<p>Gracias por su solicitud. 
        Nosotros responderemos a la brevedad al correo electrónico 
        que nos acaba de enviar. Atentamente.<br />
        <br />Equipo.</p>", "#"),

        ('.CFtools::getIdlangFromiso('de').', "'.(int)$cte_id.'", "kontaktformular", "Kontaktformular", "#",
        "<p><strong>Voorbeeld</strong> html tekst <em>voordat</em> het formulier</p><center>
        <img src=\"'.__PS_BASE_URI__.'img/logo.jpg\"></center>", "<p><strong>Voorbeeld</strong> html text <em>
        html</em> nadat het formulier</p><center><img src=\"'.__PS_BASE_URI__.'img/logo.jpg\"></center>",
        "Administrator", "Kontakt PrestaShop", "Kontakt PrestaShop Benachrichtigung", "
        <p>Vielen Dank für Ihre Anfrage. Wir werden in Kürze Antwort
        auf die E-Mail senden Sie uns einfach. Mit freundlichen Grüßen.<br />
        <br />Team.</p>", "#"),

        ('.CFtools::getIdlangFromiso('it').', "'.(int)$cte_id.'", "modulo-di-contatto", "Modulo di contatto", "#",
        "<p><strong>Muestra</strong> html Texto <em>antes</em> de formulario</p><center>
        <img src=\"'.__PS_BASE_URI__.'img/logo.jpg\"></center>", "<p><strong>Muestra
        </strong> html html texto después de la formulario</p>
        <center><img src=\"'.__PS_BASE_URI__.'img/logo.jpg\"></center>",
        "Administrator", "Contatta PrestaShop", "Contatta PrestaShop notifica", "
        <p>Grazie per la vostra richiesta. Ci sarà presto una risposta alle e-mail
        è sufficiente inviare. Cordiali saluti.<br />
        <br />Team.</p>", "#");');

                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'contactform_item` (
        `fid`, `fields_id`, `fields_name`, `confirmation`,
        `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`,
        `fields_maxtxt`, `fields_require`,
        `order`, `published`) VALUES
        ("'.(int)$cte_id.'", "sep1", "sep1", 0, "none",
         "separator", "", "", "", 250,0, 1, 1);');
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (
                    `fdid`, `id_lang`, `fields_title`,
                    `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en').',
                    "Identification", "", "", "Identification", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').',
                    "Identification", "", "", "Identification", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').',
                    "Identification", "", "", "Identificación", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').',
                    "Identification", "", "", "Identifizierung", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it').',
                    "Identification", "", "", "Identificazione", "", "");'
                );

                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item` (
                    `fid`, `fields_id`, `fields_name`,
                    `confirmation`, `fields_valid`, `fields_type`,`fields_style`, 
                    `err_style`, `fields_suppl`, `fields_maxtxt`,
                    `fields_require`, `order`, `published`) 
                    VALUES('.(int)$cte_id.', "title", "title", 0, "none",
                    "select", "", "", "", 250,1, 1, 1);'
                );
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (
                    `fdid`, `id_lang`, `fields_title`,
                    `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en').',
                    "Title", "", "", ";Mrs;Ms;Mr", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').',
                    "Titre", "", "", ";Mme;Mlle;Mr", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').',
                    "Título", "", "", ";Sra.; El Sr.", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').',
                    "Titel", "", "", "Frau;Fräulein;Herr", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it').',
                    "Titolo", "", "", "Ms.; Miss; Sig.", "", "");'
                );

                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item` (
                    `fid`, `fields_id`, `fields_name`,
                    `confirmation`, `fields_valid`, `fields_type`, `fields_style`, 
                    `err_style`, `fields_suppl`, `fields_maxtxt`,
                    `fields_require`, `order`, `published`) VALUES
                    ("'.(int)$cte_id.'", "name", "name", 0, "none", "text",
                    "", "", "size=\"35\"", 250,1, 2, 1);'
                );
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (
                    `fdid`, `id_lang`, `fields_title`,
                    `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en').',
                    "Your full name", "", "", "Your full name ...", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').',
                    "Votre nom et prenom", "", "", "Votre nom ...", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').',
                    "Su nombre", "", "", "Su nombre ..", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').',
                    "Ihr vollständiger Name", "", "", "Ihr Name ...", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it').',
                    "Il tuo nome completo", "", "", "Il tuo nome completo", "", "");'
                );

                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item` (
                    `fid`, `fields_id`, `fields_name`,
                    `confirmation`, `fields_valid`, `fields_type`, `fields_style`,
                    `err_style`, `fields_suppl`, `fields_maxtxt`,
                    `fields_require`, `order`, `published`) VALUES ('.(int)$cte_id.',
                    "myemail", "myemail", 1, "email", "email", "",
                    "", "size=\"35\"", 250,1, 3, 1);'
                );
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (
                    `fdid`, `id_lang`, `fields_title`,
                    `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en').',
                    "Your e-mail", "", "Confirm your email", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').',
                    "Votre e-mail", "", "Confirmer votre email", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').',
                    "Tu e-mail", "", "Confirme su correo electrónico", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').',
                    "Ihre E-Mail", "", "Bestätigen Sie Ihre E-Mail", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it').', "Il tuo indirizzo e-mail",
                    "", "Conferma la tua email", "", "", "");'
                );

                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'contactform_item` (
        `fid`, `fields_id`, `fields_name`,
        `confirmation`, `fields_valid`, `fields_type`, `fields_style`, `err_style`, `fields_suppl`, `fields_maxtxt`,
        `fields_require`, `order`, `published`) VALUES ("'.(int)$cte_id.'", "sep2", "sep2", 0, "none", "separator", "",
        "", "", 250,0, 4, 1);');
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (
                    `fdid`, `id_lang`, `fields_title`,
                    `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en').',
                    "", "", "", "Additional Informations", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').',
                    "", "", "", "Informations complémentaires", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').',
                    "", "", "", "Información adicional", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').',
                    "", "", "", "Aanvullende informatie", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it').',
                    "", "", "", "Informazioni aggiuntive", "", "");'
                );

                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item` (
                    `fid`, `fields_id`, `fields_name`,
                    `confirmation`, `fields_valid`, `fields_type`, `fields_style`,
                    `err_style`, `fields_suppl`, `fields_maxtxt`,
                    `fields_require`, `order`, `published`) 
                    VALUES ("'.(int)$cte_id.'", "sexe", "sexe", 0, "none", "radio", "",
                    "", "", 250,1, 5, 1);'
                );
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (
                    `fdid`, `id_lang`, `fields_title`,
                    `fields_desc`, `confirmation_txt`, `fields_default`,
                    `error_txt`, `error_txt2`) VALUES
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en').',
                    "Sex", "", "", "Male;Female", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').',
                    "Sexe", "", "", "Homme;Femme", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').',
                    "Sexo", "", "", "Hombre;Mujer", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').',
                    "Sex", "", "", "Männlich;Weiblich", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it"').',
                    "Sesso", "", "", "Maschio;Femmina", "", "");'
                );

                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'contactform_item` (
        `fid`, `fields_id`, `fields_name`,
        `confirmation`, `fields_valid`, `fields_type`, `fields_style`,
        `err_style`, `fields_suppl`, `fields_maxtxt`,
        `fields_require`, `order`, `published`) VALUES("'.(int)$cte_id.'",
        "activity", "activity", 0, "none", "checkbox", "",
        "", "style=\"margin-top:10px;\"", 250,0, 6, 1);');
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (
        `fdid`, `id_lang`, `fields_title`,
        `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES
        ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en').',
        "Business Area", "", "", "Trade;Technology;Agriculture;
        Communication;Computers; Transportation", "", ""),
        ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').', "Secteur d""activité", "", "",
        "Commerce;Technologie;Agriculture;Communication;Informatique;Transport", "", ""),
        ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').', "Área de Negocios", "", "", 
        "Comercio;Tecnología;Agricultura;Comunicaciones;Informática;Transporte", "", ""),
        ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').', "Business Area", "", "", "Handel; 
        Technologie; Landwirtschaft, Kommunikation;Computer; Transporter", "", ""),
        ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it').', "Area di Business", "", "",
        "Commercio; tecnologia;agricoltura;comunicazione; computer;Trasporto", "", "");');

                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'contactform_item` (
        `fid`, `fields_id`, `fields_name`,
        `confirmation`, `fields_valid`, `fields_type`, `fields_style`, 
        `err_style`, `fields_suppl`, `fields_maxtxt`,
        `fields_require`, `order`, `published`) VALUES ("'.(int)$cte_id.'",
        "state", "state", 0, "none", "country", "",
        "", "style=\"width:215px\"", 250,1, 7, 1);');
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (
        `fdid`, `id_lang`, `fields_title`,
        `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES
        ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en').', "State", "", "", "", "", ""),
        ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').', "Pays", "", "", "", "", ""),
        ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').', "País", "", "", "", "", ""),
        ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').', "Land", "", "", "", "", ""),
        ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it').', "Paese", "", "", "", "", "");');

                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'contactform_item` (
        `fid`, `fields_id`, `fields_name`,
        `confirmation`, `fields_valid`, `fields_type`, `fields_style`,
        `err_style`, `fields_suppl`, `fields_maxtxt`,
        `fields_require`, `order`, `published`) VALUES( "'.(int)$cte_id.'",
        "datebirth", "datebirth", 0, "none", "calendar", "",
        "", "", 250,0, 8, 1);');
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (
        `fdid`, `id_lang`, `fields_title`,
        `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES
        ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en').', "Date of birth", "", "", "", "", ""),
        ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').', "Date de naissance", "", "", "", "", ""),
        ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').', "Fecha de Nacimiento", "", "", "", "", ""),
        ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').', "Datum der Geburt", "", "", "", "", ""),
        ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it').', "Data di nascita", "", "", "", "", "");');

                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item` (`fid`, `fields_id`, `fields_name`,
                    `confirmation`, `fields_valid`, `fields_type`, `fields_style`,
                    `err_style`, `fields_suppl`, `fields_maxtxt`,
                    `fields_require`, `order`, `published`) VALUES 
                    ("'.(int)$cte_id.'", "fileup", "fileup", 1, "none", 
                    "fileup", "", "", "", 250,0, 9, 1);'
                );
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (`fdid`, `id_lang`, `fields_title`,
                    `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en').',
                    "File to provide", "", "", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').',
                    "Fichier à fournir", "", "", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').',
                    "Archivo a proveer", "", "", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').',
                    "Bestand om", "", "", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it').',
                    "File da fornire", "", "", "", "", "");'
                );

                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item` (`fid`, `fields_id`, `fields_name`,
                    `confirmation`, `fields_valid`, `fields_type`, `fields_style`,
                    `err_style`, `fields_suppl`, `fields_maxtxt`,
                    `fields_require`, `order`, `published`) 
                    VALUES ('.(int)$cte_id.', "message", "message", 0, "none", "textarea", "",
                    "", "rows=\"8\" cols=\"40\"", 250,1, 10, 1);'
                );
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (`fdid`, `id_lang`, `fields_title`,
                    `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en').',
                    "Message", "", "", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').',
                    "Message", "", "", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').',
                    "Mensaje", "", "", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').',
                    "Nachricht", "", "", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it').',
                    "Messaggio", "", "", "", "", "");'
                );

                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item` (`fid`, `fields_id`, `fields_name`,
                    `confirmation`, `fields_valid`, `fields_type`, `fields_style`,
                    `err_style`, `fields_suppl`, `fields_maxtxt`,
                    `fields_require`, `order`, `published`) 
                    VALUES ("'.(int)$cte_id.'", "captcha", "captcha", 1, "none",
                    "captcha", "", "", "", 250,0, 11, 1);'
                );
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (`fdid`, `id_lang`, `fields_title`,
                    `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en').', 
                    "Verification code", "", "Retape code here", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').',
                    "Code de sécurity", "", "Recopier le code ici", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').',
                    "Código de verificación", "", "Copia el código aquí", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').',
                    "Bestätigungs-Code", "", "Kopieren Sie den Code hier", "", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it').', 
                    "Codice di verifica", "", "Riscrivi il codice qui", "", "", "");'
                );

                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item` (
                    `fid`, `fields_id`, `fields_name`,
                    `confirmation`, `fields_valid`, `fields_type`, `fields_style`,
                    `err_style`, `fields_suppl`,`fields_maxtxt`, `fields_require`, `order`, `published`)
                    VALUES ("'.(int)$cte_id.'", "submit", "submit", 0, "none",
                    "submitbtn", "", "", "", 250,0, 13, 1);'
                );
                $newcte_id = (int)Db::getInstance()->Insert_ID();
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'contactform_item_lang` (
                    `fdid`, `id_lang`, `fields_title`,
                    `fields_desc`, `confirmation_txt`, `fields_default`, `error_txt`, `error_txt2`) VALUES
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('en').',
                    "", "", "", "Send", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('fr').',
                    "", "", "", "Envoyer", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('es').',
                    "", "", "", "Enviar", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('de').',
                    "", "", "", "Senden", "", ""),
                    ('.(int)$newcte_id.', '.CFtools::getIdlangFromiso('it').',
                    "", "", "", "Invia", "", "");'
                );
                break;
        }//End switch
        $contacformactive = Configuration::get('CONTACTFRM_FILENAME');
        $contactfrm = explode('.', $contacformactive);
        $contactfrm = ($contactfrm[0] == 'contact-form') ? 'contact' : $contactfrm[0];
        $alias = DB::getInstance()->getValue(
            'SELECT `alias` FROM `'._DB_PREFIX_.'contactform_lang` WHERE `id_lang`='.
            (int)$ctnx->language->id.' AND `fid`='.(int)$cte_id
        );
        $output = '<div style="border:1px solid #999999; 
        background-color:#B3E1EF; width:99%; margin-bottom:20px; padding:5px">'.
        Translate::getModuleTranslation(
            'contactfrm',
            'Your sample has been saved successfully',
            'contactfrm'
        )
        .'&nbsp;&nbsp;<a target="_blank" class="link" href="'.
        $ctnx->link->getCFormLink($ctnx->shop->id, $cte_id, $alias, $contactfrm, $ctnx->language->id)
        .'">'.
        Translate::getModuleTranslation('contactfrm', 'Preview', 'contactfrm')
        .'</a>>></div>';

        $output .= '<style type="text/css">
                    #grayBack
                    {
                         position: fixed ;
                         top: 0;
                         left: 0;
                         width: 100%;
                         height: 100%;
                         background-color: black;
                         z-index: 1999;
                         opacity: 0.5;
                    }
                    #customPopup
                    {
                         position: fixed;
                         left: 30%;
                         top: 30%;
                         z-index: 2000;
                         padding: 20px;
                         width: 560px;
                         background-color: #EEEEEE;
                         font-size: 12px;
                         line-height: 16px;
                         color: #202020;
                         border : 3px outset #555555;
                    }
                </style>
                <script type="text/javascript">
                    function hidePopup(){
                        document.getElementById("grayBack").style.display="none";
                        document.getElementById("customPopup").style.display="none";
                    }
                </script>
    <div id="grayBack"></div><div id="customPopup">
    <h3><img src="'.$mypath.'views/img/ok2.png" />'.
        Translate::getModuleTranslation('contactfrm', 'Your sample has been 
        saved successfully JS', 'contactfrm').'</h3>
    <p>'.Translate::getModuleTranslation('contactfrm', 'Do you want to see the form?', 'contactfrm')
        .'</p>
    <table style="text-align: center; width: 80%; margin: 10px auto 0px;">
    <tr><td style="text-align: center; padding-right: 5px;"><a style="display: block; 
    background: none repeat scroll 0% 0% rgb(0, 0, 0); color: rgb(255, 255, 255); font-weight: bold; width: 100%;"
    onclick="hidePopup();" href="'.
        $ctnx->link->getCFormLink($ctnx->shop->id, $cte_id, $alias, $contactfrm, $ctnx->language->id).
        '" target="_blank" >'.Translate::getModuleTranslation('contactfrm', 'Yes', 'contactfrm').
        '</a></td><td style="padding-left: 5px"><a onclick="hidePopup();" style="display: block; width: 80%;
    background: none repeat scroll 0% 0% rgb(0, 0, 0); color: rgb(255, 255, 255); 
    font-weight: bold; cursor: pointer; text-align: center;">'.
        Translate::getModuleTranslation('contactfrm', 'No', 'contactfrm').'</a></td></tr>
    </table>
    </div>';
        $output .= self::addsample($mypath, $id_lang);
        return $output;
    }
    public static function validRes($txt)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $output = '<div style="border:1px solid #999999; 
        background-color:#B3E1EF; width:99%; margin-bottom:20px; padding:5px">'.
        Translate::getModuleTranslation('contactfrm', $txt, 'contactfrm').'</div>';
        return $output;
    }

    public static function errFormat($errmsg, $nberr, $shownbr)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $output = '<div style="border:1px solid #999999; 
        background-color:#FFDFDF; width:99%; margin-bottom:20px; padding:5px">';
        if ($shownbr) {
            $output .= '<font color=red>'.Translate::getModuleTranslation(
                'contactfrm',
                'There are',
                'contactfrm'
            )
            .' '.$nberr.' '.
            Translate::getModuleTranslation('contactfrm', 'error(s)', 'contactfrm').'</font>:<br><br>';
        }
        $output .= $errmsg;
        $output .= '</div>';
        return $output;
    }

    public static function seedatadetails($link, $data_id, $id_lang)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $output = '';
    
        $imgy = '<img width="12" height="12" alt="" src="'.$link.'views/img/ok.png">';
        $imgn = '<img width="12" height="12" alt="" src="'.$link.'views/img/del.png">';
    
        $output .= CFtoolsbar::toolbar('seedetails', $link, $id_lang);
    
        $listmsg = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'contactform_data 
        WHERE data_id='.(int)$data_id);
    
        $output .= '<fieldset><legend><img src="'.$link.'logo.gif" alt="" title="" />'.
        Translate::getModuleTranslation('contactfrm', 'Data details', 'contactfrm').' - ID = '.
        $listmsg[0]['data_id'].'</legend>';
        $output .= '<b>'.Translate::getModuleTranslation(
            'contactfrm',
            'Ip address',
            'contactfrm'
        )
        .'</b> = '.$listmsg[0]['ip'].'<br>';
        $tabdate = explode('/', $listmsg[0]['date']);
        if (CFtools::getIsocode($id_lang) == 'fr') {
            $ddate = $tabdate[1].'/'.$tabdate[0].'/'.$tabdate[2];
        } else {
            $ddate = $listmsg[0]['date'];
        }

        $output .= '<b>'.Translate::getModuleTranslation('contactfrm', 'Date', 'contactfrm')
        .'</b> = '.$ddate.'<br>';
        $output .= '<b>'.Translate::getModuleTranslation('contactfrm', 'Mail to', 'contactfrm')
        .'</b> = '.$listmsg[0]['toemail'].'<br>';
        $output .= '<b>'.Translate::getModuleTranslation('contactfrm', 'Mail From', 'contactfrm')
        .'</b> = '.$listmsg[0]['foremail'].'<br>';
        $output .= '<b>'.
        Translate::getModuleTranslation('contactfrm', 'Message sent', 'contactfrm')
        .'</b>
        = <br><blockquote style="margin-left:40px">'.$listmsg[0]['info'].'</blockquote>';

        if ($listmsg[0]['statut_mail'] == 'mail') {
            $output .= '<b>'.Translate::getModuleTranslation('contactfrm', 'Mail Statut', 'contactfrm')
            .'</b> = '.
            $imgy.Translate::getModuleTranslation('contactfrm', 'Mail sent', 'contactfrm').'<br>';
        } else {
            $output .= '<b>'.Translate::getModuleTranslation('contactfrm', 'Mail Statut', 'contactfrm')
            .'</b> = '.
            $imgn.Translate::getModuleTranslation('contactfrm', 'Mail not sent', 'contactfrm').'<br>';
        }

        $output .= '</fieldset>';
        return $output;
    }
    public static function editcss($link, $id_lang)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $output = '';
        $output .= CFtoolsbar::toolbar('classic', $link, $id_lang);
        if (Configuration::get('CONTACTFRM_FORM') == 0) {
            $file = _PS_MODULE_DIR_.'contactfrm/views/css/front/basic.css';
        } else {
            if (Configuration::get('CONTACTFRM_STYLE') == 1) {
                $file = _PS_MODULE_DIR_.'contactfrm/views/css/front/advance.css';
            } else {
                $file = _PS_MODULE_DIR_.'contactfrm/views/css/front/template.css';
            }
        }

        $output .= '<fieldset><legend><img src="'.$link.'logo.gif" alt="" title="" />'.
        Translate::getModuleTranslation('contactfrm', 'Edit Css', 'contactfrm').'</legend>';
        if (file_exists($file)) {
            chmod($file, 0666);
            $fpcss = fopen($file, 'r');
            $output .= '<form method="POST" action="'.$_SERVER['REQUEST_URI'].'">';
            $output .= '<center><textarea name="newcss" cols="140" rows="20">';
            while (!feof($fpcss)) {
                $output .= fgets($fpcss, 1255);
            }
            $output .= '</textarea></center>';
            $output .= '<br><input class="button btn btn-success" type="submit" name="subeditcss" value="'.
            Translate::getModuleTranslation('contactfrm', '   Save   ', 'contactfrm').'">';
            $output .= '</form>';
            fclose($fpcss);
        } else {
            $output .= Translate::getModuleTranslation('contactfrm', 'File  doesn\'t exist', 'contactfrm')
            .':'.$file;
        }
        $output .= '</fieldset>';
        return $output;
    }
    public static function showPreview(
        $title,
        $location,
        $mypath,
        $imgpath,
        $btnname,
        $uploadname,
        $default,
        $width = 32,
        $height = 32,
        $upload = 1,
        $msg_body = ''
    ) {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $dir = opendir($location);
        $output = '';
        
        $output .= '
        <script type="text/javascript">
            var utils3 = setInterval(function(){ 
                if (typeof($) != "undefined") {

                    $(document).ready(function(){
                        //hide the all of the element with class msg_body
                        $(".'.$msg_body.'").hide();
                        //toggle the componenet with class msg_body
                        $(".msg_head").click(function(){
                            $(this).next(".'.$msg_body.'").slideToggle(600);
                        });
                    });

                    clearInterval(utils3);
                }
            }, 500);
        </script>
        <style type="text/css">
        .msg_head {
            padding: 2px 10px;
            cursor: pointer;
            position: relative;
            background-color:#777777;
            background: url('.$mypath.'views/img/down.png) no repeat right;
            margin:1px;
        }
        .'.$msg_body.' {
            padding: 5px 10px 15px;
            background-color:#F4F4F8;
            width:252px;
        }
        </style>
        ';
        $output .= '<div class="form-group">
        <label for="'.$uploadname.'" class="control-label col-lg-2">
        '.Translate::getModuleTranslation('contactfrm', $title, 'contactfrm').'
        </label>
        <div class="col-lg-3">
        <p class="msg_head">'.
            Translate::getModuleTranslation('contactfrm', 'Clic to show', 'contactfrm')
        .'</p>
        <div class="'.$msg_body.'">';
        if ($upload == 1) {
            $output .= '<input type="file" name="'.$uploadname.'" id="'.$uploadname.'" />';
        }
        $compteur = 0;
        $bgname = array(
                        0 => 'bg3',
                        1 => 'bg4',
                        2 => 'bg5',
                        3 => 'bg6',
                        4 => 'coin-droit'
                        );
        while ($file = readdir($dir)) {
            $ttf = explode('.', $file);
            if ($file != '.' && $file != '..' && $ttf[1] && $ttf[1] != 'db' && in_array($ttf[0], $bgname)) {
                $output .= '<span><img width="'.$width.'" style=" margin-bottom:3px" height="'.$height.'" src="'.
                $mypath.$imgpath.$file.'"><input '.($default == $file ? 'checked="checked"' : '').'
                type="radio" name="'.$btnname.'" value="'.$file.'"></span>';
            }
            $compteur++;
        }
        $output .= '</div></div>
        </div>';
        return $output;
    }

    public static function showfont(
        $title,
        $location,
        $mypath,
        $btnname,
        $uploadname,
        $default,
        $upload = 1,
        $msg_body = ''
    ) {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $dir = opendir($location);
        $output = '';
        $output .= '
        <script type="text/javascript">
        var utils4 = setInterval(function(){ 
            if (typeof($) != "undefined") {

                $(document).ready(function(){
                    //hide the all of the element with class msg_body
                    $(".'.$msg_body.'").hide();
                    //toggle the componenet with class msg_body
                    $(".msg_head").click(function(){
                        $(this).next(".'.$msg_body.'").slideToggle(600);
                    });
                });

                clearInterval(utils4);
            }
        }, 500);

        </script>
        <style type="text/css">
        .msg_head {
            padding: 2px 10px;
            cursor: pointer;
            position: relative;
            background-color:#777777;
            background: url('.$mypath.'views/img/down.png) no repeat right;
            margin:1px;
        }
        .'.$msg_body.' {
            padding: 5px 10px 15px;
            background-color:#F4F4F8;
            width:252px;
        }
        </style>
        ';
        $output .= '<div class="form-group">
        <label for="'.$uploadname.'" class="control-label col-lg-2">
        '.Translate::getModuleTranslation('contactfrm', $title, 'contactfrm').'
        </label>
        <div class="col-lg-3">
        <p class="msg_head">'.
        Translate::getModuleTranslation('contactfrm', 'Clic to show', 'contactfrm').'</p>
        <div class="'.$msg_body.'">';
        if ($upload == 1) {
            $output .= '<input type="file" name="'.$uploadname.'" id="'.$uploadname.'" />';
        }
        $compteur = 0;
        while ($file = readdir($dir)) {
            $ttf = explode('.', $file);
            if ($file != '.' && $file != '..' && $ttf[1] != 'db' && $file != 'index.php') {
                $output .= '<span>'.$file.'"<input '.($default == $file ? 'checked="checked"' : '').
                '  type="radio" name="'.$btnname.'" value="'.$file.'"></span>&nbsp;&nbsp;';
            }
            $compteur++;
        }
        $output .= '</div></div></div>';
        return $output;
    }
    public static function uploadimgFile($content_dir, $uploadname, $var_toup, $var_get, $format)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $error = 0;
        //UPDATE ARROW IMAGE
        if (!empty($_FILES[$uploadname]['tmp_name'])) {
            //$content_dir = dirname(__FILE__).'/themes/vertical/images/arrows/';
           // dossier où sera déplacé le fichier
            $tmp_file = $_FILES[$uploadname]['tmp_name'];
            $name_file = $_FILES[$uploadname]['name'];
            if ($format == 1) {
                $format = array('jpg','png','gif');
            } elseif ($format == 2) {
                $format = array('ttf');
            }
            $ext = explode('.', $name_file);
            $maxindex = count($ext) - 1;
            if (!is_uploaded_file($tmp_file)) {
                $error = 1;
            }
            if ($error == 0) {
                if (!in_array($ext[$maxindex], $format)) {
                    $error = 2;
                }
            }
            if ($error == 0) {
                if (!move_uploaded_file($tmp_file, $content_dir.$name_file)) {
                    $error = 3;
                }
            }
            if ($error == 0) {
                Configuration::updateValue($var_toup, $name_file);
            }
            return $error;
        } else {
            Configuration::updateValue($var_toup, $var_get);
        }
    }
    public static function iswriteimgDir($directory, $mypath)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        if (is_writable($directory)) {
            return '<div class="conf col-lg-12"><img src="'.$mypath.'views/img/ok2.png">'.
            Translate::getModuleTranslation(
                'contactfrm',
                'Directory writable',
                'contactfrm'
            )
            .': '.$directory.'</div>';
        } else {
            return '<div class="error col-lg-12"><img src="'.$mypath.'views/img/error.png">'.
            Translate::getModuleTranslation(
                'contactfrm',
                'Unable to write to the directory',
                'contactfrm'
            )
            .': '.$directory.'</div>';
        }
    }
    public static function DS()
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $ds = DIRECTORY_SEPARATOR;
        if (!empty($ds) && $ds != '') {
            return DIRECTORY_SEPARATOR;
        } else {
            return '/';
        }
    }

    public static function newsliderline2($title, $fieldname, $default, $id, $max, $min)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $var = $id.'x';
        $output = '<div class="form-group">
        <label for="h'.$id.'-value" class="control-label col-lg-2">
        '.Translate::getModuleTranslation('contactfrm', $title, 'contactfrm').'
        </label>
        <div class="col-lg-3">
        <div class="slider" id="slider-'.
        $id.'" tabIndex="1"><input class="slider-input" id="slider-input-'.$id.'"/></div>
        <input class="slider-input" size=6 type="text" onchange="a.setValue(parseInt(this.value))"
        name="'.$fieldname.'" id="h'.$id.'-value" value="'.
        Configuration::get('CONTACTFRM_CAPTCHAHEIGHT').'" />
        </div>
        </div>';

        $output .= '<script type="text/javascript">

        var '.$var.' = new Slider(document.getElementById("slider-'.$id.'"),
        document.getElementById("slider-input-'.$id.'"));
        '.$var.'.onchange = function () {
            document.getElementById("h'.$id.'-value").value = '.$var.'.getValue();
        };
        '.$var.'.setValue('.$default.');
        '.$var.'.setMaximum('.$max.');
        '.$var.'.setMinimum('.$min.');
        window.onresize = function () {
            '.$var.'.recalculate();
        };
        </script>';
        return $output;
    }
    public static function useSlider($mypath)
    {
        $output = '<link rel="stylesheet" type="text/css" href="'.$mypath.'views/css/luna.css" />';
        $output .= '<script type="text/javascript" src="'.$mypath.'views/js/slider/range.js"></script>';
        $output .= '<script type="text/javascript" src="'.$mypath.'views/js/slider/slider.js"></script>';
        $output .= '<script type="text/javascript" src="'.$mypath.'views/js/slider/timer.js"></script>';
        return $output;
    }
}
