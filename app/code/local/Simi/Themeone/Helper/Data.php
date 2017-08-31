<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_ThemeOne
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * ThemeOne Helper
 * 
 * @category    Magestore
 * @package     Magestore_ThemeOne
 * @author      Magestore Developer
 */
class Simi_Themeone_Helper_Data extends Mage_Core_Helper_Abstract {

    public function addCategory($installer) {
        $model = Mage::getModel('themeone/categories')->getCollection()->getFirstItem();
        if ($model->getPositionId() == null) {
            $categories = Mage::getSingleton('themeone/allcategory')->toOptionArray(true);
            $nCate = count($categories);
            $id = 1;
            for ($i = 0; $i < $nCate; $i++) {
                $category_id = $categories[$i]['value'];
                $category_name = $categories[$i]['label'];
                if ($category_id != null && $category_name != null) {
                    $query = "INSERT INTO `{$installer->getTable('themeone_categories')}` (`name`,`priority`,`category_id`,`category_name`)
                    VALUES ('Position " . $id . "'," . $id . "," . $category_id . ",'" . $category_name . "');";
                    $id++;
                    $installer->run($query);
                }
                if ($id >= 4)
                    break;
            }
            $query = "INSERT INTO `{$installer->getTable('themeone_categories')}` (`name`,`priority`,`category_id`,`category_name`)
                    VALUES ('Position " . $id . "'," . $id . ",-1,'View All Category');";
            $id++;
            $installer->run($query);
        }
    }

    public function addSpotproduct($installer) {
        $model = Mage::getModel('themeone/spotproduct')->getCollection()->getFirstItem();
        $spotproducts = Mage::getModel('themeone/config')->toOptionArray();
        $key = Mage::getModel('themeone/config')->toKeySpotArray();
        if ($model->getData('spotproduct_id') == null) {
            $nSpot = count($spotproducts);
            for ($i = 0; $i < $nSpot; $i++) {
                $query = "INSERT INTO `{$installer->getTable('themeone_spotproduct')}` (`position`,`spotproduct_name`,`spotproduct_key`,`status`)
                    VALUES (".($i+1).",'" . $spotproducts[$i]['label'] . "','" . $key[$i] . "',1);";
                $installer->run($query);
            }
        }
    }

    public function getDataImage($image_type, $image_type_id) {
        $collection = Mage::getModel('themeone/images')->getCollection()->addFieldToFilter('image_type', $image_type)
                ->addFieldToFilter('image_type_id', $image_type_id);
        return $collection;
    }

    public function saveImageStore($images, $storeId, $image_type, $image_type_id, $file, $radio, $phone_type) {
       
        foreach ($images as $item) {
            $mod = Mage::getModel('themeone/images');
         
            $file_name = $file["images_".$phone_type."_id" . $item['options']]['name'];

            $name_image = $this->renameImage($file_name, $image_type_id, $item['options']);

            if ($item['delete'] == 0) {

                $last = $mod->getCollection()->addFieldToFilter('image_type', $image_type)->addFieldToFilter('image_type_id', $image_type_id)->getLastItem()->getData('options') + 1;
                if ($last == null)
                    $last = 1;
                $name_image = $this->renameImage($file_name, $image_type_id, $last);

                $mod->setData('store_id', $storeId);

                if (($name_image != "") && isset($name_image) != NULL) {
                    $mod->setData('image_name', $name_image);
                    $this->createImage($name_image, $storeId, $image_type, $image_type_id, $item['options'], $last,$phone_type);
                   
                    if ($item['options'] == $radio) {
                        $mod->setData('status', 1);
                    } else {
                        $mod->setData('status', 0);
                    }
                  
                    $mod->setData('image_type', $image_type);
                    $mod->setImageTypeId($image_type_id);
                    $mod->setPhoneType($phone_type);
                    $mod->setData('image_delete', 2);
                    $mod->setData('options', $last);
                    
                    $mod->save();
                
                }
            } else if ($item['delete'] == 2) {
                if (($name_image != "") && isset($name_image) != NULL) {
                    $mod->setData('name', $name_image)->setId($item['id']);
                    $this->createImage($name_image, $storeId, $image_type, $image_type_id, $item['options'], $item['options'],$phone_type);
                }
               
     
                if ($item['options'] == $radio) {
                    $mod->setData('status', 1);
                } else {
                    $mod->setData('status', 0);
                }
                $mod->setData('image_delete', $item['delete'])->setId($item['id']);
                $mod->save();
            } else {
                if ($item['id'] != 0) {
                    if (($name_image != "") && isset($name_image) != NULL) {
                        $mod->setData('name', $name_image)->setId($item['id']);
                        $this->createImage($name_image, $storeId, $image_type, $image_type_id, $item['options'], $item['options'],$phone_type);
                    }
                     
                    if ($item['options'] == $radio) {
                        $mod->setData('status', 1);
                    } else {
                        $mod->setData('status', 0);
                    }
                    $mod->setData('image_delete', $item['delete'])->setId($item['id']);
                    $mod->save();
                }
            }
        }
        $this->deleteImageStore();
    }

