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
class MissingtViewComponent extends JView 
{	
	function export($tpl = null)
	{		
    $document = & JFactory::getDocument();
    $document->setMimeEncoding('text/plain');
    $date = gmdate('D, d M Y H:i:s', time()).' GMT';
		$document->setModifiedDate($date);		
        
		$target = basename($this->get('Target'));
		JResponse::setHeader( 'Content-Disposition', 'attachment; filename='.$target);
		$data   = & $this->get( 'Result');
		echo($data);
	}
	
	function exportmissing($tpl = null)
	{		
    $document = & JFactory::getDocument();
    $document->setMimeEncoding('text/plain');
    $date = gmdate('D, d M Y H:i:s', time()).' GMT';
		$document->setModifiedDate($date);		
        
		$target = basename($this->get('Target'));
		JResponse::setHeader( 'Content-Disposition', 'attachment; filename='.$target);
		$data   = & $this->get( 'ResultMissing');
		echo($data);
	}
}
?>