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
 * View class for the Missingt Files list
 *
 * @package Joomla
 * @subpackage Missingt
 * @since 0.1
 */
class MissingtViewFiles extends JView {

	function display($tpl = null)
	{
		$mainframe = &JFactory::getApplication();
  	$option = JRequest::getCmd('option');
		
		//Load pane behavior
		jimport('joomla.html.pane');

		//initialise variables
		$document	= & JFactory::getDocument();
		$pane   	= & JPane::getInstance('sliders');
		$user 		= & JFactory::getUser();
    $uri  =& JFactory::getURI();
    $state = $this->get('state');

		//add css and submenu to document
		$document->addStyleSheet('components/com_missingt/assets/css/missingt.css');
		
		//build toolbar
		JToolBarHelper::title( JText::_( 'COM_MISSINGT_VIEW_TRANSLATIONS_TITLE' ), 'missingt' );
    JToolBarHelper::custom('translate', 'forward.png', 'forward.png', JText::_('COM_MISSINGT_FILES_TOOLBAR_TRANSLATE'), true, true);
    JToolBarHelper::custom('history', 'history', 'history', JText::_('COM_MISSINGT_FILES_TOOLBAR_HISTORY'), true, true);
    JToolBarHelper::help( 'missingt.main', true );
		
		MissingtAdminHelper::buildMenu();
    
    $document->setTitle(JText::_('COM_MISSINGT_VIEW_TRANSLATIONS_TITLE'));
    
    $rows   = & $this->get('Data');
    $languages_src = & $this->get('Languages');
    $total    = & $this->get( 'Total' );
    $pagination = & $this->get( 'Pagination' );
    
        
    $filter_order     = $state->get('filter_order');
    $filter_order_Dir = $state->get('filter_order_Dir');
    $search           = $state->get('search');
    $from = $mainframe->getUserState( $option.'.files.from');
    $to   = $mainframe->getUserState( $option.'.files.to');
    $type = $mainframe->getUserState( $option.'.files.location');    
    
    // lists
    $lists = array();
    
    // source languages
    $options = array();
    foreach($languages_src as $src) {
    	$options[] = JHTML::_('select.option', $src, $src);
    }    
    $lists['from'] = JHTML::_('select.genericlist', $options, 'from', 'class="lg-refresh"', 'value', 'text', $from);
    $lists['to']   = JHTML::_('select.genericlist', $options, 'to', 'class="lg-refresh"', 'value', 'text', $to);
    
    $options = array();
    $options[] = JHTML::_('select.option', 'front', JText::_('COM_MISSINGT_VIEW_FILES_FRONTEND'));
    $options[] = JHTML::_('select.option', 'back', JText::_('COM_MISSINGT_VIEW_FILES_BACKEND'));
    $lists['location']   = JHTML::_('select.genericlist', $options, 'location', 'class="lg-refresh"', 'value', 'text', $type);
    
    // table ordering
    $lists['order_Dir'] = $filter_order_Dir;
    $lists['order'] = $filter_order;

    // search filter
    $lists['search']= $search;

    $this->assignRef('user',        JFactory::getUser());
    $this->assignRef('items',       $rows);
    $this->assignRef('lists',       $lists);
    $this->assignRef('pagination',  $pagination);
    $this->assignRef('request_url', $uri->toString());
    $this->assign('from',           $from);
    $this->assign('to',             $to);
    
    parent::display($tpl);
	}
}
?>