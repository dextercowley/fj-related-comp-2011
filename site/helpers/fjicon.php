<?php
/**
 * @version		$Id: fjicon.php 113 2011-05-09 23:39:37Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once (JPATH_BASE . '/components/com_content/helpers/icon.php');

/**
 * FJRelated Component HTML Helper (copied from Content Component HTML Helper)
 *
 * @static
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class JHTMLFJIcon
{
	function create($article, $params)
	{
		$return = JHTMLIcon::create($article, $params);
		return str_replace('com_fjrelated','com_content',$return);
	}

	function email($article, $params)
	{
		return JHTMLIcon::email($article, $params, $attribs = array());
	}

	function edit($article, $params, $attribs = array())
	{
		// Initialise variables.
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$uri	= JFactory::getURI();

		// Ignore if in a popup window.
		if ($params && $params->get('popup')) {
			return;
		}

		// Ignore if the state is negative (trashed).
		if ($article->state < 0) {
			return;
		}

		JHtml::_('behavior.tooltip');

		// Show checked_out icon if the article is checked out by a different user
		if (property_exists($article, 'checked_out') && property_exists($article, 'checked_out_time') && $article->checked_out > 0 && $article->checked_out != $user->get('id')) {
			$checkoutUser = JFactory::getUser($article->checked_out);
			$button = JHtml::_('image','system/checked_out.png', NULL, NULL, true);
			$date = JHtml::_('date',$article->checked_out_time);
			$tooltip = JText::_('JLIB_HTML_CHECKED_OUT').' :: '.JText::sprintf('COM_CONTENT_CHECKED_OUT_BY', $checkoutUser->name).' <br /> '.$date;
			return '<span class="hasTip" title="'.htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8').'">'.$button.'</span>';
		}

		$url	= 'index.php?task=article.edit&a_id='.$article->id.'&return='.base64_encode($uri);
		$icon	= $article->state ? 'edit.png' : 'edit_unpublished.png';
		$text	= JHtml::_('image','system/'.$icon, JText::_('JGLOBAL_EDIT'), NULL, true);

		if ($article->state == 0) {
			$overlib = JText::_('JUNPUBLISHED');
		}
		else {
			$overlib = JText::_('JPUBLISHED');
		}

		$date = JHtml::_('date',$article->created);
		$author = $article->created_by_alias ? $article->created_by_alias : $article->author;

		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= JText::sprintf('COM_CONTENT_WRITTEN_BY', htmlspecialchars($author, ENT_COMPAT, 'UTF-8'));

		$button = JHtml::_('link', JURI::base(true).'/'.$url, $text);

		$output = '<span class="hasTip" title="'.JText::_('COM_CONTENT_EDIT_ITEM').' :: '.$overlib.'">'.$button.'</span>';

		return $output;
	}


	function print_popup($article, $params, $attribs = array())
	{
		$return = JHTMLIcon::print_popup($article, $params, $attribs = array());
		return str_replace('com_fjrelated', 'com_content', $return);
	}

	function print_screen($article, $params, $access, $attribs = array())
	{
		return JHTMLIcon::print_screen($article, $params, $attribs = array());
	}

}
