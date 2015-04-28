<?php

class CRM_Symbiotic_Contribute_Form_Contribution_Main {
  function buildForm(&$form) {
    // JS to validate the domain name
    CRM_Core_Resources::singleton()->addScriptFile('coop.symbiotic.symbiocivicrm', 'js/contribute-form-contribution-main.js');
  }
}
