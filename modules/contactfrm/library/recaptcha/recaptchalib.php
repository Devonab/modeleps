<?php
/**
* 2016 Google Inc.
*
* NOTICE OF LICENSE
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*
* @author    Google Inc.
* @copyright 2016 Google Inc.
* @license   ARETMIC
* International Registered Trademark & Property of Aretmic SA
*/

class ReCaptcha
{
    private static $signup_url = 'https://www.google.com/recaptcha/admin';
    private static $site_verify_url = 'https://www.google.com/recaptcha/api/siteverify?';
    private $secret;
    private static $version = 'php_1.0';
    /**
    * Constructor.
    *
    * @param string $secret shared secret between site and ReCAPTCHA server.
    */
    public function __construct($secret)
    {
        if ($secret == null || $secret == '') {
            die('To use reCAPTCHA you must get an API key from 
            <a href="'.self::$signup_url.'">'.self::$signup_url.'</a>');
        }
        $this->secret = $secret;
    }
    /**
    * Encodes the given data into a query string format.
    *
    * @param array $data array of string elements to be encoded.
    *
    * @return string - encoded request.
    */
    private function encodeQs($data)
    {
        require_once(dirname(__FILE__).'/../../../../config/config.inc.php');
        $req = '';
        foreach ($data as $key => $value) {
            $req .= $key.'='.urlencode(Tools::stripslashes($value)).'&';
        }
        // Cut the last '&'
        $req = Tools::substr($req, 0, Tools::strlen($req) - 1);
        return $req;
    }
    /**
    * Submits an HTTP GET to a reCAPTCHA server.
    *
    * @param string $path url path to recaptcha server.
    * @param array  $data array of parameters to be sent.
    *
    * @return array response
    */
    private function submitHTTPGet($path, $data)
    {
        require_once(dirname(__FILE__).'/../../../../config/config.inc.php');
        $req = $this->encodeQs($data);
        $response = Tools::file_get_contents($path.$req);
        return $response;
    }
    /**
    * Calls the reCAPTCHA siteverify API to verify whether the user passes
    * CAPTCHA test.
    *
    * @param string $remote_ip   IP address of end user.
    * @param string $response   response string from recaptcha verification.
    *
    * @return ReCaptchaResponseCForm
    */
    public function verifyResponse($remote_ip, $response)
    {
        require_once(dirname(__FILE__).'/../../../../config/config.inc.php');
        require_once('recaptcharesponsecform.php');
        // Discard empty solution submissions
        if ($response == null || Tools::strlen($response) == 0) {
            $recaptcha_response = new ReCaptchaResponseCForm();
            $recaptcha_response->success = false;
            $recaptcha_response->error_codes = 'missing-input';
            return $recaptcha_response;
        }

        $get_response = $this->submitHttpGet(
            self::$site_verify_url,
            array (
            'secret' => $this->secret,
            'remoteip' => $remote_ip,
            'v' => self::$version,
            'response' => $response
            )
        );
        $answers = Tools::jsonDecode($get_response, true);
        $recaptcha_response = new ReCaptchaResponseCForm();

        if (trim($answers['success']) == true) {
            $recaptcha_response->success = true;
        } else {
            $recaptcha_response->success = false;
        }
        return $recaptcha_response;
    }
}
