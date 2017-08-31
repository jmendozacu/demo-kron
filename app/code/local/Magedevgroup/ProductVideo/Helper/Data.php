<?php
/**
 * @package    Magedevgroup_ProductVideo
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
?>
<?php

class Magedevgroup_ProductVideo_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * @param $simpleUrl
     * @return mixed
     */
    public function convertUrl($simpleUrl)
    {
        $embedUrl = preg_replace(
            "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
            "www.youtube.com/embed/$2",
            $simpleUrl
        );
        return $embedUrl;
    }
}