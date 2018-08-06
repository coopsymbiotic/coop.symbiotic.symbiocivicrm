<?php

use CRM_Symbiocivicrm_ExtensionUtil as E;

class CRM_Symbiotic_Contribute_Form_Contribution_Main {
  function buildForm(&$form) {
    $defaults = [];

    // JS to validate the domain name
    Civi::resources()
      ->addScriptFile('coop.symbiotic.symbiocivicrm', 'js/contribute-form-contribution-main.js')
      ->addStyleFile('coop.symbiotic.symbiocivicrm', 'css/contribute-form-contribution-main.css');

    // Rename the 'Contribute' button
    $buttons = $form->getElement('buttons');
    $buttons->_elements[0]->_attributes['value'] = E::ts('Submit');

    // If an amount was passed in the URL, set it as default
    if ($form->get('id') == 3 && $amount = CRM_Utils_Array::value('amt', $_REQUEST)) {
      $defaults['price_7'] = $amount;
    }

    // VPS monthly payment, force recurrence
    if ($form->get('id') == 4 && $form->elementExists('is_recur')) {
      $defaults['is_recur'] = 1;

      $e = $form->getElement('is_recur');
      $e->freeze();
    }

    if (!empty($defaults)) {
      $form->setDefaults($defaults);
    }
  }
}
