<?php
/**
 * @package		com_fj_related
 * @copyright	Copyright (C) 2008-2011 Mark Dexter. Portions Copyright Open Source Matters. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html
 *
 */

// no direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
jimport( 'joomla.html.parameter');

/**
 * HTML View class for the FJ_Related Component
 *
 * @package    FJ_Related
 */

class FJRelatedViewFJRelated extends JView
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;

	protected $lead_items = array();
	protected $intro_items = array();
	protected $link_items = array();
	protected $columns = 1;

	function __construct($config = array())
	{
		parent::__construct($config);

		//Add the helper path to the JHTML library
		JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers');
	}

	function display($tpl = null)
	{
		$app 		= JFactory::getApplication();

		$user		= JFactory::getUser();
		$uri 		= JFactory::getURI();
		$document	= JFactory::getDocument();
		$dispatcher	= JDispatcher::getInstance();
		$pathway	= $app->getPathway();

		// Get the menu item object
		$menus = $app->getMenu();
		$menu  = $menus->getActive();
		$params = $app->getParams();

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		// Initialize variables
		$article	= $this->get('Article');
		$state		= $this->get('State');

		// Request variables
		$layout     = $params->get('layout_type');
		$task		= JRequest::getCmd('task');


		$numIntro		= $params->get('num_intro_articles', 4);
		$numLeading	= $params->get('num_leading_articles', 1);
		$numLinks		= $params->get('num_links', 4);

		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
		$itemid = JRequest::getInt('Itemid',0);

		$params->def('display_num', $app->getCfg('list_limit'));
		$default_limit = $params->get('display_num');

		if ($layout == 'blog') {
			$limit = $numIntro + $numLeading + $numLinks;
		} else {
			$params->def('display_num', $app->getCfg('list_limit'));
			$default_limit = $params->get('display_num');
			$limit  = $app->getUserStateFromRequest('com_fjrelated.list.:' .$itemid. '.limit', 'limit', $default_limit, 'int');
		}




		JRequest::setVar('limit', (int) $limit);

		$contentConfig = JComponentHelper::getParams('com_content');
		$params->def('show_page_title', $contentConfig->get('show_title'));
		$menu_params = new JParameter( $menu->params );
		if (!$menu_params->get( 'page_title'))
		{
			$params->set('page_title',	($article->title) ? $article->title : $menu->title );
		}
		$document->setTitle( $params->get( 'page_title' ) );

		if ($article->metadesc) {
			$document->setDescription( $article->metadesc );
		}
		if ($article->metakey) {
			$document->setMetadata('keywords', $article->metakey);
		}

		// Get some data from the model
		$items		=  $this->get( 'Data' );
		if ($items) {
			$total =  $this->get( 'Total' ); // only do count if there are items to count
		} else {
			$total = 0;
		}
		//add alternate feed link
		if($params->get('show_feed_link', 1) == 1)
		{
			$link	= '&format=feed&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$document->addHeadLink(JRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$document->addHeadLink(JRoute::_($link.'&type=atom'), 'alternate', 'rel', $attribs);
		}

		// Create a user access object for the user
		$access					= new stdClass();
		$access->canEdit		= $user->authorize('core.edit', 'com_content');
		$access->canEditOwn		= $user->authorize('core.edit.own', 'com_content');
		$access->canPublish		= $user->authorize('core.edit.state', 'com_content');

		jimport('joomla.html.pagination');
		//In case we are in a blog view set the limit
		if ($layout == 'blog') {
			$pagination = new JPagination($total, $limitstart, $limit - $numLinks);
		} else {
			$pagination = new JPagination($total, $limitstart, $limit);
		}

		// Compute the article slugs and prepare introtext (runs content plugins).
		foreach ($items as $i => & $item)
		{
			$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
			$item->catslug = ($item->category_alias) ? ($item->catid . ':' . $item->category_alias) : $item->catid;
			$item->parent_slug = ($item->parent_alias) ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;
			// No link for ROOT category
			if ($item->parent_alias == 'root') {
				$item->parent_slug = null;
			}

			$item->event = new stdClass();

			$dispatcher = JDispatcher::getInstance();

			// Ignore content plugins on links.
			if ($i < $numLeading + $numIntro)
			{
				$item->introtext = JHtml::_('content.prepare', $item->introtext);

				$results = $dispatcher->trigger('onContentAfterTitle', array('com_content.article', &$item, &$item->params, 0));
				$item->event->afterDisplayTitle = trim(implode("\n", $results));

				$results = $dispatcher->trigger('onContentBeforeDisplay', array('com_content.article', &$item, &$item->params, 0));
				$item->event->beforeDisplayContent = trim(implode("\n", $results));

				$results = $dispatcher->trigger('onContentAfterDisplay', array('com_content.article', &$item, &$item->params, 0));
				$item->event->afterDisplayContent = trim(implode("\n", $results));
			}
		}

		// Preprocess the breakdown of leading, intro and linked articles.
		// This makes it much easier for the designer to just interogate the arrays.
		$max = count($items);

		// The first group is the leading articles.
		$limit = $numLeading;
		for ($i = 0; $i < $limit && $i < $max; $i++)
		{
			$this->lead_items[$i] = &$items[$i];
		}

		// The second group is the intro articles.
		$limit = $numLeading + $numIntro;
		// Order articles across, then down (or single column mode)
		for ($i = $numLeading; $i < $limit && $i < $max; $i++)
		{
			$this->intro_items[$i] = &$items[$i];
		}

		$this->columns = max(1, $params->def('num_columns', 1));
		$order = $params->def('multi_column_order', 1);

		if ($order == 0 && $this->columns > 1)
		{
			// call order down helper
			$this->intro_items = ContentHelperQuery::orderDownColumns($this->intro_items, $this->columns);
		}

		// The remainder are the links.
		for ($i = $numLeading + $numIntro; $i < $max; $i++)
		{
			$this->link_items[$i] = &$items[$i];
		}

		$this->assignRef('article', $article);
		$this->assignRef('params' , $params);
		$this->assignRef('user'   , $user);
		$this->assignRef('access',		$access);
		$this->assignRef('items'  , $items);
		$this->assign('total'  , $total);
		$this->assign('action', 	$uri->toString());
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('state', $state);

		parent::display($tpl);
	}
}
?>
