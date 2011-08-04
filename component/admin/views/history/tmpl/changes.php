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
	
<?php echo $this->helper->inline($this->data['old']->strings, $this->data['new']->strings); ?>