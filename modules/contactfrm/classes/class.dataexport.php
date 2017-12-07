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

class DatatExport
{
    public static function includFiles($mypath, $id_lang)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $cflang = CFtools::getIsocode($id_lang);

        if ($cflang) {
            $isoname = $cflang;
        } else {
            $isoname = 'en';
        }
        if ($isoname == 'fr') {
            $dateformat = 'd/m/Y';
        } else {
            $dateformat = 'Y/m/d';
        }
        $output = '<link rel="stylesheet" type="text/css" href="'.
        $mypath.'views/css/styles.css" />';
        $output .= '<script type="text/javascript" src="'.
        $mypath.'views/js/modal/jquery.lightbox_me.js"></script>
         <script type="text/javascript" src="'.
        $mypath.'views/js/jquery.datetimepicker.js"></script>
        
        <link rel="stylesheet" type="text/css" media="all" href="'.
        $mypath.'views/css/jquery.datetimepicker.css"  />
        
         <script type="text/javascript" charset="utf-8">
        var dataexport1 = setInterval(function(){ 
            if (typeof($) != "undefined") {

                $(function() {
                    function launch() {
                         $("#sign_up").lightbox_me({centered: true, onLoad: function() 
                         { $("#sign_up").find("input:first").focus()}});
                    }
                    
                    $("#try-1").click(function(e) {
                        $("#sign_up").lightbox_me({centered: true, onLoad: function() {
                            $("#sign_up").find("input:first").focus();
                        }});
                        
                        e.preventDefault();
                    });
                    
                    $("#try-2").click(function(e) {
                        $("#sign_up").lightbox_me({centered: true, onLoad: function() {
                            $("#sign_up").find("input:first").focus();
                        }});
                        
                        e.preventDefault();
                    });
                    
                });
                
                function closePopup(){
                    $("#sign_up").hide();
                    $(".lb_overlay").hide();
                }
        
                clearInterval(dataexport1);
            }
        }, 500);
    </script>
    
