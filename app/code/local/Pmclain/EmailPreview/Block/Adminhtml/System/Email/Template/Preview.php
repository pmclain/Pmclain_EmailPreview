<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml system template preview block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Pmclain_EmailPreview_Block_Adminhtml_System_Email_Template_Preview extends Mage_Adminhtml_Block_System_Email_Template_Preview {
  /**
   * Prepare html output
   *
   * @return string
   */
  protected function _toHtml() {
    $defaultStoreId = $this->getTemplateStoreId();

    $appEmulation = Mage::getSingleton('core/app_emulation');
    $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($defaultStoreId);

    /** @var $template Mage_Core_Model_Email_Template */
    $template = Mage::getModel('core/email_template');
    $id = (int) $this->getRequest()->getParam('id');
    if ($id) {
      $template->load($id);
    }
    else {
      $template->setTemplateType($this->getRequest()->getParam('type'));
      $template->setTemplateText($this->getRequest()->getParam('text'));
      $template->setTemplateStyles($this->getRequest()->getParam('styles'));
    }

    /* @var $filter Mage_Core_Model_Input_Filter_MaliciousCode */
    $filter = Mage::getSingleton('core/input_filter_maliciousCode');

    $template->setTemplateText(
      $filter->filter($template->getTemplateText())
    );

    Varien_Profiler::start("email_template_proccessing");
    $vars = $this->getVars();

    $templateProcessed = $template->getProcessedTemplate($vars, TRUE);

    if ($template->isPlain()) {
      $templateProcessed = "<pre>" . htmlspecialchars($templateProcessed) . "</pre>";
    }

    Varien_Profiler::stop("email_template_proccessing");

    // Stop store emulation process
    $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

    return $templateProcessed;
  }

  protected function getVars() {
    $params = $this->unsetCoreParams();
    $vars = array();
    foreach ($params as $key => $value) {
      $vars[$key] = $this->getVarObject($key, $value);
    }
    return $vars;
  }

  protected function unsetCoreParams() {
    $params = $this->getRequest()->getParams();
    $coreParams = array('key', 'form_key', 'type', 'text', 'styles', 'store');
    foreach ($coreParams as $coreParam) {
      unset($params[$coreParam]);
    }
    return $params;
  }

  protected function getVarObject($type, $value) {
    switch ($type) {
      case 'customer':
        return $this->getCustomer($value);
      case 'order':
        return Mage::getModel('sales/order')->loadByIncrementId($value);
      case 'invoice':
        return Mage::getModel('sales/order_invoice')->loadByIncrementId($value);
      case 'shipment':
        return Mage::getModel('sales/order_shipment')->loadByIncrementId($value);
      case 'creditmemo':
        return Mage::getModel('pmclain_emailpreview/sales_order_creditmemo')->loadByIncrementId($value);
    }
    return null;
  }

  protected function getCustomer($email) {
    $customer = Mage::getModel('customer/customer');
    $customer->setWebsiteId(Mage::app()->getStore($this->getTemplateStoreId())->getWebsiteId());
    $customer->loadByEmail($email);
    return $customer;
  }

  protected function getTemplateStoreId() {
    if ($this->getRequest()->getParam('store') != '') {
      return $this->getRequest()->getParam('store');
    }
    return Mage::app()->getDefaultStoreView()->getId();
  }
}
