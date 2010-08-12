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
				$model->checkout();
			} break;
			case 'edit'    :
			{
        JRequest::setVar( 'hidemainmenu', 1 );
        JRequest::setVar( 'layout', 'form'  );
        JRequest::setVar( 'view'  , 'file');
        JRequest::setVar( 'edit', true );

        // Checkout the project
        $model = $this->getModel('file');
        $model->checkout();
			} break;
		}
		//default view
    JRequest::setVar( 'view'  , 'files', 'method', false);
		parent::display();
	}
	
  function save()
	{
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		$cid = $cid[0];
		$post	= JRequest::get('post', JREQUEST_ALLOWRAW);
		
		$model = $this->getModel('file');

		if ($model->store($post)) {
			$msg = JText::_( 'COM_MISSINGT_FILE_SAVED_SUCCESS' );
		} else {
			$msg = JText::_( 'COM_MISSINGT_FILE_SAVED_FAILURE' ).$model->getError();
		}
		
		if ( $this->getTask() == 'save' ) {
			$link = 'index.php?option=com_missingt&view=files';
		}
		else {
			$link = 'index.php?option=com_missingt&controller=files&task=translate&cid[]='.$cid;
		}
		$this->setRedirect($link, $msg);
	}
	
  function export()
	{		
		JRequest::setVar('view', 'file');
		JRequest::setVar('layout', 'export');
		JRequest::setVar('format', 'raw');
		JRequest::setVar('tmpl', 'component');
		parent::display();
	}

	function remove()
	{
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

		$model = $this->getModel('files');

		$this->setRedirect( 'index.php?option=com_missingt&view=files' );
	}


	function cancel()
	{
		$this->setRedirect( 'index.php?option=com_missingt&view=files' );
	}
	
	function translate()
	{
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		$cid = $cid[0];
		 
		JRequest::setVar( 'view', 'file');
		JRequest::setVar( 'layout', 'form');
		parent::display();
	}
}
?>
