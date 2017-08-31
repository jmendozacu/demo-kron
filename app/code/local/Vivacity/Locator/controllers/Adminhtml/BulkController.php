<?php
class Vivacity_Locator_Adminhtml_BulkController extends Mage_Adminhtml_Controller_Action
{
	
  	protected function _initAction()
    	{
        	$this->loadLayout()->_setActiveMenu('locator/set_time')->_addBreadcrumb('locator Manager','locator Manager');
       		return $this;
     	}
      	public function indexAction()
      	{
         	$this->_initAction();
		$this->loadLayout()->_addContent($this->getLayout()->createBlock('vivacity_locator/adminhtml_bulk'))->renderLayout();

  		
      	}
      	public function newAction() {
		$file_handle = fopen($_FILES['bulkcsv']['tmp_name'], "r+");
		$count;
		while ($line_of_text = fgetcsv($file_handle, 50000)) {
			//$line_of_text = fgetcsv($file_handle, 50000);

			if($line_of_text[0] !=  ''){
				if($line_of_text[11] !=  ''){
					$fileBase	 = basename($line_of_text[11]); 
					$img_file	 = $line_of_text[11];
					$file_loc	 = getcwd()."/media/storeLocator/".$fileBase;
					$ch		 = curl_init();
					curl_setopt($ch, CURLOPT_POST, 0); 
					curl_setopt($ch,CURLOPT_URL,$img_file); 
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
					$file_content		 = curl_exec($ch);
					curl_close($ch);
	 				$downloaded_file	 = fopen($file_loc, 'w');
					fwrite($downloaded_file, $file_content);
					fclose($downloaded_file);
				}
				if($line_of_text[13] !=  ''){
					$fileBased	 = basename($line_of_text[13]); 
					$img_filed	 = $line_of_text[13];
					$file_locd	 = getcwd()."/media/storeLocator/".$fileBased;
					$chd		 = curl_init();
					curl_setopt($chd, CURLOPT_POST, 0); 
					curl_setopt($chd,CURLOPT_URL,$img_filed); 
					curl_setopt($chd, CURLOPT_RETURNTRANSFER, 1); 
					$file_contentd		 = curl_exec($chd);
					curl_close($chd);
	 				$downloaded_filed	 = fopen($file_locd, 'w');
					fwrite($downloaded_filed, $file_contentd);
					fclose($downloaded_filed);
				}
			}
			$customTable		= Mage::getSingleton('core/resource')->getTableName('locator');
			$config  = Mage::getConfig()->getResourceConnectionConfig("default_setup");
			$dbinfo  = array("host" => $config->host,
            				 "user" => $config->username,
            				 "pass" => $config->password,
            				 "dbname" => $config->dbname
					);
			$dbname			= $dbinfo["dbname"];
			$resource		= Mage::getSingleton('core/resource');
			$writeConnection	= $resource->getConnection('core_write');
			$mysqlQuery		= "INSERT INTO `".$dbname."`.`".$customTable."`(`locator_id`, `store_name`, `address`, `city`, `zip_code`, `country`, `state`, `faxno`, `phone`, `email`, `status`, `description`, `custom_icon`, `position`, `store_image`, `lat`, `long`) VALUES('null', '".$line_of_text[0]."', '".$line_of_text[1]."', '".$line_of_text[2]."', '".$line_of_text[3]."', '".$line_of_text[4]."', '".$line_of_text[5]."', '".$line_of_text[6]."', '".$line_of_text[7]."', '".$line_of_text[8]."', '".$line_of_text[9]."', '".$line_of_text[10]."', '".$fileBase."', '".$line_of_text[12]."', '".$fileBased."', '".$line_of_text[14]."', '".$line_of_text[15]."')";
	$writeConnection->query($mysqlQuery);
$count++;
          }
		if($count != 0){
			Mage::getSingleton('adminhtml/session')->addSuccess('CSV successfully Uploaded');
			$this->_redirect('*/adminhtml_index/');
		} else {
			Mage::getSingleton('adminhtml/session')->addError('There was error with CSV data');
			$this->_redirect('*/adminhtml_index/');
		}
	}

      
}
