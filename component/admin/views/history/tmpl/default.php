<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML::_('behavior.tooltip');

jimport('joomla.utilities.date');
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

	function submitbutton(task)
	{
		var form = document.adminForm;

		if (task == 'export'){
			document.adminForm.format.value = 'raw';
			submitform( task );
		} else {
			submitform( task );
		}
	}
</script>

<div id="missingtmain">
<h3><?php echo $this->target; ?></h3>
<form action="index.php" method="post" id="adminForm" name="adminForm">

<div id="editcell">
  <table class="adminlist" id="files-translations">
  <thead>
    <tr>
      <th width="5">
        <?php echo JText::_( 'NUM' ); ?>
      </th>
      <th width="20">
        <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
      </th>
      <th class="title">
        <?php echo JText::_('Date'); ?>
      </th>
      <th>
        <?php echo JText::_('COM_MISSINGT_HEADER_NOTE'); ?>
      </th>
      <th width="8%" nowrap="nowrap">
        <?php echo JText::_('COM_MISSINGT_CHANGES_AUTHOR'); ?>
      </th>
    </tr>
  </thead>
  
  <tfoot>
    <tr>
      <td colspan="5">
        <?php echo $this->pagination->getListFooter(); ?>
      </td>
    </tr>
  </tfoot>
  
  <tbody>
  <?php
  $k = 0;
  for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
  {
    $row = &$this->rows[$i];

    $link   = JRoute::_( 'index.php?option=com_missingt&controller=history&task=changes&cid[]='. $row->id );
    
    $date = new JDate($row->last_modified);

    //$checked  = JHTML::_('grid.checkedout',   $row, $i );

    ?>
    <tr class="<?php echo "row$k"; ?>">
      <td>
        <?php echo $this->pagination->getRowOffset( $i ); ?>
      </td>
			<td>
				<input type="checkbox" onclick="isChecked(this.checked);" value="<?php echo $row->id; ?>" name="cid[]" id="cb<?php echo $i; ?>"/>
        <?php //echo $checked; ?>
      </td>
      <td>
        <?php echo JHTML::link($link, $date->toFormat() ); ?>
      </td>
      <td>
        <?php echo $row->note; ?>
      </td>
      <td>
        <?php echo $row->username; ?>
      </td>
    </tr>
    <?php
    $k = 1 - $k;
  }
  ?>
  </tbody>
  </table>
</div>

<input type="hidden" name="option" value="com_missingt"/>
<input type="hidden" name="controller" value="history" />
<input type="hidden" name="view" value="history" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<input type="hidden" name="format" value="<?php echo 'html'; ?>" />
</form>
</div>