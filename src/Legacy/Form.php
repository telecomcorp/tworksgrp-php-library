<?php
/**
 * =============================================================================
 *
 * @package     Telecom Corporation PHP Library
 * @author      David Plath <webmaster@telecomcorp.com.au>
 * @copyright   Copyright (C) 2019 Telecom Corporation. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 *
 * =============================================================================
 */

namespace TCorp\Legacy;


/**
 * Class for working with HTML forms
 */
class Form
{


    /**
     * Holds the form's honeypot
     *
     * @var \TCorp\Legacy\Honeypot
     */
    public $honeypot = null;


    /**
     * Construtor method for initiailing new instances of this class
     * -------------------------------------------------------------------------
     * @return void
     */
    public function __construct()
    {
        // Initialise some class properties
        $this->honeypot = new Honeypot();
    }

}