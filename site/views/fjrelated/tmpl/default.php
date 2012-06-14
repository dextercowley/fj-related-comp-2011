<?php 
/**
 * @version		$Id: default.php 125 2011-05-20 16:03:33Z dextercowley $
 * @package		Site
 * @subpackage	com_fjrelated
 * @copyright	Copyright (C) 2009 - 2010 Mark Dexter. Portions Copyright(C) Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

?>
<div class="category-list<?php echo $this->pageclass_sfx;?>">

	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>

	<?php if ($this->params->get('showTitle', 1) OR $this->params->get('page_subheading')) : ?>
	<h2>
		<?php echo $this->escape($this->params->get('page_subheading')); ?>
		<?php if ($this->params->get('showTitle')) : ?>
			<span class="subheading-category"><?php echo $this->article->title;?></span>
		<?php endif; ?>
	</h2>
	<?php endif; ?>

	<?php if ($this->params->get('showText', 1)) : ?>
	<div class="category-desc">
		<?php if ($this->params->get('showText') && $this->article->text) : ?>
			<?php echo JHtml::_('content.prepare', $this->article->text); ?>
		<?php endif; ?>
		<div class="clr"></div>
	</div>
	<?php endif; ?>

	<div class="cat-items">
		<?php echo $this->loadTemplate('items'); ?>
	</div>
</div>
