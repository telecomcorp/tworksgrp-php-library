<?php
/**
 * =============================================================================
 * @package     Telecom Corporation PHP Library
 * @author      David Plath <webmaster@telecomcorp.com.au>
 * @copyright   Copyright (C) 2019 Telecom Corporation. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * =============================================================================
 */

namespace TCorp\Legacy;

use \KWS\Security\SecurityHelper;


/**
 * Helper class for working with Telecom Corp's Legacy sites/projects
 */
class Helper
{

    /**
     * A list of months of the year
     */
    const MONTHS = array('January','February','March','April','May','June',
        'July','August','September','October','November','December');


    /**
     * Send a very basic HTTP request and return the response body
     * -------------------------------------------------------------------------
     * @param  string   $url        The URL to send the quest to
     * @param  string   $method     The HTTP verb/type of request to use
     * @param  array    $data       Data to send with the request
     * @param  string[] $headers    Data to send with the request
     *
     * @return string               The reponse body
     */
    public static function sendRequest(string $url, string $method = 'GET',
        $data =array(), $headers = array())
    {
        // Compose a HTTP request using Guzzle HTTP
        $request = new \GuzzleHttp\Psr7\Request($method, $url, $headers);

        // Execute the http request with Guzzle HTTP
        $client  = new \GuzzleHttp\Client();
        $result = $client->send($request, array('query' => $data));

        // Return the result body
        return $result->getBody();
    }


    /**
     * Get a list of numbers from the T3 API
     * -------------------------------------------------------------------------
     * @param  string   $prefix     Numbe prefix ('1300' or '1800')
     * @param  string   $type       Type of numbers to get ('FLASH' or 'LUCKYDIP')
     * @param  int      $minPrice   Minimum number price
     * @param  int      $maxPrice   Max number price
     * @param  int      $pageNo     Page to start at
     * @param  int      $pageSize   Max numbers per page
     * @param  string   $sortBy     Column to sort the results by
     * @param  string   $direction  Direction to sort the results by
     *
     * @return  object[]    A list of numbers with meta data
     */
    public static function getNumbers($prefix = '1300', $type = 'FLASH',
        $minPrice = 0, $maxPrice = 1000, $pageNo = 1, $pageSize = 500,
        $sortBy = 'PRICE', $direction = 'ASCENDING')
    {

        // Compose an enpoint URL
        $params                       = array();
        $params['query']              = $prefix;
        $params['numberTypes']        = 'SERVICE_NUMBER';
        $params['serviceNumberTypes'] = $type;
        $params['minPriceDollars']    = $minPrice;
        $params['maxPriceDollars']    = $maxPrice;
        $params['pageNum']            = $pageNo;
        $params['pageSize']           = $pageSize;
        $params['sortBy']             = $sortBy;
        $params['sortDirection']      = $direction;


        // Get the data from the API
        $result = self::sendRequest(
            'https://portal.tbill.live/numbers-service-impl/api/Activations',
            'GET', $params, array('Content-type: application/json'));

        // Decode JSON response
        $result = json_decode($result);

        // Add additional meta data
        foreach($result as $number) {
            $number->format1 = preg_replace('|^(\d{4})(\d{6})$|i', '$1 $2', $number->number);
	        $number->format2 = preg_replace('|^(\d{4})(\d{3})(\d{3})$|i', '$1 $2 $3', $number->number);
            $number->format3 = preg_replace('|^(\d{4})(\d{2})(\d{2})(\d{2})$|i', '$1 $2 $3 $4', $number->number);
            $number->format4 = (!empty($number->word) ? $number->word : $number->format3);
        }

        // Return the result
        return $result;
    }


