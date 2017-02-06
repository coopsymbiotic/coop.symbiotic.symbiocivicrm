<?php

class CRM_Symbiotic_Contribute_Form_Contribution_Main {
  function buildForm(&$form) {
    $defaults = [];

    // JS to validate the domain name
    Civi::resources()
      ->addStyleFile('coop.symbiotic.symbiocivicrm', 'js/contribute-form-contribution-main.js')
      ->addStyleFile('coop.symbiotic.symbiocivicrm', 'css/contribute-form-contribution-main.css');

    // Rename the 'Contribute' button
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
