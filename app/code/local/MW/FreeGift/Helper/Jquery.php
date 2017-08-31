<?php
/**
 * User: Anh TO
 * Date: 12/13/13
 * Time: 5:05 PM
 */

class MW_FreeGift_Helper_Jquery extends Mage_Core_Helper_Abstract{
    /**
     * Name library directory.
     */
    const NAME_DIR_JS = 'mw_js/';

    /**
     * List files for include.
     *
     * @var array
     */
    protected $_files = array(
        /*'jquery.js',
        'jquery.noconflict.js',*/
        /*'jquery.ezpz_tooltip.js',
        'jquery.tmpl.min.js',
        'jquery.easing.1.3.js',
        'bcarousel.js',
        'heartcode-canvasloader-min-0.9.1.js',
        'fancybox/jquery.fancybox.js',*/
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
}