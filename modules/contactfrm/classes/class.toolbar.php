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

class CFtoolsbar
{
    public static function getPreviewLink($fid, $id_lang)
    {

        $cntx = Context::getContext();

        if ($fid != 0) {
            $getform = Db::getInstance()->ExecuteS(
                'SELECT cf.`email`,cf.`formname`, cfl.*, shp.name, shp.id_shop
                FROM `'._DB_PREFIX_.'contactform` cf 
                LEFT JOIN `'._DB_PREFIX_.'contactform_lang` cfl
                ON cf.`fid` = cfl.`fid` 
                LEFT JOIN `'._DB_PREFIX_.'shop` shp 
                ON shp.`id_shop` = cf.`id_shop`
                WHERE cfl.`id_lang`='.(int)$id_lang.' AND cf.`fid`='.(int)$fid.'
                ORDER BY cf.`fid`'
            );
            $contacform_active = Configuration::get('CONTACTFRM_FILENAME');
            $contactfrm = explode('.', $contacform_active);
            $contactfrm = ($contactfrm[0] == 'contact-form') ? 'contact' : $contactfrm[0];

            $PreviewLink = $cntx->link->getCFormLink(
                $getform[0]['id_shop'],
                $getform[0]['fid'],
                $getform[0]['alias'],
                $contactfrm,
                $getform[0]['id_lang']
            );

            return $PreviewLink;
        }

        return $cntx->link->getPageLink('cform').'&fid='.$fid;
    }

    public static function toolbar($type, $mypath, $id_lang)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $mytoken = Tools::getValue('token');
        $fid = (int)Tools::getValue('fid');
        $output = DatatExport::includFiles($mypath, $id_lang);
        $output .= '<style type="text/css">
                    table tr th a,table tr th{
                        text-decoration: none !important;
                        font-weight: bold;
                        color:#000000;
                    }    
                    table tr td a,table tr td{
                        text-decoration: none !important;    
                        color:#000000;
                    }
                    #cftoolbar tr td a{color: #FFF; text-decoration: none;}
                    #form table tr td{padding:5px;}
                    a{text-decoration: none;}
                </style>';
        switch ($type) {
            case 'showform':
                $size = '75%';
                break;
            case 'editform':
                if ($fid != 0) {
                    $size = '55%';
                } else {
                    $size = '85%';
                }
                break;
            case 'restoreform':
                $size = '95%';
                break;
            case 'settings':
                $size = '60%';
                break;
            case 'classic':
                $size = '95%';
                break;
            case 'seedata':
                $size = '85%';
                break;
            case 'seedetails':
                $size = '95%';
                break;
            default:
                $size = '60%';
                break;
        }
        $output .= '<div class="bootstrap">
                <div class="panel" style="background: none 
                repeat scroll 0px 0px rgb(0, 175, 240);">
                <table id="cftoolbar" width=100%">
                <tr align="center">
                <td width="'.$size.'" align="left">
                '.self::barHomepage($mypath).
                '</td>';

