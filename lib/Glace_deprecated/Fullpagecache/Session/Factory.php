<?php
/*
 * Developer: Michael Jacky
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 */

class Glace_Fullpagecache_Session_Factory
{
    public static function get($type)
    {
        require_once(dirname(__FILE__).'/Universal.php');
        return new Glace_Fullpagecache_Session_Universal($type);
    }
    
}