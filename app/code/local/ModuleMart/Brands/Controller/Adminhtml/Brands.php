<?php 
 /**
 * ModuleMart_Brands extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Module-Mart License
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.modulemart.com/license.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to modules@modulemart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * versions in the future. If you wish to customize this extension for your
 * needs please refer to http://www.modulemart.com for more information.
 *
 * @category   ModuleMart
 * @package    ModuleMart_Brands
 * @author-email  modules@modulemart.com
 * @copyright  Copyright 2014 © modulemart.com. All Rights Reserved
 */
class ModuleMart_Brands_Controller_Adminhtml_Brands extends Mage_Adminhtml_Controller_Action{
	/**
	 * upload file and get the uploaded name
	 * @access public
	 * @param string $input
	 * @param string $destinationFolder
	 * @param array $data
	 */
	protected function _uploadAndGetName($input, $destinationFolder, $data){
		try{
			if (isset($data[$input]['delete'])){
				return '';
			}
			else{
				$uploader = new Varien_File_Uploader($input);
				$uploader->setAllowRenameFiles(true);
				$uploader->setFilesDispersion(true);
				$uploader->setAllowCreateFolders(true);
				$result = $uploader->save($destinationFolder);
				return $result['file'];
			}
		}
		catch (Exception $e){
			if ($e->getCode() != Varien_File_Uploader::TMP_NAME_EMPTY){
				throw $e;
			}
			else{
				if (isset($data[$input]['value'])){
					return $data[$input]['value'];
				}
			}
		}
		return '';
	}
}