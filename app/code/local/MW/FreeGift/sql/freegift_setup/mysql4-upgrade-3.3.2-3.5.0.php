<?php
/**
 * Created by PhpStorm.
 * User: manhnt
 * Date: 10/24/14
 * Time: 11:44 AM
 */
$installer = $this;

$installer->startSetup();
/**
 * tao bang salestaff_staff
 */
$installer->run("
    ALTER TABLE {$this->getTable('freegift/salesrule')} ADD COLUMN `enable_social` smallint(6) NOT NULL default '2';
    ALTER TABLE {$this->getTable('freegift/salesrule')} ADD COLUMN `google_plus` smallint(6) NOT NULL default '1';
    ALTER TABLE {$this->getTable('freegift/salesrule')} ADD COLUMN `like_fb` smallint(6) NOT NULL default '1';
    ALTER TABLE {$this->getTable('freegift/salesrule')} ADD COLUMN `share_fb` smallint(6) NOT NULL default '1';
    ALTER TABLE {$this->getTable('freegift/salesrule')} ADD COLUMN `twitter` smallint(6) NOT NULL default '1';
    ALTER TABLE {$this->getTable('freegift/salesrule')} ADD COLUMN `default_message` text NOT NULL default '';
");

$installer->endSetup();