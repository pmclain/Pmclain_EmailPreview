<?php

class Pmclain_EmailPreview_Model_Observer
{
  private $_helper;

  public function appendCustomColumn(Varien_Event_Observer $observer) {
    $event = $observer->getEvent();
    $blockType = $event->getBlock()->getType();
    if($blockType == 'adminhtml/system_email_template_edit_form') {
      $this->_helper = Mage::helper('adminhtml');
      $form = $event->getBlock()->getForm();
      $templateVariables = $this->getTemplateVariables($form);

      $fieldset = $form->addFieldset(
        'preview_variables',
        array(
          'legend' => 'Preview Variables',
          'class' => 'preview-variables'
        )
      );
      foreach ($templateVariables as $templateVariable) {
        $fieldData = $this->getFieldData($templateVariable);
        $fieldset->addField(
          $templateVariable,
          $this->getFieldType($templateVariable),
          $fieldData
        );
      }
      $test = null;
    }
  }

  private function getTemplateVariables($form) {
    $fieldset = $form->getElements()[0];
    $variableElement = $this->getVariableElement($fieldset->getElements());
    $variables = $this->getVariables($variableElement);
    return $variables;
  }

  private function getVariableElement($fields) {
    foreach ($fields as $field) {
      if ($field->getId() == 'orig_template_variables') {
        return $field;
      }
    }
    return null;
  }

  private function getVariables($variableElement) {
    $variables = $this->parseVariableJson($variableElement->getValue());
    return $variables;
  }

  private function parseVariableJson($json) {
    $variableObject = json_decode($json);
    $variables = array('store');
    foreach ($variableObject as $var => $description) {
      if ($this->varIsObject($var)) {
        $variables[] = $this->cleanVarName($var);
      }
    }
    return array_unique($variables);
  }

  private function varIsObject($var) {
    if (strrpos($var, '.') || stripos($var, '$')) {
      return true;
    }
    return false;
  }

  private function cleanVarName($var) {
    if (preg_match('/\s(\w*)\./', $var, $matches)) {
      return $matches[1];
    }
    if (preg_match('/\$(\w*)/', $var, $matches)) {
      return $matches[1];
    }
    return null;
  }

  private function getFieldData($var) {
    $data = array();
    $data['name'] = $var;
    $data['label'] = $this->_helper->__($var);
    $data['title'] = $this->_helper->__($var);
    $data['disabled'] = false;
    $data['class'] = 'preview-variable-field';
    if ($this->getFieldType($var) == 'select') {
      $data['values'] = $this->getSelectValues($var);
    } else {
      $data['value'] = $this->getFieldValue($var);
    }
    return $data;
  }

  private function getFieldType($var) {
    switch ($var) {
      case 'store':
        return 'select';
    }
    return 'text';
  }

  private function getFieldValue($var) {
    switch ($var) {
      case 'order':
        return Mage::getModel('sales/order')->getCollection()->getFirstItem()->getIncrementId();
      case 'invoice':
        return Mage::getModel('sales/order_invoice')->getCollection()->getFirstItem()->getIncrementId();
      case 'shipment':
        return Mage::getModel('sales/order_shipment')->getCollection()->getFirstItem()->getIncrementId();
      case 'customer':
        return Mage::getModel('customer/customer')->getCollection()->getFirstItem()->getEmail();
    }
    return 'i am broken';
  }

  private function getSelectValues($var) {
    //TODO: get different values based on var
    $values = Mage::getModel('pmclain_emailpreview/adminhtml_system_config_source_store')->toOptionArray();
    return $values;
  }
}