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
	
<fieldset class="adminform"><legend><?php echo JText::_( 'Parameters' ); ?></legend>
<table class="admintable">
	<tr>
		<td width="20%" class="key hasTip" title="<?php echo JText::_( 'Name' ).'::'; ?>">
			<label for="name"><?php echo JText::_( 'Name' ); ?>:</label>
		</td>
		<td width="80%">
			<input class="inputbox required" type="text" name="name" id="name"
			       size="32" maxlength="250" value="<?php echo $this->row->name?>" />
		</td>
	</tr>
	<tr>
		<td width="20%" class="key hasTip" title="<?php echo JText::_( 'Alias' ).'::'; ?>">
			<label for="alias">
				<?php echo JText::_( 'Alias' ).':'; ?>
			</label>
		</td>
		<td>
			<input class="inputbox" type="text" name="alias" id="alias" size="40" maxlength="100" value="<?php echo $this->row->alias; ?>" />
		</td>
	</tr>
	<tr>
		<td width="20%" class="key hasTip" title="<?php echo JText::_( 'PUBLISHED' ).'::'; ?>">
			<label for="published">
				<?php echo JText::_( 'PUBLISHED' ).':'; ?>
			</label>
		</td>
		<td>
			<?php
			$html = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $this->row->published );
			echo $html;
			?>
		</td>
	</tr>
</table>
</fieldset>

<fieldset class="adminform"><legend><?php echo JText::_( 'Description' ); ?></legend>
<table class="adminform">
	<tr>
		<td><?php
    echo $this->editor->display( 'description',  $this->row->description, '100%', '500', '75', '20', array('pagebreak') ) ;
		?></td>
	</tr>
</table>
</fieldset>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_missingt" />
	<input type="hidden" name="controller" value="yyyy" />
	<input type="hidden" name="cid[]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="task" value="" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>