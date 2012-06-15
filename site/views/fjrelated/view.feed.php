<?php
/**
 * @package		com_fjrelated_plus
 * @copyright	Copyright (C) 2008 Mark Dexter. Portions Copyright Open Source Matters. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * Feed View class for the FJ_Related Component
 *
 * @package    FJRelated
 */

class FJRelatedViewFJRelated extends JView
{
	function display()
	{
		global $mainframe;
		// parameters
		$app		= JFactory::getApplication();
		$db			= JFactory::getDbo();
		$document	= JFactory::getDocument();
		$params		= $app->getParams();
		$feedEmail	= (@$app->getCfg('feed_email')) ? $app->getCfg('feed_email') : 'author';
		$siteEmail	= $app->getCfg('mailfrom');

		// Get some data from the model
		JRequest::setVar('limit', $app->getCfg('feed_limit'));
		$article	= & $this->get( 'Article' );
		$rows 		= & $this->get( 'Data' );

		$document->link = JRoute::_(ContentHelperRoute::getArticleRoute($article->id, $article->catid));

		foreach ( $rows as $row )
		{
			// strip html from feed item title
			$title = $this->escape( $row->title );
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');

			// url link to article
			// & used instead of &amp; as this is converted by feed creator
			$link = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catid));;

			// strip html from feed item description text
			$description	= ($params->get('feed_summary', 0) ? $row->introtext.$row->fulltext : $row->introtext);
			$author			= $row->created_by_alias ? $row->created_by_alias : $row->author;

			// load individual item creator class
			$item = new JFeedItem();
			$item->title 		= $title;
			$item->link 		= $link;
			$item->description 	= $description;
			$item->date			= $row->created;

			// loads item info into rss array
			$document->addItem( $item );
		}
	}
}
