<?php
/**
 * @version		$Id: router.php 99 2011-05-03 22:49:23Z dextercowley $
 * @copyright	Copyright (C) 2011 Mark Dexter. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.categories');
require_once (JPATH_BASE . '/components/com_content/router.php');

/**
 * Build the route for the com_fj_related component
 *
 * @param	array	An array of URL arguments
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 * @since	1.5
 */
function FJRelatedBuildRoute(&$query)
{
	// Just use the com_content router
	return ContentBuildRoute($query);
}



/**
 * Parse the segments of a URL.
 *
 * @param	array	The segments of the URL to parse.
 *
 * @return	array	The URL attributes to be used by the application.
 * @since	1.5
 */
function FJRelatedParseRoute($segments)
	{
	// Just call com_content router
	return ContentParseRoute($segments);
}
