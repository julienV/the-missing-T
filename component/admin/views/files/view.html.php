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
		JToolBarHelper::title( JText::_( 'Missingt - Files' ), 'files' );
    JToolBarHelper::custom('translate', 'forward.png', 'forward.png', 'COM_MISSINGT_FILES_TOOLBAR_TRANSLATE', true, true);
		JToolBarHelper::help( 'missingt.files', true );
		
		MissingtAdminHelper::buildMenu();
    
    $document->setTitle(JText::_('Missingt - Files'));
    
    $rows   = & $this->get('Data');
    $languages_src = & $this->get('Languages');
    $total    = & $this->get( 'Total' );
    $pagination = & $this->get( 'Pagination' );
    
        
    $filter_order     = $mainframe->getUserState( $option.'.files.filter_order',    'filter_order',   'name', 'cmd' );
    $filter_order_Dir = $mainframe->getUserState( $option.'.files.filter_order_Dir',  'filter_order_Dir', '',       'word' );
    $search           = $mainframe->getUserState( $option.'.files.search', 'search', '', 'string' );
    $from = $mainframe->getUserState( $option.'.files.from', 'en-GB', 'request', 'string');
    $to   = $mainframe->getUserState( $option.'.files.to', '', 'request', 'string');
    
    
    // lists
    $lists = array();
    
    // source languages
    $options = array();
    foreach($languages_src as $src) {
    	$options[] = JHTML::_('select.option', $src, $src);
    }    
    $lists['from'] = JHTML::_('select.genericlist', $options, 'from', 'id="lg_from"', 'value', 'text', $from);
    $lists['to']   = JHTML::_('select.genericlist', $options, 'to', 'id="lg_to"', 'value', 'text', $to);
    
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