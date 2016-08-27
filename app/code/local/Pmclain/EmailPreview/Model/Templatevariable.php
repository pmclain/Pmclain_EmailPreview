<?php

class Pmclain_EmailPreview_Model_Templatevariable extends Pmclain_EmailPreview_Model_Abstract {
  public $form;
  protected $fieldset;
  protected $variableElement;

  public function __construct() {
    parent::__construct();
  }

  public function getTemplateVariables() {
    $this->fieldset = $this->form->getElements()[0];
    $this->variableElement = $this->getVariableElement();
    $variables = $this->getVariables();
    return $variables;
  }

  protected function getVariableElement() {
    $fields = $this->fieldset->getElements();
    foreach ($fields as $field) {
      if ($field->getId() == 'orig_template_variables') {
        return $field;
      }
    }
    return null;
  }

  protected function getVariables() {
    $variables = $this->parseVariableJson();
    return $variables;
  }

  protected function parseVariableJson() {
    $json = $this->variableElement->getValue();
    $variableObject = json_decode($json);
    $variables = array('store');
    foreach ($variableObject as $var => $description) {
      if ($this->varIsObject($var)) {
        $variables[] = $this->cleanVarName($var);
      }
    }
    return array_unique($variables);
  }

  protected function varIsObject($var) {
    if (strrpos($var, '.') || stripos($var, '$')) {
      return true;
    }
    return false;
  }

  protected function cleanVarName($var) {
    if (preg_match('/\s(\w*)\./', $var, $matches)) {
      return $matches[1];
    }
    if (preg_match('/\$(\w*)/', $var, $matches)) {
      return $matches[1];
    }
    return null;
  }

  public function getFieldData($var) {
    $data = array();
    $data['name'] = $var;
    $data['label'] = $this->helper->__($var);
    $data['title'] = $this->helper->__($var);
    $data['disabled'] = false;
    $data['class'] = 'preview-variable-field';
    if ($this->getFieldType($var) == 'select') {
      $data['values'] = $this->getSelectValues($var);
    } else {
      $data['value'] = $this->getFieldValue($var);
    }
    return $data;
  }

  public function getFieldType($var) {
    switch ($var) {
      case 'store':
        return 'select';
    }
    return 'text';
  }

  protected function getFieldValue($var) {
    switch ($var) {
      case 'order':
        return Mage::getModel('sales/order')->getCollection()->getFirstItem()->getIncrementId();
      case 'invoice':
        return Mage::getModel('sales/order_invoice')->getCollection()->getFirstItem()->getIncrementId();
      case 'shipment':
        return Mage::getModel('sales/order_shipment')->getCollection()->getFirstItem()->getIncrementId();
      case 'creditmemo':
        return Mage::getModel('sales/order_creditmemo')->getCollection()->getFirstItem()->getIncrementId();
      case 'customer':
        return Mage::getModel('customer/customer')->getCollection()->getFirstItem()->getEmail();
    }
    return 'i am broken';
  }

  protected function getSelectValues($var) {
    //TODO: get different values based on var
    $values = Mage::getModel('pmclain_emailpreview/adminhtml_system_config_source_store')->toOptionArray();
    return $values;
  }
}