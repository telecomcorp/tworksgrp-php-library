<?php
/**
 * =============================================================================
 * @package     Telecom Corporation PHP Library
 * @author      David Plath <webmaster@telecomcorp.com.au>
 * @copyright   Copyright (C) 2019 Telecom Corporation. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * =============================================================================
 */

namespace TCorp;

use \TCorp\Rest\Client AS RestClient;


/**
 * Client for accessing Telecom Corporate's Web API
 */
class Client extends RestClient
{

    /**
     * Base URL for all API resources
     *
     * @var string
     */
    protected $baseUrl = 'https://tcorpapi.krealmwebservices.com.au/';

}