    /**
     * Send an email
     * -------------------------------------------------------------------------
     * @param  string   $to           Receiver, or receivers of the mail.
     * @param  string   $from         A From address
     * @param  string   $subject      Subject of the email to be sent.
     * @param  string   $message      Message to be sent.
     * @param  string   $cc           A CC address
     * @param  string   $bcc          A BCC address
     * @param  mixed    $headers      String/array of additional headers to add
     *
     * @return bool                 TRUE if successfully sent, FALSE otherwise
     */
    public static function sendEmail(string $to, string $from, string $subject,
        string $message, string $cc = '', string $bcc = '', $headers = array())
    {
        // Add some mime headers if the message contains HTML
        if ($message != strip_tags($message)) {
            $headers['MIME-Version']      = "1.0";
            $headers['Content-type']      = "text/html; charset=iso-8859-1";
        }

        // Add a From header
        if (!empty($from)) {
            $headers['From'] = $from;
        }

        // Add a CC header
        if (!empty($from)) {
            $headers['Cc'] = $from;
        }

        // Add a BCC header
        if (!empty($bcc)) {
            $headers['Bcc'] = $bcc;
        }

        // Add some additional metadata to headers
        $headers['X-WebForm-ServerIP']   = $_SERVER['SERVER_ADDR'];
        $headers['X-WebForm-ServerName'] = $_SERVER['SERVER_NAME'];
        $headers['X-WebForm-Host']       = static::getCurrentDomainName();
        $headers['X-WebForm-Referrer']   = $_SERVER['HTTP_REFERER'];
        $headers['X-WebForm-UserAgent']  = static::getRemoteUserAgent();
        $headers['X-WebForm-RemoteIP']   = static::getRemoteIPAddress();
        $headers['X-WebForm-URI']        = $_SERVER['REQUEST_URI'];
        $headers['X-WebForm-Script']     = $_SERVER['SCRIPT_NAME'];

        // Send the email
        $result = mail($to, $subject, $message, $headers);

        // Return the result
        return $result;
    }


    /**
     * Composes an email message from all POST params - plus the IP address
     * of the remote user. This is a quick and dirty way some of the older
     * sites display form data in email notifications
     * -------------------------------------------------------------------------
     * @return  string  An email message
     */
    public static function composeMessageFromPostParams() : string
    {
        // Initialise some local variables
        $params       = $_POST;
        $params['ip'] = static::getRemoteIPAddress();
        $result       = '';

        // Add a list of key-value pairs
        foreach ($params as $key => $value) {
            $k = htmlentities($key);
        	$v = htmlentities($value);
            $result .= "$k - $v\n";
        }

        // Return the result
        return $result;
    }


    /**
     * Render a hidden input field for each POST variable. Not a good
     * practice but needed to avoid breaking some of Telecom Corp's
     * legacy websites
     * -------------------------------------------------------------------------
     * @return string   Rendered HTML
     */
    public static function renderPostParamsAsHiddenFields()
    {
        // Initialise some local variables
        $result = '';

        // Render a hidden input field for each POST variable
        foreach ($_POST as $key => $value) {
            $key     = htmlentities($key);
            $value   = htmlentities($value);
            $result .= "<input type=hidden name=$key value=\"$value\">\n";
        }

        // Return the result
        return $result;
    }


    /**
     * Redirect the user's browser to another URL, preserving the current
     * URL parameters.
     * -------------------------------------------------------------------------
     * @param  string   $url             URL to redirect the user to
     * @param  bool     $preserveParams  Pass existing URL params to the redirect
     * @param  int      $statusCode      HTTP status code (usually 301 or 303)
     *
     * @return  void
     */
    public static function redirect(string $url, bool $preserveParams = TRUE,
        int $statusCode = 301) : void
    {
        // Append the exitsing params if needed
        if ($preserveParams) {

            $url = $url . ((strpos($url, '?')) ? '&' : '?') .
                $_SERVER['QUERY_STRING'];

        }

        // Redirect the user
        header('Location: ' . $url, true, $statusCode);
        exit();
    }

