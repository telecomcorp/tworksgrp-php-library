<?php
/**
 * =============================================================================
 * @package     Telcoworks Group PHP Library
 * @author      David Plath <webmaster@telcoworksgrp.com.au>
 * @copyright   Copyright (c) 2020 Telcoworks Group. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * =============================================================================
 */

namespace TelcoworksGrp\Joomla\View\Site;


use \Joomla\CMS\HTML\HTMLHelper;


/**
 * Base class for creating item based front-end views
 */
class ItemView extends GenericView
{

    /**
     * Execute and display a view layout.
     * -------------------------------------------------------------------------
     * @param  string   $tpl    The name of the view layout to parse
     *
     * @return mixed            A string if successful, Error object if not
     */
    public function display($tpl = null)
    {
        // Add data to the view
        $this->item = $this->get('Item');

        // If the item has a title then use it for the document title
        if (!empty($this->item)) {
            if (property_exists($this->item, 'title')) {
                $this->setDocumentTitle($this->item->title);
            }
        }

        // Call and return the parent method
		return parent::display($tpl);
    }

}
