<?php
/**
 * @package		Site
 * @subpackage	com_fjrelated
 * @copyright	Copyright (C) 2009 - 2010 Mark Dexter. Portions Copyright(C) Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::core();

// Create some shortcuts.
$params		= $this->params;
// Get the order by for the date column
switch ($params->get('list_show_date'))
{
	case 'created' :
		$dateOrder = 'a.created';
		break;
	case 'published' :
		$dateOrder = 'a.publish_up';
		break;
	case 'modified' :
		$dateOrder = 'a.modified';
		break;
	default :
		$dateOrder = 'a.created';
		break;
}

$n			= count($this->items);
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$filter 	= JRequest::getString('filter-search', '');
?>

<?php if (empty($this->items) && (!$filter)) : ?>

	<?php if ($this->params->get('show_no_articles',1)) : ?>
	<p><?php echo JText::_('COM_FJRELATED_NO_ARTICLES'); ?></p>
	<?php endif; ?>

<?php else : ?>

<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
	<?php if ($this->params->get('show_headings') || $this->params->get('filter_type') != 'none' || $this->params->get('show_pagination_limit')) :?>
	<fieldset class="filters">
		<?php if ($this->params->get('filter_type') != 'none') :?>
		<legend class="hidelabeltxt">
			<?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?>
		</legend>

		<div class="filter-search">
			<label class="filter-search-lbl" for="filter-search"><?php echo JText::_('COM_FJRELATED_'.$this->params->get('filter_type').'_FILTER_LABEL').'&#160;'; ?></label>
			<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_FJRELATED_FILTER_SEARCH_DESC'); ?>" />
		</div>
		<?php endif; ?>

		<?php if ($this->params->get('show_pagination_limit')) : ?>
		<div class="display-limit">
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
		<?php endif; ?>

		<input type="hidden" name="filter_order" value="" />
		<input type="hidden" name="filter_order_Dir" value="" />
		<input type="hidden" name="limitstart" value="" />
	</fieldset>
	<?php endif; ?>

	<table class="category">
		<?php if ($this->params->get('show_headings')) :?>
		<thead>
			<tr>
				<th class="list-title" id="tableOrdering">
					<?php  echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder) ; ?>
				</th>
				<?php if ($this->params->get('showMatchCount')) : ?>
					<th class="list-match-count" id="tableOrdering2">
						<?php echo JHTML::_('grid.sort',  'COM_FJRELATED_NUM_MATCHES', 'match_count', $listDirn, $listOrder); ?>
					</th>
				<?php endif; ?>
				<?php if ($this->params->get('showMatchList', 0)) : ?>
					<th	class="list-match-list" id="tableOrdering3">
						<?php echo  ($this->params->get( 'keywordLabel' ) ? trim($this->params->get( 'keywordLabel' )) : JText::_('COM_FJRELATED_MATCHING_KEYWORDS_LABEL')); ?>
					</th>
				<?php endif; ?>
				<?php if ($date = $this->params->get('list_show_date')) : ?>
				<th class="list-date" id="tableOrdering4">
					<?php echo JHtml::_('grid.sort', 'COM_FJRELATED_'.$date.'_DATE', $dateOrder, $listDirn, $listOrder); ?>
				</th>
				<?php endif; ?>

				<?php if ($this->params->get('list_show_author',1)) : ?>
				<th class="list-author" id="tableOrdering5">
					<?php echo JHtml::_('grid.sort', 'JAUTHOR', 'author', $listDirn, $listOrder); ?>
				</th>
				<?php endif; ?>

				<?php if ($this->params->get('list_show_hits',1)) : ?>
				<th class="list-hits" id="tableOrdering6">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
				</th>
				<?php endif; ?>
			</tr>
		</thead>
		<?php endif; ?>

		<tbody>

		<?php foreach ($this->items as $i => $article) : ?>
			<?php if ($this->items[$i]->state == 0) : ?>
				<tr class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
			<?php else: ?>
				<tr class="cat-list-row<?php echo $i % 2; ?>" >
			<?php endif; ?>
				<?php if (in_array($article->access, $this->user->getAuthorisedViewLevels())) : ?>

					<td class="list-title">
						<?php // If fjrelated_link, use it for the article link ?>
						<?php if ($article->fjrelated_link) : ?>
							<a href="<?php echo JRoute::_($article->fjrelated_link); ?>">
						<?php else : ?>
							<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid)); ?>">
						<?php endif; ?>
						<?php echo $this->escape($article->title); ?></a>

						<?php if ($article->params->get('access-edit')) : ?>
						<ul class="actions">
							<li class="edit-icon">
								<?php echo JHtml::_('icon.edit',$article, $params); ?>
							</li>
						</ul>
						<?php endif; ?>
					</td>
				<?php if ($this->params->get('showMatchCount')) : ?>
					<td class="list-match-count">
						<?php echo $article->match_count; ?>
					</td>
				<?php endif; ?>
					<?php if ($this->params->get('showMatchList', 0)) : ?>
						<td class="list-match-list">
							<?php $temp_list = $article->match_list; ?>
							<?php natcasesort($temp_list); ?>
							<?php echo implode(', ', $temp_list); ?>
						</td>
					<?php endif; ?>
					<?php if ($this->params->get('list_show_date')) : ?>
					<td class="list-date">
						<?php echo JHtml::_('date',$article->displayDate, $this->escape(
						$this->params->get('date_format', JText::_('DATE_FORMAT_LC3')))); ?>
					</td>
					<?php endif; ?>

					<?php if ($this->params->get('list_show_author',1) && !empty($article->author )) : ?>
					<td class="list-author">
						<?php $author =  $article->author ?>
						<?php $author = ($article->created_by_alias ? $article->created_by_alias : $author);?>
						<?php echo $author; ?>
					</td>
					<?php endif; ?>

					<?php if ($this->params->get('list_show_hits',1)) : ?>
					<td class="list-hits">
						<?php echo $article->hits; ?>
					</td>
					<?php endif; ?>

				<?php else : // Show unauth links. ?>
					<td>
						<?php
							echo $this->escape($article->title).' : ';
							$menu		= JFactory::getApplication()->getMenu();
							$active		= $menu->getActive();
							$itemId		= $active->id;
							$link = JRoute::_('index.php?option=com_users&view=login&Itemid='.$itemId);
							$returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug));
							$fullURL = new JURI($link);
							$fullURL->setVar('return', base64_encode($returnURL));
						?>
						<a href="<?php echo $fullURL; ?>" class="register">
							<?php echo JText::_( 'COM_FJRELATED_REGISTER_TO_READ_MORE' ); ?></a>
					</td>
				<?php endif; ?>
				</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<?php if (($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
	<div class="pagination">

		<?php if ($this->params->def('show_pagination_results', 1)) : ?>
		 	<p class="counter">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>
		<?php endif; ?>

		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<?php endif; ?>
</form>
<?php endif; ?>
