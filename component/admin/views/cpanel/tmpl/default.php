<?php
/**
* @version    $Id$ 
* @package    Missingt
* @copyright  Copyright (C) 2008 Julien Vonthron. All rights reserved.
* @license    GNU/GPL, see LICENSE.php
* Missingt is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

?>
<div id="cpanel">

	<div class="icon-wrapper">
		<div class="icon">
			<a href="index.php?option=com_missingt&view=files">
				<?php echo JHtml::_('image', JURI::base().'components/com_missingt/assets/images/icon-48-language.png', NULL, NULL, true); ?>
				<span><?php echo JText::_( 'COM_MISSINGT_VIEW_TRANSLATIONS_TITLE' ); ?></span></a>
		</div>
	</div>

	<div class="icon-wrapper">
		<div class="icon">
			<a href="index.php?option=com_missingt&view=components">
				<?php echo JHtml::_('image', JURI::base().'components/com_missingt/assets/images/icon-48-extension.png', NULL, NULL, true); ?>
				<span><?php echo JText::_( 'COM_MISSINGT_VIEW_COMPONENTS_TITLE' ); ?></span></a>
		</div>
	</div>

</div>