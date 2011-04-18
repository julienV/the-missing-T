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
 * View class for the Missingt home screen
 *
 * @package Joomla
 * @subpackage Missingt
 * @since 0.1
 */
class MissingtViewComponents extends JView {
	
  function display($tpl = null) 
  {
		//initialise variables
		$document	= & JFactory::getDocument();
		$user 		= & JFactory::getUser();
    $uri  =& JFactory::getURI();

		//add css and submenu to document
		$document->addStyleSheet('components/com_missingt/assets/css/missingt.css');
    $document->setTitle(JText::_('COM_MISSINGT_VIEW_COMPONENTS_TITLE'));
		
		//build toolbar
		JToolBarHelper::title( JText::_( 'COM_MISSINGT_VIEW_COMPONENTS_TITLE' ), 'missingt' );
    JToolBarHelper::custom('parse', 'parse','parse', JText::_("COM_MISSINGT_TRANSLATE_FILE_TOOLBAR_PARSE"), true );
    
		MissingtAdminHelper::buildMenu();
		
		$state = &$this->get('State');
    $search           = $state->get( 'search', '');
		
    $items      = & $this->get('Data');
    $total      = & $this->get( 'Total' );
    $pagination = & $this->get( 'Pagination' );
    
    $lists = array();
    $lists['search'] = $search;
    
    $this->assignRef('items', $items);
    $this->assignRef('pagination',  $pagination);
    $this->assignRef('lists',  $lists);
    
    parent::display($tpl);
  }
}

?>