        switch ($type) {
                    //-----------------------Settings---------------------------------
            case 'settings':
                $output .= '
    
                <td>
                <a href="index.php?tab=AdminModules&configure=contactfrm&token='.
                $mytoken.'&task=editcss"><i class="icon-paint-brush icon-3x"></i></a>
                </td>';
                $output .= '    <td>
                <a href="index.php?tab=AdminModules&configure=contactfrm&token='.$mytoken.'">
                <i class="icon-times-circle icon-3x"></i></a></td>
                </tr><tr align="center"><td></td>';
                $output .= '<td>
                <a href="index.php?tab=AdminModules&configure=contactfrm&token='.
                $mytoken.'&task=editcss">'.
                Translate::getModuleTranslation('contactfrm', 'Edit Css', 'contactfrm')
                .'</a></td>';
                $output .= '<td><a title="'.
                Translate::getModuleTranslation('contactfrm', 'Close', 'contactfrm').
                '" href="index.php?tab=AdminModules&configure=contactfrm&token='.$mytoken.'">
                '.Translate::getModuleTranslation('contactfrm', 'Close', 'contactfrm').'</a></td>
                </tr>';
                break;
                    //-------------------------------Show form --------------------------
            case 'showform':
                $output .= '    <td align="center">
                <a href="index.php?tab=AdminModules&configure=contactfrm&task=editform&token='.$mytoken.'">
                            <i class="icon-plus-circle icon-3x"></i></a></td>
                <td align="center" style="text-align: center;">
                <a href="index.php?tab=AdminModules&configure=contactfrm&task=formrelation&token='.
                        $mytoken.'">
                <i class="icon-compress icon-3x"></i></a></td>
                <td align="center"><a href="index.php?tab=AdminModules&configure=contactfrm&token='.
                        $mytoken.'">
                <i class="icon-times-circle icon-3x"></i></a></td>
                </tr>
                <tr >
                    <td></td>
                    <td align="center">
                        <a href="index.php?tab=AdminModules&configure=contactfrm&task=editform&token='.$mytoken.'">'.
                        Translate::getModuleTranslation('contactfrm', 'New form', 'contactfrm')
                            .'</a></td>
                    <td align="center" style="text-align: center;">
                    <a href="index.php?tab=AdminModules&configure=contactfrm&task=formrelation&token='.$mytoken.'">'.
                        Translate::getModuleTranslation('contactfrm', 'Relation between form', 'contactfrm').'</a></td>
                    <td align="center"><a href="index.php?tab=AdminModules&configure=contactfrm&token='.
                        $mytoken.'">'.Translate::getModuleTranslation('contactfrm', 'Close', 'contactfrm').'</a></td>
                </tr>';
                break;
                    //---------------------------------------Relation form ---------------------------------
            case 'formrelation':
                $output .= '    <td align="center">
                <a href="index.php?tab=AdminModules&configure=contactfrm&task=createrelation&token='.$mytoken.'">
                            <i class="icon-plus-circle icon-3x"></i></a></td>
                <td align="center" style="text-align: center;" >
                <a href="index.php?tab=AdminModules&configure=contactfrm&task=formrelation&token='.$mytoken.'">
                <i class="icon-compress icon-3x"></i></a></td>
                <td align="center"><a href="index.php?tab=AdminModules&configure=contactfrm&token='
                .$mytoken.'&task=showformList">
                <i class="icon-times-circle icon-3x"></i></a></td>
                </tr>
                <tr >
                    <td></td>
                    <td align="center">
                    <a href="index.php?tab=AdminModules&configure=contactfrm&task=createrelation&token='.$mytoken.'">'.
                            Translate::getModuleTranslation('contactfrm', 'Create', 'contactfrm')
                            .'</a></td>
                    <td align="center" style="text-align: center;">
                    <a href="index.php?tab=AdminModules&configure=contactfrm&task=formrelation&token='.$mytoken.'">'.
                            Translate::getModuleTranslation('contactfrm', 'Relation between form', 'contactfrm')
                            .'</a></td>
                    <td align="center">
                    <a href="index.php?tab=AdminModules&configure=contactfrm&token='.$mytoken.'&task=showformList">'.
                            Translate::getModuleTranslation('contactfrm', 'Close', 'contactfrm')
                            .'</a></td>
                </tr>';
                break;
                    //----------------------End relation form -----------------
            case 'relviewelm':
                    $output .= '<link rel="stylesheet" type="text/css" href="'.$mypath.'views/css/isocraprint.css" />';
                    $output .= '<script src="'.$mypath.'views/js/dragdrop/jquery.js" type="text/javascript"></script>';
                    $output .= '    <td align="center">&nbsp;</td>
                <td align="center">&nbsp;</td>
                <td align="center"><a href="index.php?tab=AdminModules&configure=contactfrm&token='.
                $mytoken.'&task=formrelation"><i class="icon-times-circle icon-3x"></i></a></td>
                </tr>
                <tr >
                    <td></td>
                    <td align="center">&nbsp;</td>
                    <td align="center">&nbsp;</td>
                    <td align="center">
                    <a href="index.php?tab=AdminModules&configure=contactfrm&token='.$mytoken.'&task=formrelation">'.
                            Translate::getModuleTranslation('contactfrm', 'Close', 'contactfrm')
                            .'</a></td>
                </tr>';
                break;
                    //------------------------End viewelement relation --------------------
            case 'editform':
                if ($fid != 0) {
                    $output .= '<td>
                        <a href="index.php?tab=AdminModules&configure=contactfrm&token='.
                            $mytoken.'&task=addfield&fid='.$fid.'">
                        <i class="icon-plus-circle icon-3x"></i>
                        </a>
                        </td>
                        <td>
                        <a href="index.php?tab=AdminModules&configure=contactfrm&token='.
                            $mytoken.'&task=showfieldList&fid='.$fid.'"><i class="icon-th-list icon-3x"></i></a>
                        </td>
                        <td>
                        <a target="_blank" href="'.self::getPreviewLink($fid, $id_lang).'">
                        <i class="icon-eye icon-3x"></i></a>
                        </td>';
                }
                $output .= '    
                <td><a href="index.php?tab=AdminModules&configure=contactfrm&task=showformList&token='.
                $mytoken.'"><i class="icon-list-alt icon-3x"></i></a></td>
                <td><a href="index.php?tab=AdminModules&configure=contactfrm&task=showformList&token='.
                $mytoken.'"><i class="icon-times-circle icon-3x"></i></a></td>
                </tr>
                <tr align="center">
                    <td></td>';
                if ($fid != 0) {
                    $output .= '
                <td>
            <a href="index.php?tab=AdminModules&configure=contactfrm&token='.$mytoken.'&task=addfield&fid='.$fid.'">'.
                                Translate::getModuleTranslation('contactfrm', 'New field', 'contactfrm').'</a></td>
                        <td><a href="index.php?tab=AdminModules&configure=contactfrm&token='.$mytoken.
                                '&task=showfieldList&fid='.$fid.'">'.
                                Translate::getModuleTranslation('contactfrm', 'List Fields', 'contactfrm').'</a></td>
                        <td><a target="_blank" href="'.self::getPreviewLink($fid, $id_lang).'">'.
                                Translate::getModuleTranslation('contactfrm', 'Preview', 'contactfrm').'</a></td>';
                }

                $output .= '    
                <td><a href="index.php?tab=AdminModules&configure=contactfrm&token='.
                $mytoken.'&task=showformList">'.
                Translate::getModuleTranslation('contactfrm', 'List Form', 'contactfrm').'</a></td>
                <td><a href="index.php?tab=AdminModules&configure=contactfrm&token='.
                $mytoken.'&task=showformList">'.
                Translate::getModuleTranslation('contactfrm', 'Close', 'contactfrm').'</a></td>
                </tr>';
                break;
            case 'exportform':
                $output .= '<td>
                <input type="image" name="subSavesql" src="'.$mypath.'views/img/save.png">
                    </td>
                    <td>
                    <a href="index.php?tab=AdminModules&configure=contactfrm&token='.
                        $mytoken.'&task=saveSql"><i class="icon-floppy-o icon-3x"></i></a>
                    </td>
                    
                    ';
                $output .= '    <td><a href="index.php?tab=AdminModules&configure=contactfrm&token='.
                $mytoken.'"><i class="icon-times-circle icon-3x"></i></td>
                </tr>
                <tr align="center">
                    <td></td>
                
                    <td>'.Translate::getModuleTranslation('contactfrm', '   Save   ', 'contactfrm').'</td>
                    <td><a href="index.php?tab=AdminModules&configure=contactfrm&token='.
                        $mytoken.'&task=saveSql">'.
                        Translate::getModuleTranslation('contactfrm', 'Backup Alternative', 'contactfrm')
                        .'</a></td>';
                        $output .= '<td><a href="index.php?tab=AdminModules&configure=contactfrm&token='.
                        $mytoken.'">'.Translate::getModuleTranslation('contactfrm', 'Close', 'contactfrm').'</a></td>
                </tr>';
                break;
            case 'restoreform':
                $output .= '    <td>
                <a title="'.Translate::getModuleTranslation('contactfrm', 'Close', 'contactfrm').
                '" href="index.php?tab=AdminModules&configure=contactfrm&token='.$mytoken.'">
                <i class="icon-times-circle icon-3x"></i></a></td>
                </tr>
                <tr align="center">
                    <td></td>';
                        $output .= '<td><a title="'.
                        Translate::getModuleTranslation('contactfrm', 'Close', 'contactfrm').'" href="
                index.php?tab=AdminModules&configure=contactfrm&token='.$mytoken.'">'.
                        Translate::getModuleTranslation('contactfrm', 'Close', 'contactfrm').'</a></td>
                </tr>';
                break;
            case 'classic':
                $output .= '    <td>
                <a title="'.Translate::getModuleTranslation('contactfrm', 'Close', 'contactfrm').'" href=
                "index.php?tab=AdminModules&configure=contactfrm&token='.$mytoken.'">
                <i class="icon-times-circle icon-3x"></i></a></td>
                </tr>
                <tr align="center">
                    <td></td>';
                $output .= '<td><a title="'.Translate::getModuleTranslation('contactfrm', 'Close', 'contactfrm').
                '" href="index.php?tab=AdminModules&configure=contactfrm&token='.
                $mytoken.'">'.Translate::getModuleTranslation('contactfrm', 'Close', 'contactfrm').'</a></td>
                </tr>';
                break;
            case 'seedata':
                $output .= '
                <td> <a href="#" id="try-1" ><i class="icon-download icon-3x"></i></a></td>
                <td><a title="'.Translate::getModuleTranslation('contactfrm', 'Close', 'contactfrm').
                '" href="index.php?tab=AdminModules&configure=contactfrm&token='.
                $mytoken.'"><i class="icon-times-circle icon-3x"></i></a></td>
                </tr>
                <tr align="center">
                    <td></td>
                    <td><a href="#" id="try-2" class="">'.
                        Translate::getModuleTranslation('contactfrm', 'Export', 'contactfrm').'</a></td>';
                        $output .= '<td><a title="'.
                        Translate::getModuleTranslation('contactfrm', 'Close', 'contactfrm').
                        '" href="index.php?tab=AdminModules&configure=contactfrm&token='.
                        $mytoken.'">'.
                        Translate::getModuleTranslation('contactfrm', 'Close', 'contactfrm').'</a></td>
                </tr>';
                $cflang = CFtools::getIsocode($id_lang);
                if ($cflang) {
                    $isoname = $cflang;
                } else {
                    $isoname = 'en';
                }
                if ($isoname == 'en') {
                    $dateformat = 'd/m/Y';
                } else {
                    $dateformat = 'Y/m/d';
                }
                $output .= '<div id="sign_up">
                <h3>'.Translate::getModuleTranslation('contactfrm', 'Export your data', 'contactfrm').'</h3>
                <span>'.
                Translate::getModuleTranslation(
                    'contactfrm',
                    'Please, fill the form below',
                    'contactfrm'
                )
                .'</span>
                <form method="POST" action="'.$_SERVER['REQUEST_URI'].'">
                <div id="sign_up_form">
                        
                        <ul class="info_export">
                        <li>
                            <p>'.Translate::getModuleTranslation('contactfrm', 'Format', 'contactfrm').':</p>
                            <select name="format">
                                <option value="xls">Excel xls</option>
                                <option selected="selected" value="csv">CSV</option>
                            </select>
                            '.Translate::getModuleTranslation('contactfrm', 'Csv separator', 'contactfrm')
                            .' : <input type="text" size="1" name="separator" value=";">
                        </li>
                        <li>
                            <div>
                            <p>formulaire:</p>
                            <select name="formid">
                            <option value="0" selected="selected">'.
                            Translate::getModuleTranslation('contactfrm', 'All', 'contactfrm')
                            .'</option>'.DatatExport::getFormList().'</select></div>
                        <div id="multi">
                        <p>'.Translate::getModuleTranslation('contactfrm', 'Start date', 'contactfrm')
                        .':</p>    <input type="text" name="dateA"/><br />
                            
                        <p>'.Translate::getModuleTranslation('contactfrm', 'End date', 'contactfrm')
                        .':</p>    <input type="text" name="dateB"/></button>
                        </div>
                        </li>
                        </ul>
                        <input type="hidden" value="'.$dateformat.'" name="dateformat">
                        <input type="hidden" value="'.$isoname.'" name="isoname">
                    <input type="submit" name="exportalldata" value="'.
                    Translate::getModuleTranslation('contactfrm', 'Export', 'contactfrm').'">    
                  </form>  
                </div>
                <a id="close_x" class="close sprited" href="#" onclick="closePopup()">close</a>
            </div>';
                break;
            case 'seedetails':
                        $output .= '    <td><a title="'.
                        Translate::getModuleTranslation('contactfrm', 'Close', 'contactfrm').
                        '" href="index.php?tab=AdminModules&configure=contactfrm&token='.
                        $mytoken.'&task=seedata"><i class="icon-times-circle icon-3x"></i></a></td>
        </tr>
        <tr align="center">
            <td></td>';
                $output .= '<td><a title="'.
                Translate::getModuleTranslation('contactfrm', 'Close', 'contactfrm').
                '" href="index.php?tab=AdminModules&configure=contactfrm&token='.
                $mytoken.'&task=seedata">'.
                Translate::getModuleTranslation('contactfrm', 'Close', 'contactfrm').'</a></td>
        </tr>';
                break;
            default:
                break;
        }
        $output .= '</table>        ';
        $output .= '</div></div><br>
        <style type="text/css">
            .icon-3x {
                font-size: 3em;
            }
        </style>';
        return $output;
    }
    public static function barHomepage($link)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $mytoken = Tools::getValue('token');
        $url = 'index.php?tab=AdminModules&configure=contactfrm&token='.$mytoken;
        $output = '<link rel="stylesheet" type="text/css" 
        href="'.$link.'views/css/anylinkcssmenu.css" />';
        $output .= '<script type="text/javascript" 
        src="'.$link.'views/js/homemenu/anylinkcssmenu.js" /></script>';
        $output .= '
    <script type="text/javascript">
    //anylinkcssmenu.init("menu_anchors_class") 
    ////Pass in the CSS class of anchor links (that contain a sub menu)
    anylinkcssmenu.init("anchorclass")
    </script>
    <a href="index.php?tab=AdminModules&configure=contactfrm&token='.$mytoken.'" >
    <i class="icon-home icon-5x"></i></a>
    
    <a href="index.php?tab=AdminModules&configure=contactfrm&token='.$mytoken.'"
    class="anchorclass myownclass" rel="submenu3">
    <i class="icon-th-large icon-5x"></i></a>
                                                        
    <div id="submenu3" class="anylinkcsscols">';
        $output .= '<table class="homepage" border="1">';
        $output .= '<tr>';
        $output .= '<td>';
        $output .= '<table class="homepage1" border="1" >';
        $output .= '<tr>';
            $output .= '<td><a href="'.$url.'&task=showformList"><i class="icon-book icon-3x"></i><br /><br />'.
                            Translate::getModuleTranslation('contactfrm', 'Managing your form', 'contactfrm')
                            .'</a></td>';
            $output .= '<td><a href="'.$url.'&task=seedata">
            <i class="icon-archive icon-3x"></i><br /><br />'.
            Translate::getModuleTranslation('contactfrm', 'See data', 'contactfrm').'</a></td>';
            $output .= '<td><a href="'.$url.'&task=addsample">
            <i class="icon-database icon-3x"></i><br /><br />'.
            Translate::getModuleTranslation('contactfrm', 'Add sample data', 'contactfrm').'</a></td>';
        $output .= '</tr>';
        $output .= '<tr>';
            $output .= '<td><a href="'.$url.'&task=exportForm">
            <i class="icon-floppy-o icon-3x"></i><br /><br />'.
            Translate::getModuleTranslation('contactfrm', 'Save your form', 'contactfrm')
            .'</a></td>';
            $output .= '<td><a href="'.$url.'&task=restoreForm">
            <i class="icon-recycle icon-3x"></i><br /><br />'.
            Translate::getModuleTranslation('contactfrm', 'Restore your Form', 'contactfrm')
            .'</a></td>';
            $output .= '<td><a href="'.$url.'&task=settings">
            <i class="icon-cogs icon-3x"></i><br /><br />'.
            Translate::getModuleTranslation('contactfrm', 'Settings', 'contactfrm')
            .'</a></td>';
        $output .= '</tr>';
        $output .= '</table>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '</table>';
        $output .= '    </div></div>
        <style type"text/css">
        .icon-5x{
            font-size: 5em !important;
        }
        .myownclass .icon-th-large.icon-5x {
            margin-left: 40px;
        }
        </style>
        ';
        return $output;
    }
}
