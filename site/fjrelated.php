<?php
/**
 * @version		$Id: fjrelated.php 121 2011-05-18 23:46:18Z dextercowley $
 * @package		com_fjrelated_plus
 * @copyright	Copyright (C) 2008 Mark Dexter. Portions Copyright Open Source Matters. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html
 * 
 */
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
// Require the base controller
 
jimport('joomla.application.component.controller');
 
// Create the controller
$controller = JController::getInstance('FJRelated');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();

?>