<?php

require_once 'app/Mage.php';
Mage::app();


$product = Mage::getModel('catalog/product')->load();

echo $product->getName()."  ".$product->getFeatured();

/** @var Mage_Customer_Model_Customer $customer_collection */
$customer_collection = mage::getModel('customer/customer')->getCollection();

$customer_idlist = [];

/** @var Mage_Customer_Model_Customer $customer */
foreach ($customer_collection as $customer) {
    /** @var Mage_Sales_Model_Entity_Sale_Collection $customerTotals */
    $customerTotals = Mage::getResourceModel('sales/sale_collection')
        ->setOrderStateFilter(Mage_Sales_Model_Order::STATE_CANCELED, true)
        ->setCustomerFilter($customer)
        ->load()
        ->getTotals();
    $customerLifetimeSales = $customerTotals->getLifetime();

    $customer_idlist [] = array('id' => $customer->getId(), 'spent' => $customerLifetimeSales);
}

usort($customer_idlist, "compare"); // sort by spent

$i = 0;
$csv = new Varien_File_Csv();
$csvdata = array();
$customer_info = array();

$customer_info['ID'] =  "ID";
$customer_info['Name'] =  "Name";
$customer_info['Email'] = "Email";
$customer_info['Phone'] = "Phone";
$customer_info['Spent'] = "Spent";
$csvdata[] = $customer_info;

foreach ($customer_idlist as $item) {
    /** @var Mage_Customer_Model_Customer $one_customer */
    $one_customer = Mage::getModel('customer/customer')->load($item['id']);

    $customer_info['ID'] =  $one_customer ->getId();
    $customer_info['Name'] =  $one_customer ->getName();
    $customer_info['Email'] = $one_customer ->getEmail();
    $customer_info['Phone'] = $one_customer ->getTelephone();
    $customer_info['Spent'] = $item['spent'];

    $csvdata[] = $customer_info;

    $i++;
}
$csv->saveData('Customer_list_by_spent.csv', $csvdata);

echo "Load " . $i . " customers\n";

/* Function for sort */
function compare($v1, $v2)
{
    /* Compare value by key spent */
    if ($v1['spent'] == $v2['spent']) return 0;
    return ($v1['spent'] > $v2['spent']) ? -1 : 1;
}
