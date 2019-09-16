<?php
/**
 * =============================================================================
 * @package     Telecom Corporation PHP Library
 * @author      David Plath <webmaster@telecomcorp.com.au>
 * @copyright   Copyright (C) 2019 Telecom Corporation. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * =============================================================================
 */

namespace TCorp\Legacy\Form;

use \TCorp\Legacy\Helper;


/**
 * Class for working with the telecom corporate transfer signup form
 */
class TransferForm extends BaseForm
{

    /**
     * PLan the user is signing up to
     *
     * @var string
     */
    public $plan = '';


    /**
     * The 1300/1800 number to be transfered
     *
     * @var string
     */
    public $number = '';


    /**
     * The provider from which the number needs to be
     * transfered from
     *
     * @var string
     */
    public $provider = '';


    /**
     * The customer's account number
     *
     * @var string
     */
    public $accountno = '';


    /**
     * The customer's company/trading name
     *
     * @var string
     */
    public $company = '';


    /**
     * The customer's ABN
     *
     * @var string
     */
    public $abn = '';


    /**
     * The address in which the customer's company is lcoated
     *
     * @var string
     */
    public $address1 = '';


    /**
     * The address in which the customer's company is lcoated
     *
     * @var string
     */
    public $address2 = '';


    /**
     * The suburb in which the customer's company is lcoated
     *
     * @var string
     */
    public $suburb     = '';


    /**
     * The state in which the customer's company is lcoated
     *
     * @var string
     */
    public $state = '';


    /**
     * The postcode in which the customer's company is located
     *
     * @var string
     */
    public $postcode = '';


    /**
     * The customer's first name
     *
     * @var string
     */
    public $firstname = '';


    /**
     * The customer's last name
     *
     * @var string
     */
    public $lastname = '';


    /**
     * The customer's mobile number
     *
     * @var string
     */
    public $mobile = '';


    /**
     * The customer's email address
     *
     * @var string
     */
    public $email = '';


    /**
     * The customer's phone number
     *
     * @var string
     */
    public $phone = '';


    /**
     * The user agreed to terms and conditions ??
     *
     * @var bool
     */
    public $iagree = false;




    /**
     * Load the current state of the form
     * -------------------------------------------------------------------------
     * @return void
     */
    public function loadState()
    {
        $this->plan       = Helper::getFormFieldState('trasnfer.plan', 'plan');
        $this->number     = Helper::getFormFieldState('trasnfer.number', 'number');
        $this->provider   = Helper::getFormFieldState('trasnfer.provider', 'provider');
        $this->accountno  = Helper::getFormFieldState('trasnfer.accountno', 'accountno');
        $this->company    = Helper::getFormFieldState('trasnfer.company', 'company');
        $this->abn        = Helper::getFormFieldState('trasnfer.abn', 'abn');
        $this->address1   = Helper::getFormFieldState('trasnfer.address1', 'address1');
        $this->address2   = Helper::getFormFieldState('trasnfer.address2', 'address2');
        $this->suburb     = Helper::getFormFieldState('trasnfer.suburb', 'suburb');
        $this->state      = Helper::getFormFieldState('trasnfer.state', 'state');
        $this->postcode   = Helper::getFormFieldState('trasnfer.postcode', 'postcode');
        $this->firstname  = Helper::getFormFieldState('trasnfer.firstname', 'firstname');
        $this->lastname   = Helper::getFormFieldState('trasnfer.lastname', 'lastname');
        $this->mobile     = Helper::getFormFieldState('trasnfer.mobile', 'mobile');
        $this->email      = Helper::getFormFieldState('trasnfer.email', 'email');
        $this->phone      = Helper::getFormFieldState('trasnfer.phone', 'phone');
        $this->iagree     = Helper::getFormFieldState('trasnfer.iagree', 'iagree');

        // Call the parent method
        parent::loadState();
    }

}