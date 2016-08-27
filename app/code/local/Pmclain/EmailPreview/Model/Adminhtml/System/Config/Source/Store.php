<?php

class Pmclain_EmailPreview_Model_Adminhtml_System_Config_Source_Store {
  public function toOptionArray() {
    $output = array();
    $stores = Mage::app()->getStores();
    foreach ($stores as $store) {
      $websiteName = $store->getWebsite()->getName();
      $storeName = $store->getGroup()->getName();
      $viewName = $store->getName();
      $output[] = array(
        'value' => $store->getId(),
        'label' => $websiteName . ' / ' . $storeName . ' / ' . $viewName
      );
    }

    return $output;
  }
}