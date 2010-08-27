<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML::_('behavior.tooltip');

//Ordering allowed ?
$ordering = ($this->lists['order'] == 'o.ordering');
?>

<script language="javascript" type="text/javascript">
	window.addEvent('domready', function(){

		$$('.lg-refresh').addEvent('change', function(){
			$('adminForm').submit(); 
		});		

		$$('.editfile').addEvent('click', function(event){
			var cb = $(event.target).getParent().getParent().getElement('input[id^=cb]');
			cb.setProperty('checked', 'checked');
			isChecked(cb.checked);
			submitbutton('translate'); 
		});		

	});
</script>

<div id="missingtmain">
<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm" name="adminForm">

<fieldset>
<legend><?php echo JText::_('Languages')?></legend>

<div id="languages_settings">
<p>
	<?php echo JText::_('COM_MISSINGT_FILES_LANGUAGE_FROM').$this->lists['from']; ?>
	<?php echo JText::_('COM_MISSINGT_FILES_LANGUAGE_TO').$this->lists['to']; ?>
</p>
<p><?php echo JText::_('COM_MISSINGT_VIEW_FILES_LANGUAGE_SOURCE').': '.$this->lists['location']; ?></p>
</div>

</fieldset>

<table>
<tr>
  <td align="left" width="100%">
    <?php echo JText::_( 'Filter' ); ?>:
    <input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
    <button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
    <button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
  </td>
  <td nowrap="nowrap">
    <?php
      //echo $this->lists['language'];
    ?>
  </td>
</tr>
</table>

<div id="editcell">
  <table class="adminlist">
  <thead>
    <tr>
      <th width="5">
        <?php echo JText::_( 'NUM' ); ?>
      </th>
      <th width="20">
        <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
      </th>
      <th class="title">
        <?php echo JHTML::_('grid.sort',  'Name', 'name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
      </th>
      <th width="8%" nowrap="nowrap">
        <?php echo JHTML::_('grid.sort',  'Status', 'status', $this->lists['order_Dir'], $this->lists['order'] ); ?>
      </th>
    </tr>
  </thead>
  
  <tfoot>
    <tr>
      <td colspan="4">
        <?php echo $this->pagination->getListFooter(); ?>
      </td>
    </tr>
  </tfoot>
  
  <tbody>
  <?php
  $k = 0;
  for ($i=0, $n=count( $this->items ); $i < $n; $i++)
  {
    $row = &$this->items[$i];

    $link   = JRoute::_( 'index.php?option=com_missingt&controller=yyyy&task=edit&name[]='. $row );

    //$checked  = JHTML::_('grid.checkedout',   $row, $i );

    ?>
    <tr class="<?php echo "row$k"; ?>">
      <td>
        <?php echo $this->pagination->getRowOffset( $i ); ?>
      </td>
			<td>
				<input type="checkbox" onclick="isChecked(this.checked);" value="<?php echo $row; ?>" name="cid[]" id="cb<?php echo $i; ?>"/>
        <?php //echo $checked; ?>
      </td>
      <td>
        <?php echo JHTML::link('#', basename($row), array('class' => 'editfile')); ?>
      </td>
      <td>
        <?php echo $this->status[$row]; ?>
      </td>
    </tr>
    <?php
    $k = 1 - $k;
  }
  ?>
  </tbody>
  </table>
</div>

<input type="hidden" name="controller" value="files" />
<input type="hidden" name="view" value="files" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
</form>
</div>