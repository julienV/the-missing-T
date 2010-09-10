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
 * View class for the Missingt home screen
 *
 * @package Joomla
 * @subpackage Missingt
 * @since 0.1
 */
class MissingtViewCpanel extends JView {

	function display($tpl = null)
	{
		//Load pane behavior
		jimport('joomla.html.pane');

		//initialise variables
		$document	= & JFactory::getDocument();
		$pane   	= & JPane::getInstance('sliders');
		$user 		= & JFactory::getUser();

		MissingtAdminHelper::buildMenu();
		
		//build toolbar
		JToolBarHelper::title( JText::_( 'COM_MISSINGT_VIEW_CPANEL_TITLE' ), 'missingt' );
    JToolBarHelper::preferences('com_Missingt', '360');
		JToolBarHelper::help( 'missingt.main', true );

		//add css and submenu to document
		$document->addStyleSheet('components/com_missingt/assets/css/missingt.css');

		//assign vars to the template
		$this->assignRef('pane'			, $pane);
		$this->assignRef('user'			, $user);

		parent::display($tpl);

	}	

  /**
   * Creates the buttons view
   *
   * @param string $link targeturl
   * @param string $image path to image
   * @param string $text image description
   * @param boolean $modal 1 for loading in modal
   */
  function quickiconButton( $link, $image, $text, $modal = 0 )
  {
    //initialise variables
    $lang     = & JFactory::getLanguage();
      ?>

    <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
      <div class="icon">
        <?php
        $html = JHTML::_('image', 'administrator/images/'.$image, $text ) . '<span>' . $text .'</span>';
        if ($modal == 1) {
          JHTML::_('behavior.modal');
          $imagelink = JHTML::link( $link.'&tmpl=component', 
                                    $html, 
                                    array('style' => "cursor:pointer", 'class' => 'modal', 'rel' => "{handler: 'iframe', size: {x: 650, y: 400}}")
                                    );
        } 
        else {        	
          $imagelink = JHTML::link( $link, 
                                    $html
                                    );
        }
        echo $imagelink;
        ?>
      </div>
    </div>
    <?php
  }
}
?>