<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Product
 *
 * @author thanhtung
 */
class Simi_Themeone_Helper_Product extends Mage_Core_Helper_Abstract {
    public function getSortOption($sort_option) {
        $sort = array();
        switch ($sort_option) {
            case 0:
                $sort[0] = '';
                $sort[1] = '';
                break;
            case 1:
                $sort[0] = 'price';
                $sort[1] = 'ASC';
                break;
            case 2:
                $sort[0] = 'price';
                $sort[1] = 'DESC';
                break;
            case 3:
                $sort[0] = 'name';
                $sort[1] = 'ASC';
                break;
            case 4:
                $sort[0] = 'name';
                $sort[1] = 'DESC';
                break;
            default :
                $sort = null;
        }
        return $sort;
    }
}

?>
