<?php

use CRM_Symbiocivicrm_ExtensionUtil as E;

class CRM_Symbiotic_Contribute_Form_Contribution_Main {
  function buildForm(&$form) {
    $defaults = [];

    // JS to validate the domain name
    $aegir_pages = Civi::settings()->get('symbiocivicrm_aegir_signup_page');

    if (in_array($form->get('id'), $aegir_pages)) {
      Civi::resources()
        ->addScriptFile('coop.symbiotic.symbiocivicrm', 'js/contribute-form-contribution-main.js')
        ->addStyleFile('coop.symbiotic.symbiocivicrm', 'css/contribute-form-contribution-main.css');
    }

    // If an amount was passed in the URL, set it as default
    // TODO: This is hardcoded to a SymbioTIC form and should not be necessary.
    if ($form->get('id') == 3 && $amount = CRM_Utils_Array::value('amt', $_REQUEST)) {
      $defaults['price_7'] = $amount;
    }

    // Rename the 'contribute' button to 'submit'
    $submit_pages = Civi::settings()->get('symbiocivicrm_contribute_submit_button');

    if (in_array($form->get('id'), $submit_pages)) {
      $buttons = $form->getElement('buttons');
      $buttons->_elements[0]->_attributes['value'] = E::ts('Submit');
    }

    // VPS monthly payment, force recurrence
    $recur_pages = Civi::settings()->get('symbiocivicrm_force_recur');

    if (in_array($form->get('id'), $recur_pages) && $form->elementExists('is_recur')) {
      $defaults['is_recur'] = 1;

      $e = $form->getElement('is_recur');
      $e->freeze();
    }

    if (!empty($defaults)) {
      $form->setDefaults($defaults);
    }
  }
}
