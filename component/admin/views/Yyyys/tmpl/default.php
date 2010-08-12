<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML::_('behavior.tooltip');

//Ordering allowed ?
$ordering = ($this->lists['order'] == 'o.ordering');
?>

<div id="missingtmain">
<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm">
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
      echo $this->lists['state'];
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
        <?php echo JHTML::_('grid.sort',  'Name', 'o.name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
      </th>
      <th class="title" nowrap="nowrap"><?php echo JText::_('ALIAS'); ?></th>
      <th width="5%" nowrap="nowrap"><?php echo JHTML::_('grid.sort',  'Published', 'o.published', $this->lists['order_Dir'], $this->lists['order'] ); ?>
      </th>
      <th width="8%" nowrap="nowrap">
        <?php echo JHTML::_('grid.sort',  'Order', 'r.ordering', $this->lists['order_Dir'], $this->lists['order'] ); ?>
        <?php echo JHTML::_('grid.order',  $this->items ); ?>
      </th>
      <th width="1%" nowrap="nowrap">
        <?php echo JHTML::_('grid.sort',  'ID', 'o.id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
      </th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="9">
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

    $link   = JRoute::_( 'index.php?option=com_missingt&controller=yyyy&task=edit&cid[]='. $row->id );

    $checked  = JHTML::_('grid.checkedout',   $row, $i );
    $published  = JHTML::_('grid.published', $row, $i );

    ?>
    <tr class="<?php echo "row$k"; ?>">
      <td>
        <?php echo $this->pagination->getRowOffset( $i ); ?>
      </td>
      <td>
        <?php echo $checked; ?>
      </td>
      <td>
        <?php
        if (  JTable::isCheckedOut($this->user->get ('id'), $row->checked_out ) ) {
          echo $row->name;
        } else {
        ?>
          <a href="<?php echo $link; ?>" title="<?php echo JText::_( 'Edit Yyyy' ); ?>">
            <?php echo $row->name; ?></a>
        <?php
        }
        ?>
      </td>
      <td align="center"><?php echo $row->alias;?></td>
      <td align="center"><?php echo $published;?></td>
      <td class="order">
        <span><?php echo $this->pagination->orderUpIcon( $i, $i > 0 , 'orderup', 'Move Up', $ordering ); ?></span>
        <span><?php echo $this->pagination->orderDownIcon( $i, $n, $i < $n, 'orderdown', 'Move Down', $ordering ); ?></span>
        <?php $disabled = true ?  '' : 'disabled="disabled"'; ?>
        <input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
      </td>
      <td align="center">
        <?php echo $row->id; ?>
      </td>
    </tr>
    <?php
    $k = 1 - $k;
  }
  ?>
  </tbody>
  </table>
</div>

<input type="hidden" name="controller" value="yyyy" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
</form>
</div>