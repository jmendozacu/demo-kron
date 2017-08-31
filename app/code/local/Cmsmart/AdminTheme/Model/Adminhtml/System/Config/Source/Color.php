<?php
/*____________________________________________________________________
* Name Themes: Magento responsive admin template
* Author: The Cmsmart Development Team 
* Date Created: 2015
* Websites: http://cmsmart.net
* Technical Support: Forum - http://cmsmart.net/support
* GNU General Public License v3 (http://opensource.org/licenses/GPL-3.0)
* Copyright © 2011-2015 Cmsmart.net. All Rights Reserved.
______________________________________________________________________*/
?>
<?php
class Cmsmart_AdminTheme_Model_Adminhtml_System_Config_Source_Color
{
  public function toOptionArray()
  {
    return array(
      array('value' => 'ffb849', 'label' =>'Main color 1 (#ffb849)'),
      array('value' => '1d82f8', 'label' =>'Main color 2 (#1d82f8)'),
      array('value' => '0ab9d6', 'label' =>'Main color 3 (#0ab9d6)'),
      array('value' => '0ad2ae', 'label' =>'Main color 4 (#0ad2ae)'),
      array('value' => 'f74d4d', 'label' =>'Main color 5 (#f74d4d)')
    );
  }
}