    /**
     * Disable browser caching of this request
     * -------------------------------------------------------------------------
     * @return  void
     */
    public static function disableCache() : void
    {
        header("Cache-Control: max-age=0, no-cache, no-store, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
    }



    /**
     * Look up the details for a given ABN using an API
     * -------------------------------------------------------------------------
     * @param  string   $abn    The ABN to lookup
     *
     * @return object   ABN details, or False if ABN not found
     */
    public static function getABNDetails(string $abn)
    {
        // Initialise some local variables
        $result = new \stdClass();

        // Look up the ABN details using ABR's API
        $url = "https://abr.business.gov.au/abrxmlsearch/" .
            "AbrXmlSearch.asmx/ABRSearchByABN";

        $guid = Config::getValue('abnlookup.guid', '');

        $data = self::sendRequest($url, 'GET', array(
            'searchString'             => $abn,
            'includeHistoricalDetails' => 'Y',
            'authenticationGuid'       => $guid
        ));

        // Parse the data returned by the API
        $data = new \SimpleXMLElement($data);
        $data = $data->response;

        $result->statement = (string) $data->usageStatement;
        $result->abn       = (string) $data->businessEntity->ABN->identifierValue;
        $result->current   = (string) $data->businessEntity->ABN->isCurrentIndicator;
        $result->asicNo    = (string) $data->businessEntity->ASICNumber;

        $entityType               = $data->businessEntity->entityType;
        $result->entityType       = new \stdClass;
        $result->entityType->code = (string) $entityType->entityTypeCode;
        $result->entityType->desc = (string) $entityType->entityDescription;

        $legalName                    = $data->businessEntity->legalName;
        $result->legalName            = new \stdClass;
        $result->legalName->firstname = (string) $legalName->givenName;
        $result->legalName->othername = (string) $legalName->otherGivenName;
        $result->legalName->lastname  = (string) $legalName->familyName;

        $mainName                       = $data->businessEntity->mainName;
        $result->mainName               = new \stdClass;
        $result->mainName->organisation = (string) $mainName->organisationName;
        $result->mainName->effective    = (string) $mainName->effectiveFrom;

        $tradeName                       = $data->businessEntity->mainTradingName;
        $result->tradeName               = new \stdClass;
        $result->tradeName->organisation = (string) $tradeName->organisationName;
        $result->tradeName->effective    = (string) $tradeName->effectiveFrom;

        // Return the result
        return $result;
    }



    /**
     *  Block the user if thier IP belongs to a banned country. SecurityHelper::
     *  WORST_SPAM_COUNTRIES is a predefined list of the worst spam/bot
     *  countries according to Spamhaus. To avoid blocking Googlebot, the US is
     *  exluded from this predefined list.
     *  ------------------------------------------------------------------------
     *  @return void
     */
    public static function blockBannedCountries() : void
    {
        $apiKey = Config::getValue('ipgeolocation.apikey', '');

        if (SecurityHelper::checkIpLocation(SecurityHelper::
            WORST_SPAM_COUNTRIES, $apiKey)) {


            SecurityHelper::blockAccess();
        }
    }


    /**
     * Proxy for the SecurityHelper::getHoneypotHtml() method
     * -------------------------------------------------------------------------
     * @return  string  HTML for rendering a hidden honeypot text field
     */
    public static function getHoneypotHtml()
    {
        return SecurityHelper::getHoneypotHtml();
    }


    /**
     * Check the hidden honeypot form field. If it is missing or invalid then
     * the user will be blocked
     * -------------------------------------------------------------------------
     * @return  void
     */
    public static function blockIfInvalidHoneypot() : void
    {
        if (!SecurityHelper::checkHoneypot()) {
            SecurityHelper::blockAccess();
        }
    }


    /**
     * Proxy for the SecurityHelper::getCSRFTokenHtml() method
     * -------------------------------------------------------------------------
     * @return  string  HTML for rendering a CSRF token inside a web form
     */
    public static function getCSRFTokenHtml()
    {
        return SecurityHelper::getCSRFTokenHtml();
    }


    /**
     * Check the CSRF token. If it is missing or doesn't match the one stored
     * in the user's session then the user will be blocked
     * -------------------------------------------------------------------------
     * @return  void
     */
    public static function blockIfInvalidCSRFToken() : void
    {
        if (!SecurityHelper::checkCSRFToken()) {
            SecurityHelper::blockAccess();
        }
    }


    /**
     * Proxy for the SecurityHelper::getReCaptchaHtml() method
     * -------------------------------------------------------------------------
     * @return  string  HTML for rendering a CSRF token inside a web form
     */
    public static function getReCaptchaHtml()
    {
        $siteKey = Config::getValue('recaptcha.sitekey', '');
        return SecurityHelper::getReCaptchaHtml($siteKey);
    }


    /**
     * Check if the form ReCaptcha was successfully completed. If not, then
     * the user will be redirected
     * -------------------------------------------------------------------------
     * @return void
     */
    public static function redirectIfInvalidReCaptcha(string $redirectUrl) : void
    {
        $secret = Config::getValue('recaptcha.secret', '');
        if (!SecurityHelper::checkReCaptcha($secret)) {
            self::redirect($redirectUrl, false, 303);
        }
    }

    /**
     * Get the one-time affilate referral id that is set when an affiliate
     * reffers a cutsomer to this website to make an application. This referral
     * id should not be confused with an "affiliate id" which identifies the
     * affilate not the referral.
     * -------------------------------------------------------------------------
     * @return  string  The one-time affilate refferal id.
     */
    public static function getAffiliateReferralId()
    {
        return Input::getValue('affiliate', '');
    }

    /**
     * Store the value of a request variable in a session var. If the request
     * var doesn't exist then preserve the existing session var. If a session
     * var with the given key doesn't exist then set a session var with the
     * given key to a given default value.
     * -------------------------------------------------------------------------
     * @param string    $key        A key name for referancing the stored value
     * @param string    $var        A GET/POST variable name
     * @param string    $default    Default value if both request and session
     *                              var doesn't exist
     * @param string    $filter     Filter Type (for sanitisation)
     *
     * @return  mixed   The final value of session var
     */
    public static function setSessionVarFromRequest(string $key, string $var,
        $default = '', string $filter = 'STRING')
    {
        if (isset($_REQUEST[$var])) {
            Session::setValue($key, $_REQUEST[$var]);
        } else {
            if (!isset($_SESSION[$key])) {
                Session::setValue($key, $default);
            }
        }

        // Return the result
        return Session::getValue($key);
    }


    /**
     * Get the user's/remote IP address
     * -------------------------------------------------------------------------
     * @return  string  An IP address
     */
    public static function getRemoteIPAddress()
    {
        return $_SERVER['REMOTE_ADDR'];
    }


    /**
     * Get the user's/remote User Agent
     * -------------------------------------------------------------------------
     * @return  string  An IP address
     */
    public static function getRemoteUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }


    /**
     * Get the current domain name
     * -------------------------------------------------------------------------
     * @return  string  A domain name
     */
    public static function getCurrentDomainName()
    {
        return $_SERVER['HTTP_HOST'];
    }


    /**
     * Get the current state of a given form field's value. Values in the
     * request replace values in the current session. If a value can't be
     * found in the request or the current session a default value can
     * be returned.
     * -------------------------------------------------------------------------
     * @param  string   $key        Session variable  name
     * @param  string   $name       Input variable name
     * @param  mixed    $default    Default value if no value can be found
     * @param  string   $filter     Filter type for sanitization
     *
     * @return mixed    The current value f the form field, as per the
     *                  request and session
     */
    public static function getFormFieldState(string $key, string $name,
        $default = null, string $filter = 'STRING')
    {
        // Try to get the form field's value from the request.
        $result = Input::getValue($name, false, $filter);

        // If a value from the request has been found, set/update
        // the corrsponding session variable
        if ($result !== false) {
            Session:setValue($key, $result);
        }

        // Get the new/existing value from the session.
        $result = Session::getValue($key, false);

        // If we still don't have a value return the default value.
        if ($result === false) {
            $result = $default;
        }

        // Return the result
        return $result;
    }

}