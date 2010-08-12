<?php
/**
* @version    $Id$ 
* @package    Missingt
* @copyright	Copyright (C) 2008 Julien Vonthron. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Missingt is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include library dependencies
jimport('joomla.filter.input');

class MissingtTableYyyy extends JTable {
	
	var $id;
	
	var $name;
	
	var $alias;
	
	var $description;
  
  var $checked_out;
  var $checked_out_time;    
  var $ordering;
  var $published;
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	function __construct(& $db) 
	{
		parent::__construct('#__missingt_yyyy', 'id', $db);
	}
	
	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 * @since 1.0
	 */
	function check()
	{
    $alias = JFilterOutput::stringURLSafe($this->name);

    if(empty($this->alias) || $this->alias === $alias ) {
      $this->alias = $alias;
    }
    
		return true;
	}
}