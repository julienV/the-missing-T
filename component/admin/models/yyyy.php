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

jimport('joomla.application.component.model');

/**
 * Joomla Missingt Component yyyy Model
 *
 * @author Julien Vonthron <julien.vonthron@gmail.com>
 * @package   Missingt
 * @since 0.1
 */
class MissingtModelYyyy extends JModel
{
 /**
   * id
   *
   * @var int
   */
  var $_id = null;

  /**
   * venue data array
   *
   * @var array
   */
  var $_data = null;

  /**
   * Constructor
   *
   * @since 0.9
   */
  function __construct()
  {
    parent::__construct();

    $array = JRequest::getVar('cid',  0, '', 'array');
    $this->setId((int)$array[0]);
  }

  /**
   * Method to set the identifier
   *
   * @access  public
   * @param int event identifier
   */
  function setId($id)
  {
    // Set venue id and wipe data
    $this->_id      = $id;
    $this->_data  = null;
  }

  /**
   * Logic for the event edit screen
   *
   * @access public
   * @return array
   * @since 0.9
   */
  function &getData()
  {
    if ($this->_loadData())
    {

    }
    else  $this->_initData();

    return $this->_data;
  }

  /**
   * Method to checkin/unlock the item
   *
   * @access  public
   * @return  boolean True on success
   * @since 0.9
   */
  function checkin()
  {
    if ($this->_id)
    {
      $obj = & JTable::getInstance('yyyy', 'MissingtTable');
      return $obj->checkin($this->_id);
    }
    return false;
  }

  /**
   * Method to checkout/lock the item
   *
   * @access  public
   * @param int $uid  User ID of the user checking the item out
   * @return  boolean True on success
   * @since 0.9
   */
  function checkout($uid = null)
  {
    if ($this->_id)
    {
      // Make sure we have a user id to checkout the item with
      if (is_null($uid)) {
        $user =& JFactory::getUser();
        $uid  = $user->get('id');
      }
      // Lets get to it and checkout the thing...
      $obj = & JTable::getInstance('yyyy', 'MissingtTable');
      return $obj->checkout($uid, $this->_id);
    }
    return false;
  }

  /**
   * Tests if the obj is checked out
   *
   * @access  public
   * @param int A user id
   * @return  boolean True if checked out
   * @since 0.9
   */
  function isCheckedOut( $uid=0 )
  {
    if ($this->_loadData())
    {
      if ($uid) {
        return ($this->_data->checked_out && $this->_data->checked_out != $uid);
      } else {
        return $this->_data->checked_out;
      }
    } elseif ($this->_id < 1) {
      return false;
    } else {
      JError::raiseWarning( 0, 'Unable to Load Data');
      return false;
    }
  }

  /**
   * Method to store the venue
   *
   * @access  public
   * @return  boolean True on success
   * @since 1.5
   */
  function store($data)
  {
    $user   = & JFactory::getUser();
    $config   = & JFactory::getConfig();

    $row  =& $this->getTable('yyyy', 'MissingtTable');

    // bind it to the table
    if (!$row->bind($data)) {
      JError::raiseError(500, $this->_db->getErrorMsg() );
      return false;
    }

    // sanitise id field
    $row->id = (int) $row->id;

    $nullDate = $this->_db->getNullDate();

    //update item order
    if (!$row->id) {
      $row->ordering = $row->getNextOrder();
    }

    // Make sure the data is valid
    if (!$row->check()) {
      $this->setError($row->getError());
      return false;
    }

    // Store it in the db
    if (!$row->store()) {
      JError::raiseError(500, $this->_db->getErrorMsg() );
      return false;
    }

    return $row->id;
  }
 
	/**
	 * Method to load content Webcast data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	0.1
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = 'SELECT *'.
					' FROM #__missingt_yyyy' .
          ' WHERE id = '.(int) $this->_id;
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the competition data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$this->_data					= $this->getTable('yyyy', 'MissingtTable');
			return (boolean) $this->_data;
		}
		return true;
	}  

  /**
   * Method to save item order
   *
   * @access  public
   * @return  boolean True on success
   * @since 1.5
   */
  function saveorder($cid = array(), $order)
  {
    $row  =& $this->getTable('yyyy', 'MissingtTable');

    // update ordering values
    for( $i=0; $i < count($cid); $i++ )
    {
      $row->load( (int) $cid[$i] );

      if ($row->ordering != $order[$i])
      {
        $row->ordering = $order[$i];
        if (!$row->store()) {
          $this->setError($this->_db->getErrorMsg());
          return false;
        }
      }
    }
    
    return true;
  }
}
?>
