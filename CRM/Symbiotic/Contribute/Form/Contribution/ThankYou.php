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
    $email = $smarty->get_template_vars('email');
    $field_id = Civi::settings()->get('symbiocivicrm_domain_name_fieldid');
    $url = $form->_params['custom_' . $field_id];

    // On which server to create the site
    $aegir_server_field_id = Civi::settings()->get('symbiocivicrm_aegir_server_fieldid');
    $server = CRM_Symbiotic_Utils::getAegirServer($form->_params['custom_' . $aegir_server_field_id]);

    $contact_id = $form->_contactID;

    if (!empty($form->_params['onbehalfof_id'])) {
      $contact_id = $form->_params['onbehalfof_id'];
    }

    // FIXME: Normally the form/tpl should have the invoice or trxn_id, needs to be fixed in core.
    // For now we assume there aren't too many transactions at the same time and just fetch
    // the latest succesful contribution on an aegir page.
    // NB: we intentionally do not check the contribution_status_id because a webhook IPN might
    // not have finished processing yet (Stripe).
    $trxn_id = CRM_Core_DAO::singleValueQuery('SELECT trxn_id FROM civicrm_contribution WHERE contribution_page_id IN (%1) ORDER BY receive_date DESC LIMIT 1', [
      1 => [implode(',', $aegir_pages), 'CommaSeparatedIntegers'],
    ]);

    // Also note: this is not checking is_test=0, but later Aegir will
    // use the REST API to Contribution.get, and that will assume is_test=0.
    // Therefore we can test part of the process, but not all of it (unless
    // we temporarily edit the Aegir hosting_restapi code).
    Civi::log()->debug('AEGIR found trxn_id: ' . $trxn_id);

    $url = strtolower($url);
    $url = preg_replace("/[àáâãäå]/", "a", $url);
    $url = preg_replace("/[èéêë]/", "e", $url);
    $url = preg_replace("/[ôöò]/", "o", $url);
    $url = preg_replace("/[ùû]/", "u", $url);
    $url = preg_replace("/[îì]/", "i", $url);

    $url = preg_replace("/[^a-zA-Z0-9]/", '', $url);

    if (empty($url)) {
      Civi::log()->info('AEGIR url was empty. Request cancelled.');
      return;
    }

    CRM_Symbiotic_Utils::createDnsHost($url, $server);

    if ($suffix = Civi::settings()->get('symbiocivicrm_domain_suffix')) {
      $url .= '.' . $suffix;
    }

    CRM_Core_Resources::singleton()->addSetting([
      'symbiocivicrm' => [
        'trxn_id' => $trxn_id,
        'url' => $url,
        'email' => $email,
        'server' => $server,
        // Ex: example.org, helps with our .org to .com migration
        'crmhost' => $_SERVER['SERVER_NAME'],
      ],
    ]);

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
