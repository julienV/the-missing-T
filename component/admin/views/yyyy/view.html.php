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
 * View class for the yyyy edit screen
 *
 * @package Joomla
 * @subpackage Missingt
 * @since 0.1
 */
class MissingtViewYyyy extends JView {

	function display($tpl = null)
	{		
    global $mainframe;
    
    //initialise variables
    $document = & JFactory::getDocument();
    $user     = & JFactory::getUser();
    $editor   = & JFactory::getEditor();

		//add css and submenu to document
		$document->addStyleSheet('components/com_missingt/assets/css/missingt.css');

    //get vars
    $cid  = JRequest::getVar( 'cid', array(0), 'post', 'array' );
    $cid = $cid[0];
    
    $model = & $this->getModel();
    $row        = & $this->get( 'Data');
    
    // fail if checked out not by 'me'
    if ($row->id) {
      if ($model->isCheckedOut( $user->get('id') )) {
        JError::raiseWarning( 'SOME_ERROR_CODE', $row->name.' '.JText::_( 'EDITED BY ANOTHER ADMIN' ));
        $mainframe->redirect( 'index.php?option=com_missingt&controller=yyyy' );
      }
    }
    
    //create the toolbar
    if ( $cid ) {
      JToolBarHelper::title( JText::_( 'EDIT YYYY' ), 'yyyyedit' );
      //makes data safe
      JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'description' );
    } else {
      JToolBarHelper::title( JText::_( 'ADD YYYY' ), 'yyyyedit' );
    }
    JToolBarHelper::apply();
    JToolBarHelper::spacer();
    JToolBarHelper::save();
    JToolBarHelper::spacer();
    JToolBarHelper::cancel();
    JToolBarHelper::spacer();
    //JToolBarHelper::help( 'screen.webcast', true );
    
    //assign data to template
    $this->assignRef('row'        , $row);
    $this->assignRef('editor'       , $editor);
    
    parent::display($tpl);
	}
}
?>