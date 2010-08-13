<?php
/**
* @version    $Id$ 
* @package    missingt
* @copyright  Copyright (C) 2009 JLV-Solutions. All rights reserved.
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
?>

<script language="javascript" type="text/javascript">
  
	function submitbutton(task)
	{
		var form = document.adminForm;

		if (task == 'cancel') {
			submitform( task );
		} else if (task == 'export'){
			document.adminForm.format.value = 'raw';
			submitform( task );
		} else {
			submitform( task );
		}
	}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">

<div class="info">
<p><?php echo JText::_('COM_MISSINGT_FILE_TARGET_LABEL'); ?>: <?php echo $this->target; ?></p>
<p class="<?php echo ($this->writable ? 'is-writable' : 'not-writable'); ?>">
<?php if ($this->writable): ?>
<?php echo JHTML::image('administrator/components/com_missingt/assets/images/ok_16.png', JText::_('COM_MISSINGT_FILE_WRITABLE')).' '.JText::_('COM_MISSINGT_FILE_WRITABLE'); ?>
<?php else: ?>
<?php echo JHTML::image('administrator/components/com_missingt/assets/images/warning_16.png', JText::_('COM_MISSINGT_FILE_NOT_WRITABLE')).' '.JText::_('COM_MISSINGT_FILE_NOT_WRITABLE'); ?>
<?php endif; ?>
</p>
<p><?php echo JText::_('COM_MISSINGT_FILE_TOTAL_TRANSLATED'); ?>: <?php echo count($this->data->to).'/'.count($this->data->from); ?></p>
</div>
	
<table class="adminlist">
	<thead>
		<tr>
			<th width="10%"><?php echo JText::_('COM_MISSINGT_VIEW_FILE_HEADER_KEY'); ?></th>
			<th width="45%"><?php echo JText::_('COM_MISSINGT_VIEW_FILE_HEADER_FROM'); ?></th>
			<th width="45%"><?php echo JText::_('COM_MISSINGT_VIEW_FILE_HEADER_TO'); ?></th>
		</tr>
	</thead>
	<?php foreach ($this->data->from as $key => $value): ?>
	<?php $trans = (isset($this->data->to[$key]) ? $this->data->to[$key] : '' ); ?>
	<tr>
		<td class="key" width="10%"><?php echo $key; ?></td>
		<td width="45%"><?php echo $value; ?></td>
		<td width="45%"><textarea name="KEY_<?php echo $key; ?>" cols="40" rows="3" class="<?php echo (empty($trans) ? 'no-trans':'' );?>"><?php echo $trans; ?></textarea></td>
	</tr>
	<?php endforeach; ?>
</table>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_missingt" />
	<input type="hidden" name="controller" value="files" />
	<input type="hidden" name="cid[]" value="<?php echo $this->file; ?>" />
	<input type="hidden" name="to" value="<?php echo $this->to; ?>" />
	<input type="hidden" name="location" value="<?php echo $this->location; ?>" />
	<input type="hidden" name="format" value="<?php echo 'html'; ?>" />
	<input type="hidden" name="task" value="" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>