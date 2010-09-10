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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

/**
 * Joomla Missingt Component Controller
 *
 * @package		Missingt
 * @since 0.1
 */
class MissingtControllerComponents extends JController
{
  function __construct()
  {
    parent::__construct();
    
		$this->registerTask( 'apply',         'save' );	
		$this->registerTask( 'exportmissing', 'export' );		
  }
  
  function display()
  {
    JRequest::setVar('view', 'components');
    parent::display();
  }

  function parse()
  {
    JRequest::setVar('view', 'component');
    JRequest::setVar('layout', 'form');
  	parent::display();
  }

	
	function export()
	{
		// Set the view and the model
		$view   = JRequest::getVar( 'view', 'component' );
		$layout = JRequest::getVar( 'layout', 'export' );
		JRequest::setVar('format', 'raw');
		
		$view = & $this->getView( $view, 'raw' );
		
		$model = & $this->getModel( 'component' );
		$view->setModel( $model, true );
		
		$view->setLayout( $layout );
		
		// Display the view
		$task = JRequest::getVar('task');
		if ($task == 'exportmissing') {
			$view->exportmissing();		
		}
		else {			
			$view->export();
		}
	}

	function cancel()
	{
		$this->setRedirect( 'index.php?option=com_missingt&view=components' );
	}
	
  function save()
	{
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		$cid = $cid[0];
  	$post = MissingtAdminHelper::getRealPOST();
		// message type for redirect
		$type = 'message';
		
		$model = $this->getModel('component');

		if ($model->store($post)) {
			$msg = JText::_( 'COM_MISSINGT_FILE_SAVED_SUCCESS' );
		} else {
			$msg = JText::_( 'COM_MISSINGT_FILE_SAVED_FAILURE' ).$model->getError();
			$type = 'error';
		}
		
		if ( $this->getTask() == 'save' ) {
			$link = 'index.php?option=com_missingt&view=components';
		}
		else {
			$link = 'index.php?option=com_missingt&controller=components&task=parse&cid[]='.$cid
			      . '&location='.JRequest::getVar('location')
			      ;
		}
		$this->setRedirect($link, $msg, $type);
		$this->redirect();
	}
	
	function history()
	{
		$cid = JRequest::getVar( 'cid', array(), 'request', 'array' );
		$cid = $cid[0];
		
		if (JRequest::getVar('location') == 'frontend') {
			$path = 'language'.DS.'en-GB'.DS.'en-GB.'.$cid.'.ini';
		}
		else {
			$path = 'administrator'.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$cid.'.ini';
		}
		$this->setRedirect( 'index.php?option=com_missingt&controller=history&file='.urlencode($path) );
	}
}
?>
