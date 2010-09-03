<?php
/**
* @version    $Id$ 
* @package    missingt
* @copyright  Copyright (C) 2009 JLV-Solutions. All rights reserved.
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
?>

<div class="info">
<p><?php echo JText::_('COM_MISSINGT_FILE_TARGET_LABEL'); ?>: <?php echo $this->target; ?></p>
<p><?php echo JText::_('COM_MISSINGT_FILE_TOTAL_CHANGES'); ?>: <?php // echo count($this->data->to).'/'.count($this->data->from); ?></p>
</div>
	
<table class="adminlist">
	<thead>
		<tr>
			<th width="5px">#</th>
			<th width="10%"><?php echo JText::_('COM_MISSINGT_VIEW_FILE_HEADER_KEY'); ?></th>
			<th width="45%"><?php echo $this->data->from; ?></th>
			<th width="45%"><?php echo $this->data->to; ?></th>
		</tr>
	</thead>
	<?php $k = 1;?>
	<?php foreach ($this->data->strings as $key => $value): ?>
	<?php $changed = ($value->src != $value->dest); ?>
	<tr class="<?php echo ($changed ? 'changed' : ''); ?>">
		<td width="5px"><?php echo $k++; ?></td>
		<td class="key" width="10%"><?php echo $key; ?></td>
		<td width="45%" class="src"><?php echo $value->src; ?></td>
		<td width="45%" class="dest"><?php echo $value->dest; ?></td>
	</tr>
	<?php endforeach; ?>
</table>