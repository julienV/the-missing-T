<?php
defined('_JEXEC') or die('Restricted Access');
?>
<div class="missingt-intro"><?php echo JText::_('COM_MISSINGT_COMPONENTS_INTRO'); ?></div>

<form name="adminForm" method="POST" action="index.php">
  <table class="adminlist">
    <thead>
      <tr>
        <th width = '1%'>
          #
        </th>
        <th width = '1%'>
          <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
        </th>
        <th>
          <?php echo JText::_('COM_MISSINGT_COMPONENTS_NAME');?>
        </th>
      </tr>
    </thead>
    
	  <tfoot>
	    <tr>
	      <td colspan="3">
	        <?php echo $this->pagination->getListFooter(); ?>
	      </td>
	    </tr>
	  </tfoot>
	  
    <tbody>
      <?php
      $k = 0;
      $i = 0;
      foreach ($this->items as $key=> $item) {
        $link = "index.php?option=com_missingt&controller=components&task=parse&cid[]=".$item;
        ?>
        <tr class="row<?php echo $k; ?>">
          <td><?php echo $i+1; ?></td>
          <td><?php echo JHTML::_('grid.id', $i, $item);?></td>
          <td><a href="<?php echo $link; ?>"><?php echo $item; ?></a> </td>
        </tr>
        <?php
        $i++;
        $k = 1 - $k;
      }
       ?>
    </tbody>

  </table>
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_missingt" />
<input type="hidden" name="controller" value="components" />
<input type="hidden" name="view" value="components" />
</form>