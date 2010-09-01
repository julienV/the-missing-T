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
class MissingtViewComponent extends JView {

	function display($tpl = null)
	{		
    global $mainframe;
    
    if ($this->getLayout() == 'export') {
    	return $this->_displayExport($tpl);
    }
        
    parent::display($tpl);
	}
	
	function _displayExport($tpl = null)
	{		
		$target = basename($this->get('Target'));
		$data   = & $this->get( 'Result');
		header('Content-Type: text/plain');
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Content-Disposition: attachment; filename='.$target);
		header('Pragma: no-cache');
		echo($data);
		exit;
	}
}
?>