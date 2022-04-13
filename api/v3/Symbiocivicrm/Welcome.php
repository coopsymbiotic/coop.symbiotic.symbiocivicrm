<?php

/**
 * Adjust Metadata for Welcome action.
 *
 * The metadata is used for setting defaults, documentation & validation.
 *
 * @param array $params
 *   Array of parameters determined by getfields.
 */
function civicrm_api3_symbiocivicrm_welcome_spec($params) {
  $params['loginurl'] = [
    'title' => 'Login URL',
    'description' => 'CiviCRM Login URL',
    'type' => CRM_Utils_Type::T_STRING,
    'required' => TRUE,
  ];
  $params['contribution_id'] = [
    'title' => 'Contribution ID',
    'description' => 'Contribution ID',
    'type' => CRM_Utils_Type::T_INT,
    'required' => TRUE,
  ];
}

/**
 * Symbiocivicrm.Welcome API.
 *
 * @param array $params
 *
 * @return array
 *   API result descriptor; return items are alert codes/messages
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_symbiocivicrm_welcome($params) {
  $contribution_id = $params['contribution_id'];

  $contribution = civicrm_api3('Contribution', 'getsingle', [
    'id' => $contribution_id,
  ]);

  $contact_id = $contribution['contact_id'];

  if (empty($contact_id)) {
    throw new Exception("Contact not found.");
  }

  $contact = civicrm_api3('Contact', 'getsingle', [
    'id' => $contact_id,
  ]);

  // @todo Use the site language and send translated template, if available
  // @todo Hardcoded template ID
  $msg_template_id = 260;

  $tplParams = [
    'civicrm_site_login_url' => $params['loginurl'],
  ];

  $sendTemplateParams = [
    'messageTemplateID' => $msg_template_id,
    'tplParams' => $tplParams,
    'isTest' => 0,
    'from' => "CiviCRM Spark <spark@civicrm.org>", // @todo setting? "$domainValues[0] <$domainValues[1]>",
    'toEmail' => $contact['email'],
    'toName' => $contact['display_name'],
    'bcc' => 'mathieu@bidon.ca', // @todo setting?
  ];

  list($sent) = CRM_Core_BAO_MessageTemplate::sendTemplate($sendTemplateParams);

  return civicrm_api3_create_success([
    'sent' => $sent,
  ]);
}
