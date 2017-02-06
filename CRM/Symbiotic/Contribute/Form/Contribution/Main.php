<?php

class CRM_Symbiotic_Contribute_Form_Contribution_Main {
  function buildForm(&$form) {
    $defaults = [];

    // JS to validate the domain name
    CRM_Core_Resources::singleton()->addScriptFile('coop.symbiotic.symbiocivicrm', 'js/contribute-form-contribution-main.js');
    CRM_Core_Resources::singleton()->addStyleFile('coop.symbiotic.symbiocivicrm', 'css/contribute-form-contribution-main.css');

    // Renommer le bouton 'Contribute'
    $buttons = $form->getElement('buttons');
    $buttons->_elements[0]->_attributes['value'] = ts('Submit');

    // If an amount was passed in the URL, set it as default
    if ($form->get('id') == 3 && $amount = CRM_Utils_Array::value('amt', $_REQUEST)) {
      $defaults['price_7'] = $amount;
    }

    if (!empty($defaults)) {
      $form->setDefaults($defaults);
    }
  }
}
