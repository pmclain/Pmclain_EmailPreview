<?php

class Pmclain_EmailPreview_Model_Sales_Order_Creditmemo extends Mage_Sales_Model_Order_Creditmemo {
  public function loadByIncrementId($incrementId)
  {
    $ids = $this->getCollection()
      ->addAttributeToFilter('increment_id', $incrementId)
      ->getAllIds();

    if (!empty($ids)) {
      reset($ids);
      $this->load(current($ids));
    }
    return $this;
  }
}