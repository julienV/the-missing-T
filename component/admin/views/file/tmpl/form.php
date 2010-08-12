<?php
/**
* @version    $Id$ 
* @package    WebCast
* @copyright  Copyright (C) 2009 JLV-Solutions. All rights reserved.
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');
?>

<script language="javascript" type="text/javascript">
  
	function submitbutton(task)
	{
		var form = document.adminForm;
    var validator = document.formvalidator;

		if (task == 'cancel') {
			submitform( task );
		} else if (validator.validate(form.name) === false) {
			alert( "<?php echo JText::_( 'ADD NAME' ); ?>" );
			form.name.focus();
		} else {
			submitform( task );
		}
	}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate">
	
<fieldset class="adminform"><legend><?php echo JText::_( 'COM_MISSINGT_TRANSLATE_KEYS' ); ?></legend>
<table class="admintable">
	<?php foreach ($this->data->from as $key => $value): ?>
	<tr>
		<td class="key" width="10%"><?php echo $key; ?></td>
		<td width="45%"><?php echo $value; ?></td>
		<td width="45%"><textarea name="<?php echo $key; ?>" cols="40" rows="3"><?php echo (isset($this->data->to[$key]) ? $this->data->to[$key] : '' ); ?></textarea></td>
	</tr>
	<?php endforeach; ?>
</table>
</fieldset>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_missingt" />
	<input type="hidden" name="controller" value="files" />
	<input type="hidden" name="file" value="<?php echo $this->file; ?>" />
	<input type="hidden" name="to" value="<?php echo $this->to; ?>" />
	<input type="hidden" name="task" value="" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>