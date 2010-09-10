<?php
/**
* @version    $Id$ 
* @package    missingt
* @copyright  Copyright (C) 2009 JLV-Solutions. All rights reserved.
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
?>
<script type="text/javascript" src="http://www.google.com/jsapi">
</script>
<script type="text/javascript">
  google.load("language", "1");
</script>
<script language="javascript" type="text/javascript">

	window.addEvent('domready', function(){
		$$('.mtcopy').addEvent('click', function(event){
			elcopy(event.target);
		});

		$$('.mtgtranslate').addEvent('click', function(event){
			elgtranslate(event.target);
		});
	});
	

	function submitbutton(task)
	{
		var form = document.adminForm;

		if (task == 'copyall') {
			if (confirm("<?php echo JText::_('COM_MISSINGT_CONFIRM_COPYALL'); ?>")) {
				$$('.mtcopy').each(function(element){
					elcopy(element);
				});
			}
			return;
		}
		if (task == 'googleall') {
			if (confirm("<?php echo JText::_('COM_MISSINGT_CONFIRM_GOOGLEALL'); ?>")) {
				$$('.mtcopy').each(function(element){
					elgtranslate(element);
				});
			}
			return;
		}
		if (task == 'cancel') {
			submitform( task );
		} else if (task == 'export'){
			document.adminForm.format.value = 'raw';
			submitform( task );
		} else {
			submitform( task );
		}
	}

	function elcopy(element)
	{
		var tr = $(element).getParent().getParent();
		tr.getElement('.dest').value = tr.getElement('.src').getText();
	}

	function elgtranslate(element)
	{
		var tr = $(element).getParent().getParent();
		var content = tr.getElement('.src').innerHTML;
		google.language.translate(content, "<?php $ln = (explode('-',$this->from)); echo $ln[0]; ?>", "<?php $ln = (explode('-',$this->to)); echo $ln[0]; ?>", function(result) {
			  if (!result.error) {
				  var div = new Element('div').setHTML(result.translation); // trick to convert back html entities
				  tr.getElement('.dest').value = div.innerHTML;
			  }
			});
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
			<th width="5px">#</th>
			<th width="10%"><?php echo JText::_('COM_MISSINGT_VIEW_FILE_HEADER_KEY'); ?></th>
			<th width="45%"><?php echo $this->from; ?></th>
			<th width="5px"></th>
			<th width="45%"><?php echo $this->to; ?></th>
		</tr>
	</thead>
	<?php $k = 1;?>
	<?php foreach ($this->data->from as $key => $value): ?>
	<?php $trans = (isset($this->data->to[$key]) ? $this->data->to[$key] : '' ); ?>
	<tr>
		<td width="5px"><?php echo $k++; ?></td>
		<td class="key" width="10%"><?php echo (strpos($key, 'not_a_key_line') === 0 ? JText::_('COM_MISSINGT_COMMENT'): $key); ?></td>
		<td width="45%" class="src"><?php echo $value; ?></td>
		<td class="buttons">
			<?php if (empty($trans)): ?>
			<?php echo JHTML::image( JURI::root().'administrator/components/com_missingt/assets/images/copy_16.png', 
			                    JText::_('COM_MISSINGT_COPY'), array('class' => 'mtcopy')); ?>
			<?php echo JHTML::image( JURI::root().'administrator/components/com_missingt/assets/images/google_16.png', 
			                    JText::_('COM_MISSINGT_GOOGLE_TRANSLATE'), array('class' => 'mtgtranslate')); ?>
			<?php endif; ?>
		</td>
		<td width="45%"><textarea name="KEY_<?php echo $key; ?>" cols="40" rows="3" class="dest<?php echo (empty($trans) ? ' no-trans':'' );?>"><?php echo $trans; ?></textarea></td>
	</tr>
	<?php endforeach; ?>
</table>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_missingt" />
	<input type="hidden" name="controller" value="files" />
	<input type="hidden" name="cid[]" value="<?php echo $this->file; ?>" />
	<input type="hidden" name="from" value="<?php echo $this->from; ?>" />
	<input type="hidden" name="to" value="<?php echo $this->to; ?>" />
	<input type="hidden" name="location" value="<?php echo $this->location; ?>" />
	<input type="hidden" name="format" value="<?php echo 'html'; ?>" />
	<input type="hidden" name="task" value="" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>