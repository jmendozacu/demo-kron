<?php
class Kronosav_Loan_Helper_Data extends Mage_Core_Helper_Abstract
{
 /**
     * Name library directory.
     */
    const NAME_DIR_JS = 'jquery/';

    /**
     * List files for include.
     *
     * @var array
     */
    protected $_files = array(
        'jquery.js',
        
    );
	  /**
     * Return path file.
     *
     * @param $file
     *
     * @return string
     */
    public function getJQueryPath($file)
    {
        return self::NAME_DIR_JS . $file;
    }

    /**
     * Return list files.
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->_files;
    }
	public function emailNotify(){
		return Mage::getStoreConfig('options/email/email_options');
	}
}