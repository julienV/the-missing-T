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
class MissingtControllerFiles extends JController
{
  
  function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add',  'display' );
		$this->registerTask( 'edit', 'display' );
		$this->registerTask( 'apply', 'save' );		
	}
  
  
	function display() 
	{	
	  switch($this->getTask())
		{
			case 'add'     :
			{
				JRequest::setVar( 'hidemainmenu', 1 );
				JRequest::setVar( 'layout', 'form'  );
				JRequest::setVar( 'view'  , 'file');
				JRequest::setVar( 'edit', false );

				// Checkout the project
				$model = $this->getModel('file');
//				$model->checkout();
			} break;
			case 'edit'    :
			{
        JRequest::setVar( 'hidemainmenu', 1 );
        JRequest::setVar( 'layout', 'form'  );
        JRequest::setVar( 'view'  , 'file');
        JRequest::setVar( 'edit', true );

        // Checkout the project
        $model = $this->getModel('file');
//        $model->checkout();
			} break;
		}
		//default view
    JRequest::setVar( 'view'  , 'files', 'method', false);
		parent::display();
	}
	
	/**
	 * save translatio to file
	 * 
	 */
  function save()
	{
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		$cid = $cid[0];
  	$post = MissingtAdminHelper::getRealPOST();
		// message type for redirect
		$type = 'message';
		
		$model = $this->getModel('file');

		if ($model->store($post)) {
			$msg = JText::_( 'COM_MISSINGT_FILE_SAVED_SUCCESS' );
		} else {
			$msg = JText::_( 'COM_MISSINGT_FILE_SAVED_FAILURE' ).$model->getError();
			$type = 'error';
		}
		
		if ( $this->getTask() == 'save' ) {
			$link = 'index.php?option=com_missingt&view=files';
		}
		else {
			$link = 'index.php?option=com_missingt&controller=files&task=translate&cid[]='.$cid
			      . '&from='.JRequest::getVar('from')
			      . '&to='.JRequest::getVar('to')
			      . '&location='.JRequest::getVar('location')
			      ;
		}
		$this->setRedirect($link, $msg, $type);
		$this->redirect();
	}
	
	/**
	 * export translation to plain text
	 */
  function export()
	{		
		JRequest::setVar('view', 'file');
		JRequest::setVar('layout', 'export');
		parent::display();
	}

	/**
	 * cancel translation
	 */
	function cancel()
	{
		$this->setRedirect( 'index.php?option=com_missingt&view=files' );
	}
	
	/**
	 * prepare display of translate screen
	 */
	function translate()
	{
		$cid = JRequest::getVar( 'cid', array(), 'request', 'array' );
		$cid = $cid[0];
		
		JRequest::setVar( 'view', 'file');
		JRequest::setVar( 'layout', 'form');
		parent::display();
	}
	
	function history()
	{
		$cid = JRequest::getVar( 'cid', array(), 'request', 'array' );
		$cid = $cid[0];
		
		$loc = JRequest::getVar('location');
		
		if ($loc == 'front') {
			$path = 'language'.DS.JRequest::getVar('to').DS.str_replace(JRequest::getVar('from'), JRequest::getVar('to'), $cid);
		}
		else if ($loc == 'sys') {
			$path = 'administrator'.DS.'language'.DS.JRequest::getVar('to').DS.str_replace(JRequest::getVar('from'), JRequest::getVar('to'), $cid);
		}
		else {
			$path = 'administrator'.DS.'language'.DS.JRequest::getVar('to').DS.str_replace(JRequest::getVar('from'), JRequest::getVar('to'), $cid);
		}
		$this->setRedirect( 'index.php?option=com_missingt&controller=history&file='.urlencode($path) );
	}
}
?>
