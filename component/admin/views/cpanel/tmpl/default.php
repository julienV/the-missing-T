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

?>
  <table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
      <td valign="top">
      <table class="adminlist">
        <tr>
          <td>
            <div>
            <?php
            $link = 'index.php?option=com_missingt&view=files';
            $this->quickiconButton( $link, 'query.png', JText::_( 'COM_MISSINGT_VIEW_TRANSLATIONS_TITLE' ) );            
            ?>
            </div>
          </td>
          <td>
            <div>
            <?php
            $link = 'index.php?option=com_missingt&view=components';
            $this->quickiconButton( $link, 'query.png', JText::_( 'COM_MISSINGT_VIEW_COMPONENTS_TITLE' ) );            
            ?>
            </div>
          </td>
        </tr>
      </table>
      </td>
      <td valign="top" width="320px" style="padding: 7px 0 0 5px">
      <?php
      $title = JText::_( 'STATS' );
      echo $this->pane->startPane( 'stat-pane' );
      
      echo $this->pane->startPanel( $title, 'stats' );
      ?>
      <table class="adminlist">
        <tr>
          <td>
            <?php echo JText::_( 'A_stat' ).': '; ?>
          </td>
          <td>
            <b><?php echo '-'; ?></b>
          </td>
        </tr>
      </table>
      <?php
      echo $this->pane->endPanel();
      
      echo $this->pane->endPane();
      ?>
      </td>
    </tr>
    </table>