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
 * View class for the component files edit screen
 *
 * @package Joomla
 * @subpackage Missingt
 * @since 0.3
 */
class MissingtViewComponent extends JView {

	function display($tpl = null)
	{		
		$option = JRequest::getCmd('option');
		
		$app = &JFactory::getApplication();
        
    //initialise variables
    $document = & JFactory::getDocument();
    $user     = & JFactory::getUser();

		//add css and submenu to document
		$document->addStyleSheet('components/com_missingt/assets/css/missingt.css');

    //get vars
    $cid      = JRequest::getVar( 'cid', array(0), 'request', 'array' );
    $cid      = $cid[0];
    
    $model    = & $this->getModel();
    $data     = & $this->get( 'Data');
    $target   = $this->get( 'Target');
    $writable = $this->get( 'IsWritable');
            
    //create the toolbar
    JToolBarHelper::title( JText::_( 'COM_MISSINGT_COMPONENT_MISSING_TITLE' ), 'missingt' );
    if ($writable) {
	    JToolBarHelper::apply();
	    JToolBarHelper::save();
    }
    JToolBarHelper::custom('export', 'upload.png', 'upload.png', JText::_('COM_MISSINGT_TRANSLATE_FILE_TOOLBAR_EXPORT'), false);
    JToolBarHelper::custom('exportmissing', 'upload.png', 'upload.png', JText::_('COM_MISSINGT_TRANSLATE_FILE_TOOLBAR_EXPORT_MISSING'), false);
    JToolBarHelper::custom('history', 'history', 'history', JText::_('COM_MISSINGT_FILES_TOOLBAR_HISTORY'), false);
    JToolBarHelper::spacer();
    JToolBarHelper::cancel();
    JToolBarHelper::spacer();
    JToolBarHelper::help( 'missingt.main', true );
    
    $type = $app->getUserStateFromRequest( $option.'.component.location', 'location', 'frontend', 'string');
    
    // lists
    $lists = array();
    
    // location
    $options = array();
    $options[] = JHTML::_('select.option', 'frontend', JText::_('COM_MISSINGT_VIEW_FILES_FRONTEND'));
    $options[] = JHTML::_('select.option', 'backend', JText::_('COM_MISSINGT_VIEW_FILES_BACKEND'));
    $lists['location']   = JHTML::_('select.genericlist', $options, 'location', 'class="lg-refresh"', 'value', 'text', $type);
    
    //assign data to template
    $this->assignRef('data',    $data);
    $this->assignRef('lists',   $lists);
    $this->assign('component',  $cid);
    $this->assign('writable',   $writable);
    $this->assign('type',       $type);
//    echo '<pre>';print_r($this); echo '</pre>';exit;
    parent::display($tpl);
	}
}
?>