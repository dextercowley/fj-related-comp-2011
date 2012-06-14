<?php
/**
 * @version		$Id: controller.php 143 2011-06-06 02:26:43Z dextercowley $
 * @package		com_fjrelated_plus
 * @copyright	Copyright (C) 2008 Mark Dexter. Portions Copyright Open Source Matters. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html
 *
 */

// no direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Method to show an article as the main display page
 *
 * @access  public
 */
class FJRelatedController extends JController
{
	/**
	 * Method to display the view
	 *
	 * @access    public
	 */
	function display($cachable = false, $urlparams = false)
	{
		$safeurlparams = array('catid'=>'INT','id'=>'INT','cid'=>'ARRAY','year'=>'INT','month'=>'INT','limit'=>'INT','limitstart'=>'INT',
			'showall'=>'INT','return'=>'BASE64','filter'=>'STRING','filter_order'=>'CMD','filter_order_Dir'=>'CMD','filter-search'=>'STRING','print'=>'BOOLEAN','lang'=>'CMD');
		 
		return parent::display(false, $safeurlparams);
	}
}