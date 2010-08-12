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
 * View class for the Missingt Yyyy list
 *
 * @package Joomla
 * @subpackage Missingt
 * @since 0.1
 */
class MissingtViewYyyys extends JView {

	function display($tpl = null)
	{
		global $option, $mainframe;
		
		//Load pane behavior
		jimport('joomla.html.pane');

		//initialise variables
		$document	= & JFactory::getDocument();
		$pane   	= & JPane::getInstance('sliders');
		$user 		= & JFactory::getUser();
    $uri  =& JFactory::getURI();

		//add css and submenu to document
		$document->addStyleSheet('components/com_missingt/assets/css/missingt.css');
		
		//build toolbar
		JToolBarHelper::title( JText::_( 'Missingt - Yyyys' ), 'yyyys' );
    JToolBarHelper::addNewX();
    JToolBarHelper::editListX();
    JToolBarHelper::deleteList();
		JToolBarHelper::help( 'missingt.yyyys', true );
		
		MissingtAdminHelper::buildMenu();
    
    $document->setTitle(JText::_('Missingt - Yyyys'));
        
    $filter_state   = $mainframe->getUserStateFromRequest( $option.$this->getName().'.filter_state',    'filter_state',   '',       'word' );
    $filter_order   = $mainframe->getUserStateFromRequest( $option.$this->getName().'.filter_order',    'filter_order',   'o.name', 'cmd' );
    $filter_order_Dir = $mainframe->getUserStateFromRequest( $option.$this->getName().'.filter_order_Dir',  'filter_order_Dir', '',       'word' );
    $search       = $mainframe->getUserStateFromRequest( $option.$this->getName().'.search', 'search', '', 'string' );
    
    $rows = & $this->get('Data');
    $total    = & $this->get( 'Total' );
    $pagination = & $this->get( 'Pagination' );
    
    // state filter
    $stateopt = array();
    $stateopt[] = JHTML::_('select.option', '', JText::_('- Select State -'));
    $stateopt[] = JHTML::_('select.option', 'P', JText::_('Published'));
    $stateopt[] = JHTML::_('select.option', 'A', JText::_('Archived'));
    $stateopt[] = JHTML::_('select.option', 'U', JText::_('Unpublished'));
    $lists['state'] = JHTML::_('select.genericlist', $stateopt, 'filter_state', 'class="inputbox" onchange="submitform( );" size="1"', 'value', 'text', $filter_state );

    // table ordering
    $lists['order_Dir'] = $filter_order_Dir;
    $lists['order'] = $filter_order;

    // search filter
    $lists['search']= $search;

    $this->assignRef('user',    JFactory::getUser());
    $this->assignRef('items',    $rows);
    $this->assignRef('lists',   $lists);
    $this->assignRef('pagination',  $pagination);
    $this->assignRef('request_url', $uri->toString());
    
    parent::display($tpl);
	}
}
?>