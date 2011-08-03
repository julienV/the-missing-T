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
class MissingtViewHistory extends JView {

	function display($tpl = null)
	{		
		$option = JRequest::getCmd('option');
		
		$app = &JFactory::getApplication();
        
    //initialise variables
    $document = & JFactory::getDocument();
    $user     = & JFactory::getUser();
    $uri      =& JFactory::getURI();

		//add css and submenu to document
		$document->addStyleSheet('components/com_missingt/assets/css/missingt.css');
		
    $filter_order     = $app->getUserState( $option.'.files.filter_order',    'filter_order',   'date', 'cmd' );
    $filter_order_Dir = $app->getUserState( $option.'.files.filter_order_Dir',  'filter_order_Dir', 'DESC',       'word' );

    //get vars
    $cid      = JRequest::getVar( 'cid', array(0), 'request', 'array' );
    $cid      = $cid[0];
    
    $model    = & $this->getModel();
    $rows     = & $this->get( 'History');
    $writable = & $this->get( 'Writable');
    $pagination = & $this->get( 'Pagination' );
    $target   = $this->get( 'Target' );
            
    //create the toolbar
    JToolBarHelper::title( JText::_( 'COM_MISSINGT_TRANSLATE_HISTORY' ), 'missingt' );
    JToolBarHelper::custom('changes', 'changes', 'changes', JText::_('COM_MISSINGT_HISTORY_FILE_TOOLBAR_CHANGES'), true);
    if ($writable) {
    	JToolBarHelper::custom('restore', 'restore', 'restore', JText::_('COM_MISSINGT_HISTORY_FILE_TOOLBAR_RESTORE'), false);
    }
    JToolBarHelper::custom('export', 'upload.png', 'upload.png', JText::_('COM_MISSINGT_HISTORY_FILE_TOOLBAR_EXPORT'), true);
    JToolBarHelper::spacer();
    JToolBarHelper::deleteList();
    JToolBarHelper::back();
    JToolBarHelper::spacer();
    JToolBarHelper::help( 'missingt.main', true );
        
    $document->setTitle(JText::_( 'COM_MISSINGT_TRANSLATE_HISTORY' ). ' - '. basename($target));
    
    // lists
    $lists = array();
    
    // table ordering
    $lists['order_Dir'] = $filter_order_Dir;
    $lists['order'] = $filter_order;
    
    //assign data to template
    $this->rows        = $rows;
    $this->lists       = $lists;
    $this->pagination  = $pagination;
    $this->request_url = $uri->toString();
    $this->writable    = $writable;
    $this->target      = $target;
    
    parent::display($tpl);
	}
	
	function changes($tpl = null)
	{		
		$option = JRequest::getCmd('option');
		
		$app = &JFactory::getApplication();
        
    //initialise variables
    $document = & JFactory::getDocument();
    $user     = & JFactory::getUser();
    $uri      =& JFactory::getURI();

		//add css and submenu to document
		$document->addStyleSheet('components/com_missingt/assets/css/missingt.css');
		
    $filter_order     = $app->getUserState( $option.'.files.filter_order',    'filter_order',   'date', 'cmd' );
    $filter_order_Dir = $app->getUserState( $option.'.files.filter_order_Dir',  'filter_order_Dir', 'DESC',       'word' );
    
    $model    = & $this->getModel();
    $data     = & $this->get( 'Changes');
    $target   = $this->get( 'Target' );
            
    //create the toolbar
    JToolBarHelper::title( JText::_( 'COM_MISSINGT_TRANSLATE_HISTORY_CHANGES' ), 'missingt' );
    JToolBarHelper::back();
    JToolBarHelper::spacer();
    JToolBarHelper::help( 'missingt.main', true );
        
    $document->setTitle(JText::_( 'COM_MISSINGT_TRANSLATE_HISTORY_CHANGES' ). ' - '. basename($target));
        
    //assign data to template
    $this->assignRef('data',        $data);
    $this->assignRef('request_url', $uri->toString());
    $this->assign('target',   $target);
    
    parent::display($tpl);
	}
}
?>