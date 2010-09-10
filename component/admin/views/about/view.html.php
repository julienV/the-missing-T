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

jimport( 'joomla.application.component.view');

/**
 * View class for the history screen
 *
 * @package Joomla
 * @subpackage Missingt
 * @since 0.4
 */
class MissingtViewAbout extends JView {

	function display($tpl = null)
	{		
		global $option;
		
		$app = &JFactory::getApplication();
        
    //initialise variables
    $document = & JFactory::getDocument();
    $user     = & JFactory::getUser();
    $uri      =& JFactory::getURI();

		//add css and submenu to document
		$document->addStyleSheet('components/com_missingt/assets/css/missingt.css');
        
    $document->setTitle(JText::_( 'COM_MISSINGT_ABOUT' ));
    JToolBarHelper::title( JText::_( 'COM_MISSINGT_ABOUT' ), 'missingt' );
    
		MissingtAdminHelper::buildMenu();
        
    parent::display($tpl);
	}
}
?>