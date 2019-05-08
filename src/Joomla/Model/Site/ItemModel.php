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

namespace TCorp\Joomla\Model\Site;


use \Joomla\CMS\MVC\Model\ItemModel;
use \Joomla\CMS\Factory;


class ItemModel extends ItemModel
{

    /**
     * Auto-populate the model state.
     * -------------------------------------------------------------------------
     * @return  void
     */
    protected function populateState()
    {
        // Initialize some local variables
        $application = Factory::getApplication();
        $input       = $application->input;

        // Set the item id in the model state
		$this->setState('id', $input->getInt('id', 0));

        // Call the parent method
        parent::populateState();
    }

}