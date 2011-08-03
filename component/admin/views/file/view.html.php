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
 * View class for the file edit screen
 *
 * @package Joomla
 * @subpackage Missingt
 * @since 0.1
 */
class MissingtViewFile extends JView {

	function display($tpl = null)
	{		
		$option = JRequest::getCmd('option');
		
		$app = &JFactory::getApplication();
		$location = $app->getUserState($option.'.files.location');
		$to       = $app->getUserState($option.'.files.to');
		$from     = $app->getUserState($option.'.files.from');
        
    //initialise variables
    $document = & JFactory::getDocument();
    $user     = & JFactory::getUser();

		//add css and submenu to document
		$document->addStyleSheet('components/com_missingt/assets/css/missingt.css');

		//js
		$document->addScript('http://www.google.com/jsapi');
		JHTML::_('behavior.mootools');
		$document->addScript('components/com_missingt/assets/js/file.js');
		JText::script('COM_MISSINGT_CONFIRM_COPYALL');
		JText::script('COM_MISSINGT_CONFIRM_GOOGLEALL');
		
    //get vars
    $cid      = JRequest::getVar( 'cid', array(0), 'request', 'array' );
    $cid      = $cid[0];
    
    $model    = & $this->getModel();
    $data     = & $this->get( 'Data');
    $target   = $this->get( 'Target');
    $writable = $this->get( 'Writable');
            
    //create the toolbar
    JToolBarHelper::title( JText::_( 'COM_MISSINGT_TRANSLATE_FILE_TITLE' ), 'missingt' );
    JToolBarHelper::custom('copyall', 'copyall', 'copyall', JText::_('COM_MISSINGT_TRANSLATE_FILE_TOOLBAR_COPYALL'), false);
    JToolBarHelper::custom('googleall', 'googleall.png', 'googleall.png', JText::_('COM_MISSINGT_TRANSLATE_FILE_TOOLBAR_GOOGLEALL'), false);
    if ($writable) {
	    JToolBarHelper::apply();
	    JToolBarHelper::save();
    }
    JToolBarHelper::custom('export', 'upload.png', 'upload.png', JText::_('COM_MISSINGT_TRANSLATE_FILE_TOOLBAR_EXPORT'), false);
    JToolBarHelper::spacer();
    JToolBarHelper::cancel();
    JToolBarHelper::spacer();
    JToolBarHelper::help( 'missingt.main', true );
    
    //assign data to template
    $this->assignRef('data',    $data);
    $this->assign('file',       $cid);
    $this->assign('from',       $from);
    $this->assign('to',         $to);
    $this->assign('writable',   $writable);
    $this->assign('target',     $target);
    $this->assign('location',   $location);
    
    parent::display($tpl);
	}
}
?>