            <script type="text/javascript">
                var dataexport2 = setInterval(function(){ 
                    if (typeof(jQuery) != "undefined") {
                        
                        jQuery(document).ready(function() { 
                            jQuery("#multi input").datetimepicker({
                                lang:"'.$cflang.'",
                                format:"'.$dateformat.'",
                                formatDate:"'.$dateformat.'",
                                timepicker:false
                            });
                        });

                        clearInterval(dataexport2);
                    }
                }, 500);
            </script>
         ';
        return $output;
    }
    public static function datetoTime($date, $isoname)
    {
        if ($isoname == 'fr') {
            list($day, $month, $year) = explode('/', $date);
        } else {
            list($year, $month, $day) = explode('/', $date);
        }
        $timestamp = mktime(0, 0, 0, $month, $day, $year);
        return $timestamp;
    }
    public static function exportxls($date1, $date2, $isoname)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=data'.time().'.xls');
        if ($date1 != '' && $date2 != '') {
            $timestamp1 = self::datetoTime($date1, $isoname);
            $timestamp2 = self::datetoTime($date2, $isoname);
            $data_lists = Db::getInstance()->ExecuteS(
                'SELECT * FROM `'._DB_PREFIX_.
                'contactform_data` WHERE `comment` >='.(int)$timestamp1.
                ' AND `comment` <='.(int)$timestamp2
            );
        } elseif ($date1 != '' && $date2 == '') {
            $timestamp1 = self::datetoTime($date1, $isoname);
            $data_lists = Db::getInstance()->ExecuteS('SELECT * 
            FROM `'._DB_PREFIX_.'contactform_data` WHERE `comment` >='.(int)$timestamp1);
        } elseif ($date1 == '' && $date2 != '') {
            $timestamp2 = self::datetoTime($date2, $isoname);
            $data_lists = Db::getInstance()->ExecuteS('SELECT * 
            FROM `'._DB_PREFIX_.'contactform_data` WHERE `comment` <='.(int)$timestamp2);
        } else {
            $data_lists = Db::getInstance()->ExecuteS('SELECT *
            FROM `'._DB_PREFIX_.'contactform_data`');
        }
        $output = '<table width="100%" class="table" cellspacing="0"
        cellpadding="0" style="border: 1px solid #000">';
        $output .= '<tr style="border: 1px solid #000">
                        <th width="5%">ID</th>
                        <th width="5%">'.
                        Translate::getModuleTranslation('contactfrm', 'Ip address', 'contactfrm').'</th>
                        <th width="5%">'.
                        Translate::getModuleTranslation('contactfrm', 'Date', 'contactfrm').'</th>
                        <th width="5%">'.
                        Translate::getModuleTranslation('contactfrm', 'Email to', 'contactfrm').'</th>
                        <th>'.Translate::getModuleTranslation('contactfrm', 'Email from', 'contactfrm').'</a></th>
                        <th>'.Translate::getModuleTranslation('contactfrm', 'Message sent', 'contactfrm').'</a></th>
                 </tr>';

        foreach ($data_lists as $data_list) {
            $output .= '<tr align="center" valign="top" style="border: 1px solid #000">';

                        $output .= '<td>'.$data_list['data_id'].'</td>';
                        $output .= '<td>'.$data_list['ip'].'</td>';
                        $output .= '<td>'.$data_list['date'].'</td>';
                        $output .= '<td>'.$data_list['toemail'].'</td>';
                        $output .= '<td>'.$data_list['foremail'].'</td>';
                        $output .= '<td align="left">'.str_replace(
                            '<br>',
                            '<br style="mso-data-placement:same-cell;" />',
                            $data_list['info']
                        )
                        .'</td>';
            $output .= '</tr>';
        }
        $output .= '</table>';
        $output = utf8_decode($output);
        ob_clean();
        flush();
        print $output;
        exit;
    }
    public static function exportxlsx($date1, $date2, $isoname)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename=data'.time().'.xlsx');
        if ($date1 != '' && $date2 != '') {
            $timestamp1 = self::datetoTime($date1, $isoname);
            $timestamp2 = self::datetoTime($date2, $isoname);
            $data_lists = Db::getInstance()->ExecuteS(
                'SELECT * FROM `'._DB_PREFIX_.
                'contactform_data` WHERE `comment` >='.(int)$timestamp1.
                ' AND `comment` <='.(int)$timestamp2
            );
        } elseif ($date1 != '' && $date2 == '') {
            $timestamp1 = self::datetoTime($date1, $isoname);
            $data_lists = Db::getInstance()->ExecuteS('SELECT * 
            FROM `'._DB_PREFIX_.'contactform_data` WHERE `comment` >='.(int)$timestamp1);
        } elseif ($date1 == '' && $date2 != '') {
            $timestamp2 = self::datetoTime($date2, $isoname);
            $data_lists = Db::getInstance()->ExecuteS('SELECT * 
            FROM `'._DB_PREFIX_.'contactform_data` WHERE `comment` <='.(int)$timestamp2);
        } else {
            $data_lists = Db::getInstance()->ExecuteS('SELECT *
            FROM `'._DB_PREFIX_.'contactform_data`');
        }
        $output = '<table width="100%" class="table" cellspacing="0"
        cellpadding="0" style="border: 1px solid #000">';
        $output .= '<tr style="border: 1px solid #000">
                        <th width="5%">ID</th>
                        <th width="5%">'.
                        Translate::getModuleTranslation('contactfrm', 'Ip address', 'contactfrm').'</th>
                        <th width="5%">'.
                        Translate::getModuleTranslation('contactfrm', 'Date', 'contactfrm').'</th>
                        <th width="5%">'.
                        Translate::getModuleTranslation('contactfrm', 'Email to', 'contactfrm').'</th>
                        <th>'.
                        Translate::getModuleTranslation('contactfrm', 'Email from', 'contactfrm').'</a></th>
                        <th>'.
                        Translate::getModuleTranslation('contactfrm', 'Message sent', 'contactfrm').'</a></th>
                 </tr>';

        foreach ($data_lists as $data_list) {
            $output .= '<tr align="center" valign="top" style="border: 1px solid #000">';
            $output .= '<td>'.$data_list['data_id'].'</td>';
            $output .= '<td>'.$data_list['ip'].'</td>';
            $output .= '<td>'.$data_list['date'].'</td>';
            $output .= '<td>'.$data_list['toemail'].'</td>';
            $output .= '<td>'.$data_list['foremail'].'</td>';
            $output .= '<td align="left">'.
            str_replace(
                '<br>',
                '<br style="mso-data-placement:same-cell;" />',
                $data_list['info']
            )
            .'</td>';
            $output .= '</tr>';
        }
        $output .= '</table>';
        $output = utf8_decode($output);
        ob_clean();
        flush();
        print $output;
        exit;
    }
    public static function exportcsv($date1, $date2, $isoname, $separator, $formid)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        header('Content-type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-disposition: attachment; filename=data'.time().'.csv');
        if ($formid != 0) {
            $form_request = ' AND `fid` = '.$formid;
            $form_request_else = ' WHERE `fid` = '.$formid;
        } else {
            $form_request = '';
            $form_request_else = '';
        }
        if (!$separator || $separator == '') {
            $separator = ';';
        }
        $csv_output = '';
        if ($date1 != '' && $date2 != '') {
            $timestamp1 = self::datetoTime($date1, $isoname);
            $timestamp2 = self::datetoTime($date2, $isoname);
            $data_lists = Db::getInstance()->ExecuteS(
                'SELECT * FROM `'._DB_PREFIX_.
                'contactform_data` WHERE `comment` >='.(int)$timestamp1.
                ' AND `comment` <='.(int)$timestamp2.$form_request
            );
        } elseif ($date1 != '' && $date2 == '') {
            $timestamp1 = self::datetoTime($date1, $isoname);
            $data_lists = Db::getInstance()->ExecuteS('SELECT * 
            FROM `'._DB_PREFIX_.'contactform_data` WHERE `comment` >='.(int)$timestamp1.$form_request);
        } elseif ($date1 == '' && $date2 != '') {
            $timestamp2 = self::datetoTime($date2, $isoname);
            $data_lists = Db::getInstance()->ExecuteS('SELECT * 
            FROM `'._DB_PREFIX_.'contactform_data` WHERE `comment` <='.(int)$timestamp2.$form_request);
        } else {
            $data_lists = Db::getInstance()->ExecuteS('SELECT * 
            FROM `'._DB_PREFIX_.'contactform_data`'.$form_request_else);
        }

        foreach ($data_lists as $data_list) {
            $info = str_replace($separator, ' ', $data_list['info']);
            $info = str_replace('<br>', '/', $info);
            $info = str_replace('<br />', '/', $info);
            $info = str_replace('<br/>', '/', $info);
            $info = strip_tags($info);
            $info = utf8_decode($info);
            $csv_output .= $data_list['data_id'].$separator.$data_list['ip'].
            $separator.$data_list['date'].$separator.$data_list['toemail'].
            $separator.$data_list['foremail'].$separator.$info."\n";
        }

        ob_clean();
        flush();
        print $csv_output;
        exit;
    }

    public static function getFormList()
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        $form_lists = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'contactform`');
        $lists = '';
        foreach ($form_lists as $form_list) {
            $lists .= '<option value="'.$form_list['fid'].'">'.$form_list['formname'].'</option>';
        }
        return $lists;
    }
    public static function exportcsvform($date1, $date2, $isoname, $separator, $formid)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        header('Content-type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-disposition: attachment; filename=data'.time().'.csv');
        $form_request = ' AND `fid` = '.$formid;
        $form_request_else = ' WHERE `fid` = '.$formid;
        $sql = '';
        if (!$separator || $separator == '') {
            $separator = ';';
        }
        $csv_output = '';
        $wheretimestamp12 = '';
        $wheretimestamp1 = '';
        $wheretimestamp2 = '';
        if ($date1 != '' && $date2 != '') {
            $timestamp1 = self::datetoTime($date1, $isoname);
            $timestamp2 = self::datetoTime($date2, $isoname);
            $wheretimestamp12 = ' WHERE `comment` >='.(int)$timestamp1.'
            AND `comment` <='.(int)$timestamp2.$form_request;
        } elseif ($date1 != '' && $date2 == '') {
            $timestamp1 = self::datetoTime($date1, $isoname);
            $wheretimestamp1 = ' WHERE `comment` >='.(int)$timestamp1.$form_request;
        } elseif ($date1 == '' && $date2 != '') {
            $timestamp2 = self::datetoTime($date2, $isoname);
            $wheretimestamp2 = ' WHERE `comment` <='.(int)$timestamp2.$form_request;
        } else {
            $wherenotimestamp = $form_request_else;
        }
        $sql .= 'SELECT * FROM `'._DB_PREFIX_.'contactform_data`'.
        $wheretimestamp12.$wheretimestamp1.$wheretimestamp2.$wherenotimestamp;
        $data_lists = Db::getInstance()->ExecuteS($sql);
        if (!$data_lists) {
            $csv_output .= Translate::getModuleTranslation('contactfrm', 'No data found', 'contactfrm');
        } else {
            $fields_not_export = array(0 => 'captcha', 1 => 'code de sécurity');
            $datas_part = explode('<br>', $data_lists[0]['info']);
            $datas_part = array_filter($datas_part);
            $csv_output .= 'IP'.$separator.'Date'.$separator.'TO'.$separator.'FROM'.$separator;
            foreach ($datas_part as $data_part) {
                $single_info = explode(':', $data_part);
                $single_inf = trim(Tools::strtolower(strip_tags($single_info[0])));
                if (!in_array($single_inf, $fields_not_export)) {
                    $csv_output .= strip_tags(utf8_decode($single_info[0])).$separator;
                }
            }
            $csv_output .= 'MAIL STATUS';
            $csv_output .= "\n";
            foreach ($data_lists as $data_list) {
                $csv_output .= $data_list['ip'].
                                $separator.$data_list['date'].
                                $separator.$data_list['toemail'].
                                $separator.$data_list['foremail'].$separator;
                foreach ($datas_part as $data_part) {
                    $single_info = explode(':', $data_part);
                    $single_inf = trim(Tools::strtolower(strip_tags($single_info[0])));
                    if (!in_array($single_inf, $fields_not_export)) {
                        $csv_output .= strip_tags(utf8_decode($single_info[1])).$separator;
                    }
                }
                $csv_output .= ($data_list['statut_mail'] == 'mail') ? 'Send' : 'Not send';
                $csv_output .= "\n";
            }
        }
        ob_clean();
        flush();
        print $csv_output;
        exit;
    }

    public static function exportxlsform($date1, $date2, $isoname, $formid)
    {
        require_once(dirname(__FILE__).'/../../../config/config.inc.php');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=data'.time().'.xls');
        $form_request = ' AND `fid` = '.$formid;
        $form_request_else = ' WHERE `fid` = '.$formid;
        $sql = '';
        $wheretimestamp1 = '';
        $wheretimestamp12 = '';
        $wheretimestamp2 = '';
        $wherenotimestamp = '';
        $fields_not_export = array(0 => 'captcha', 1 => 'code de sécurity');
        if ($date1 != '' && $date2 != '') {
            $timestamp1 = self::datetoTime($date1, $isoname);
            $timestamp2 = self::datetoTime($date2, $isoname);
            $wheretimestamp12 = ' WHERE `comment` >='.(int)$timestamp1.'
            AND `comment` <='.(int)$timestamp2.$form_request;
        } elseif ($date1 != '' && $date2 == '') {
            $timestamp1 = self::datetoTime($date1, $isoname);
            $wheretimestamp1 = ' WHERE `comment` >='.(int)$timestamp1.$form_request;
        } elseif ($date1 == '' && $date2 != '') {
            $timestamp2 = self::datetoTime($date2, $isoname);
            $wheretimestamp2 = ' WHERE `comment` <='.(int)$timestamp2.$form_request;
        } else {
            $wherenotimestamp = $form_request_else;
        }
        $sql .= 'SELECT * FROM `'._DB_PREFIX_.'contactform_data`'.$wheretimestamp12.
        $wheretimestamp1.$wheretimestamp2.$wherenotimestamp;
        $data_lists = Db::getInstance()->ExecuteS($sql);
        if (!$data_lists) {
            $output = '<table width="100%" class="table" 
            cellspacing="0" cellpadding="0" style="border: 1px solid #000">';
            $output .= '<tr><th>'.
            Translate::getModuleTranslation('contactfrm', 'No data found', 'contactfrm')
            .'</th></tr>';
            $output .= '</table>';
        } else {
            $fields_not_export = array(0 => 'captcha');
            $output = '<table width="100%" class="table" cellspacing="0"
            cellpadding="0" style="border: 1px solid #000">';
            $output .= '<tr style="border: 1px solid #000">
                            <th width="5%">'.
                            Translate::getModuleTranslation('contactfrm', 'Ip address', 'contactfrm').'</th>
                            <th width="5%">'.
                            Translate::getModuleTranslation('contactfrm', 'Date', 'contactfrm').'</th>
                            <th width="5%">'.
                            Translate::getModuleTranslation('contactfrm', 'Email to', 'contactfrm').'</th>
                            <th>'.
                            Translate::getModuleTranslation('contactfrm', 'Email from', 'contactfrm').'</a></th>';
            $datas_part = explode('<br>', $data_lists[0]['info']);
            $datas_part = array_filter($datas_part);
            foreach ($datas_part as $data_part) {
                $single_info = explode(':', $data_part);
                $single_inf = trim(Tools::strtolower(strip_tags($single_info[0])));
                if (!in_array($single_inf, $fields_not_export)) {
                    $output .= '<th>'.strip_tags(utf8_decode($single_info[0])).'</th>';
                }
            }
            $output .= '<th>MAIL STATUS</th>';
            $output .= '</tr>';
            foreach ($data_lists as $data_list) {
                $output .= '<tr align="center" valign="top" style="border: 1px solid #000">';
                $output .= '<td>'.$data_list['ip'].'</td>
                            <td>'.$data_list['date'].'</td>
                            <td>'.$data_list['toemail'].'</td>
                            <td>'.$data_list['foremail'].'</td>';
                foreach ($datas_part as $data_part) {
                    $single_info = explode(':', $data_part);
                    $single_inf = trim(Tools::strtolower(strip_tags($single_info[0])));
                    if (!in_array($single_inf, $fields_not_export)) {
                        $output .= '<td>'.strip_tags(utf8_decode($single_info[1])).'</td>';
                    }
                }
                $output .= ($data_list['statut_mail'] == 'mail') ? '<td>Send</td>' : '<td>Not send</td>';
                $output .= '</tr>';
            }
            $output .= '</table>';
        }
        ob_clean();
        flush();
        print $output;
        exit;
    }
}
