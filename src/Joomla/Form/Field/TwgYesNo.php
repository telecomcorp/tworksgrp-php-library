<?php
/**
 * =============================================================================
 * @package     Telcoworks Group PHP Library
 * @author      David Plath <webmaster@telcoworksgrp.com.au>
 * @copyright   Copyright (c) 2020 Telcoworks Group. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * =============================================================================
 */

namespace TelcoworksGrp\Joomla\Form\Field;

use \Joomla\CMS\Factory;
use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Form\FormHelper;


// Load the parent FormField class
FormHelper::loadFieldClass('radio');


/**
 * Custom Form field for selecting a "Yes" or "No"
 */
class TwgYesNo extends \JFormFieldRadio
{

    protected $type = 'TwgYesNo';


    /**
	 * Get a list of field options.
	 * -------------------------------------------------------------------------
	 * @return 	array 	An array of options (as HTML)
	 */
	protected function getOptions()
	{
        // Call the parent method
        $options = parent::getOptions();

		// Add field options to the result
		$options[]= HTMLHelper::_('select.option', '0', 'No');
        $options[]= HTMLHelper::_('select.option', '1', 'Yes');

		// Return the resulting options (as html)
		return $options;
	}


}
