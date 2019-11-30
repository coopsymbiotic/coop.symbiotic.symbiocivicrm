<?php

class CRM_Symbiotic_Contribute_Form_Contribution_ThankYou {
  /**
   * Implements hook_civicrm_buildForm().
   */
  function buildForm(&$form) {
    // Only enable on Spark forms.
    $aegir_pages = Civi::settings()->get('symbiocivicrm_aegir_signup_page');
    if (!in_array($form->_id, $aegir_pages)) {
      return;
    }

    // Add a JS settings variable to make sure we have all the data we need
    // to do an API call using Ajax/POST.
    $smarty = CRM_Core_Smarty::singleton();
    $trxn_id = $smarty->get_template_vars('trxn_id');
    $email = $smarty->get_template_vars('email');
    $field_id = Civi::settings()->get('symbiocivicrm_domain_name_fieldid');
    $url = $form->_params['custom_' . $field_id];

    $url = $form->_params['custom_4'];

    $url = strtolower($url);
    $url = preg_replace("/[àáâãäå]/", "a", $url);
    $url = preg_replace("/[èéêë]/", "e", $url);
    $url = preg_replace("/[ôöò]/", "o", $url);
    $url = preg_replace("/[ùû]/", "u", $url);
    $url = preg_replace("/[îì]/", "i", $url);

    $url = preg_replace("/[^a-zA-Z0-9]/", '', $url);

    if (empty($url)) {
      return;
    }

    if ($suffix = Civi::settings()->get('symbiocivicrm_domain_suffix')) {
      $url .= '.' . $suffix;
    }

    CRM_Core_Resources::singleton()->addSetting(array(
      'symbiocivicrm' => array(
        'trxn_id' => $trxn_id,
        'url' => $url,
        'email' => $email,
      )
    ));

    $smarty->assign('symbiocivicrm_url', 'https://' . $url);

    // HTML with our statusbox
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => 'CRM/Symbiotic/Contribute/Form/Contribution/ThankYou.statusbox.tpl',
    ));

    // Styles for our statusbox
    CRM_Core_Resources::singleton()->addStyleFile('coop.symbiotic.symbiocivicrm', 'css/contribute-form-contribution-thankyou.css');

    // JS to talk to the Aegir server and create the site
    CRM_Core_Resources::singleton()->addScriptFile('coop.symbiotic.symbiocivicrm', 'js/contribute-form-contribution-thankyou.js');
  }
}
