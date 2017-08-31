<?php
/**
 * Intenso Premium Theme
 * 
 * @category    Itactica
 * @package     Itactica_OrbitSlider
 * @copyright   Copyright (c) 2014-2015 Itactica (http://www.itactica.com)
 * @license     http://getintenso.com/license
 */

$this->startSetup();

$this->getConnection()->addColumn(
    $this->getTable('itactica_orbitslider/slides'),
    'video',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'unsigned'  => true,
        'nullable'  => true,
        'default'   => '',
        'comment'   => 'video'
    )
);

$this->endSetup();