    private function renameImage($image_name, $image_type_id, $options) {

        $name = "";
        if (isset($image_name) && ($image_name != null)) {
            $array_name = explode('.', $image_name);
            $array_name[0] = $image_type_id . '_' . $options;
            $name = $array_name[0] . '.' . end($array_name);
        }
        return $name;
    }

    public function createImage($image, $storeId, $image_type, $image_type_id, $fileid, $options, $phone_type) {
        try {
            /* Starting upload */
            
            $uploader = new Varien_File_Uploader("images_".$phone_type."_id" . $fileid);
            // Any extention would work
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);

            // We set media as the upload dir
            $path = $this->getImagePath($storeId, $image_type, $image_type_id,$options,$phone_type);
            
             $image_path = $path . DS . $image;
            // $image_path_cache = $this->getImagePathCache($store_id, $image_type, $image_type_id, $option) . DS . $image_name;
            try {
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            } catch (Exception $e) {
                
            }
            
            //echo $path;exit;
            $uploader->save($path, $image);
            //  echo "thanh tung";exit;
        } catch (Exception $e) {
            
        }
    }

    public function deleteImageStore() {
        $image_info = Mage::getModel('themeone/images')->getCollection()->addFieldToFilter('image_delete', 1);
        foreach ($image_info as $item) {
            $store_id = $item->getData('store_id');
            $image_type = $item->getData('image_type');
            $image_type_id = $item->getData('image_type_id');
            $option = $item->getData('options');
            $image_name = $item->getData('image_name');
            $phone_type=$item->getData('phone_type');

            $image_path = $this->getImagePath($store_id, $image_type, $image_type_id, $option,$phone_type) . DS . $image_name;
            // $image_path_cache = $this->getImagePathCache($store_id, $image_type, $image_type_id, $option) . DS . $image_name;
            try {
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
//                if (file_exists($image_path_cache)) {
//                    unlink($image_path_cache);
//                }
            } catch (Exception $e) {
                
            }
        }
    }

    public function getImagePath($store_id, $image_type, $image_type_id, $options, $phone_type) {
        $path = Mage::getBaseDir('media') . DS . 'themeone' . DS . 'images' . DS . $store_id . DS . $image_type . DS . $image_type_id . DS . $phone_type. DS . $options;
        return $path;
    }

    public function getImagePathForResponse($store_id, $image_type, $image_type_id, $options, $image_name, $phone_type) {
        $path = Mage::getBaseUrl('media') . 'themeone/images/' . $store_id . '/' . $image_type . '/' . $image_type_id . '/' .$phone_type.'/'. $options . '/' . $image_name;
        return $path;
    }

    public function getImagePathCache($store_id, $image_type, $image_type_id, $options) {
        $path = Mage::getBaseDir('media') . DS . 'themeone' . DS . 'images' . DS . 'cache' . DS . $store_id . DS . $image_type . DS . $image_type_id . DS . $options;
        return $path;
    }

    public function getImageUrlJS() {
        $url = Mage::getBaseUrl('media') . 'themeone/images/';
        return $url;
    }

}

?>