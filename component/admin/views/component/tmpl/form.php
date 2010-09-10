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

	window.addEvent('domready', function(){

		$$('.lg-refresh').addEvent('change', function(){
			$('mytask').value = "parse";
			document.adminForm.format.value = 'html';
			$('adminForm').submit(); 
		});		
		
	});

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

<form action="index.php" method="post" name="adminForm" id="adminForm" class="component-strings">

<div class="info">
<p><?php echo JText::_('COM_MISSINGT_FILE_TARGET_LABEL'); ?>: <?php echo $this->component; ?></p>
<p class="<?php echo ($this->writable ? 'is-writable' : 'not-writable'); ?>">
<?php if ($this->writable): ?>
<?php echo JHTML::image('administrator/components/com_missingt/assets/images/ok_16.png', JText::_('COM_MISSINGT_FILE_WRITABLE')).' '.JText::_('COM_MISSINGT_FILE_WRITABLE'); ?>
<?php else: ?>
<?php echo JHTML::image('administrator/components/com_missingt/assets/images/warning_16.png', JText::_('COM_MISSINGT_FILE_NOT_WRITABLE')).' '.JText::_('COM_MISSINGT_FILE_NOT_WRITABLE'); ?>
<?php endif; ?>
</p>
</div>
	
<div id="languages_settings">
<p><?php echo JText::_('COM_MISSINGT_VIEW_FILES_LANGUAGE_SOURCE').': '.$this->lists['location']; ?></p>
</div>

<table class="adminlist">
	<thead>
		<tr>
			<th width="5px">#</th>
			<th width="10%"><?php echo JText::_('COM_MISSINGT_VIEW_COMPONENT_HEADER_KEY'); ?></th>
			<th width="45%"><?php echo JText::_('COM_MISSINGT_VIEW_COMPONENT_HEADER_VALUE'); ?></th>
		</tr>
	</thead>
	<?php $k = 1;?>
	<?php foreach (($this->type == 'frontend' ? $this->data->front : $this->data->admin) as $file => $values): ?>
	<tr class="file-section">
		<td colspan="3"># <?php echo $file; ?></td>
	</tr>
		<?php foreach ($values as $key => $value): ?>
		<tr>
			<td width="5px"><?php echo $k++; ?></td>
			<td class="key" width="10%"><?php echo $key; ?></td>
			<td><textarea name="KEY_<?php echo $key; ?>" cols="40" rows="3" class="dest<?php echo (empty($value->value) ? ' no-trans':'' );?>"><?php echo $value->value; ?></textarea></td>
		</tr>
		<?php endforeach; ?>
	<?php endforeach; ?>
</table>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_missingt" />
	<input type="hidden" name="controller" value="components" />
	<input type="hidden" name="view" value="component" />
	<input type="hidden" name="cid[]" value="<?php echo $this->component; ?>" />
	<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
	<input type="hidden" name="task" id="mytask" value="parse" />
	<input type="hidden" name="format" value="html" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>