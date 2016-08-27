<?php

class Pmclain_EmailPreview_Model_Observer extends Pmclain_EmailPreview_Model_Abstract
{
  public function __construct() {
    parent::__construct();
  }

  public function appendCustomColumn(Varien_Event_Observer $observer) {
    $event = $observer->getEvent();
    $blockType = $event->getBlock()->getType();
    if($blockType == 'adminhtml/system_email_template_edit_form') {
      $form = $event->getBlock()->getForm();
      $templateVariable = Mage::getModel('pmclain_emailpreview/templatevariable');
      $templateVariable->form = $form;
      $templateVariables = $templateVariable->getTemplateVariables();

      $fieldset = $form->addFieldset(
        'preview_variables',
        array(
          'legend' => 'Preview Variables',
          'class' => 'preview-variables'
        )
      );
      foreach ($templateVariables as $var) {
        $fieldData = $templateVariable->getFieldData($var);
        $fieldset->addField(
          $var,
          $templateVariable->getFieldType($var),
          $fieldData
        );
      }
    }
  }
}