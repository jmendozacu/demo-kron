<?php
/*
 * Developer: Michael Jacky
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 */
if (version_compare(phpversion(), '5.2.0', '<')===true) {
    echo  '<div style="font:12px/1.35em arial, helvetica, sans-serif;"><div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;"><h3 style="margin:0; font-size:1.7em; font-weight:normal; text-transform:none; text-align:left; color:#2f2f2f;">Whoops, it looks like you have an invalid PHP version.</h3></div><p>Magento supports PHP 5.2.0 or newer. <a href="http://www.magentocommerce.com/install" target="">Find out</a> how to install</a> Magento using PHP-CGI as a work-around.</p></div>';
    exit;
}

/**
 * Error reporting
 */
error_reporting(E_ALL | E_STRICT);

// CMSIDEAS MAGENTO FULL PAGE CACHE PREPROCESSING...
function glace_cache_getFilePath($relativePath)
{
    $path = dirname(__FILE__);
    return is_file($path . $relativePath) && is_readable($path . $relativePath) ? $path . $relativePath : (is_file($path . '/..' . $relativePath) && is_file($path . '/..' . $relativePath) ? $path . '/..' . $relativePath : null);
}

$maintenanceFile = 'maintenance.flag';

if (file_exists($maintenanceFile)) {
    include_once glace_cache_getFilePath('/errors/503.php');
    exit;
}

$mainpage = glace_cache_getFilePath('/lib/Glace/Fullpagecache/Mainpage.php');
if($mainpage) {
    include_once $mainpage;

    $booster = Glace_Fullpagecache_Mainpage::getInstance(dirname(__FILE__));

    if (!$booster->_enableCache)
    {
        $booster->glaceDebugTop("CMSIDEAS MAGENTO FULL PAGE CACHE DISABLED.");
    }

    if ($result = $booster->loadPage())
    {
        echo $result;
        exit;
    }
    if($booster->_enableCache && $booster->_canCachePage==false)
    {
        if($booster->checkAdmin())
        {
            $booster->glaceDebugTop("<div style='display:inline-block;
	padding:10px;
	background-color: red;
	position: fixed;
	bottom: 0px;
	left:0px;color:#ffffff;font-weight:bold;'>Sorry, The CSM-IDEAS Full Page Cache not create cache in backend. :)</div>");
        }
        else
        {
            $booster->glaceDebugTop("<div style='display:inline-block;
	padding:10px;
	background-color: red;
	position: fixed;
	bottom: 0px;
	left:0px;color:#ffffff;font-weight:bold;'>Cache can't create. Please check again steps install and config, guide for this extension. Thanks :(</div>");
        }
    }
}
// END 

/**
 ***********************************************************
 * MAGENTO DEFAULT INDEX.PHP CODE
 */
/**
 * Compilation includes configuration file
 */
define('MAGENTO_ROOT', getcwd()); //magento 1.7+ define 

$compilerConfig = glace_cache_getFilePath('/includes/config.php');
if (file_exists($compilerConfig)) {
    include $compilerConfig;
}

$mageFilename = glace_cache_getFilePath('/app/Mage.php');

if (!file_exists($mageFilename)) {
    if (is_dir('downloader')) {
        header("Location: downloader");
    } else {
        echo $mageFilename." was not found";
    }
    exit;
}

require_once $mageFilename;

#Varien_Profiler::enable();

if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
    Mage::setIsDeveloperMode(true);
}

#ini_set('display_errors', 1);

umask(0);

/* Store or website code */
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';

/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';

Mage::run($mageRunCode, $mageRunType);

/**
 * END MAGENTO DEFAULT INDEX.PHP CODE
 ***********************************************************
 */

// CMSIDEAS MAGENTO FULL PAGE CACHE POSTPROCESSING
if(isset($booster) && $booster->_enableCache && $booster->_canCachePage)
{
	$fileContents = ob_get_contents();


	$booster->glaceDebugTop(Mage::helper('fullpagecache')->saveContentToCache($booster->getCacheFilePath(), $fileContents));
    echo $booster->glaceDebug();
}
// END
