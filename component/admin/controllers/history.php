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
class MissingtControllerHistory extends JController
{
  
  function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add',  'display' );
	}
  
  
	function display() 
	{	
		// Set the view and the model
		$view = JRequest::getVar( 'view', 'history' );
		$layout = JRequest::getVar( 'layout', 'default' );
		
		$view = & $this->getView( $view, 'html' );
		
		$model = & $this->getModel( 'history' );
		$view->setModel( $model, true );
		
		$view->setLayout( $layout );
		
		// Display the view
		$view->display();		
	}	
	
	function changes()
	{
		// Set the view and the model
		$view = JRequest::getVar( 'view', 'history' );
		$layout = JRequest::getVar( 'layout', 'changes' );
		
		$view = & $this->getView( $view, 'html' );
		
		$model = & $this->getModel( 'history' );
		$view->setModel( $model, true );
		
		$view->setLayout( $layout );
		
		// Display the view
		$view->changes();		
	}
	
	function cancel()
	{
		$this->setRedirect( 'index.php?option=com_missingt&controller=files&view=files' );
		$this->redirect();
	}
	
	function export()
	{
		// Set the view and the model
		$view = JRequest::getVar( 'view', 'history' );
		$layout = JRequest::getVar( 'layout', 'export' );
		
		$view = & $this->getView( $view, 'raw' );
		
		$model = & $this->getModel( 'history' );
		$view->setModel( $model, true );
		
		$view->setLayout( $layout );
		
		// Display the view
		$view->export();		
	}
	
	function restore()
	{
		$cid = JRequest::getVar('cid', array(), 'array');
		$cid = $cid[0];
		$model = & $this->getModel( 'history' );
		
		if ($model->restore())
		{
			$msg = JText::_('COM_MISSINGT_HISTORY_VERSION_RESTORED');
			$msgtype = 'message';
		}
		else
		{
			$msg = $model->getError();
			$msgtype = 'error';			
		}
		$this->setRedirect( 'index.php?option=com_missingt&controller=history&cid[]='.$cid, $msg, $msgtype );
		$this->redirect();
	}
	
	function remove()
	{
		$cids = JRequest::getVar('cid', array(), 'array');
		$model = & $this->getModel( 'history' );
		
		$target = $model->getTarget();
		
		if ($model->remove($cids))
		{
			$msg = JText::_('COM_MISSINGT_HISTORY_VERSIONS_REMOVED');
			$msgtype = 'message';
		}
		else
		{
			$msg = $model->getError();
			$msgtype = 'error';			
		}
		$this->setRedirect( 'index.php?option=com_missingt&controller=history&file='.urlencode($target), $msg, $msgtype );
		$this->redirect();		
	}
}
?>
