<?php

class Pmclain_EmailPreview_Model_Abstract
{
  protected $helper;

  public function __construct() {
    $this->helper = Mage::helper('pmclain_emailpreview');
  }
}