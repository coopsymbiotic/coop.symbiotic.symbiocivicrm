<?php

/**
 * Adjust Metadata for Createsite action.
 *
 * The metadata is used for setting defaults, documentation & validation.
 *
 * @param array $params
 *   Array of parameters determined by getfields.
 */
function civicrm_api3_symbiocivicrm_createsite_spec($params) {
  $params['server'] = [
    'title' => 'Server',
    'description' => 'Aegir server hostname',
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
 * Symbiocivicrm.Createsite API.
 *
 * @param array $params
 *
 * @return array
 *   API result descriptor; return items are alert codes/messages
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_symbiocivicrm_createsite($params) {
  $base_uri = 'https://' . $params['server'];
  $contribution_id = $params['contribution_id'];

  $contribution = civicrm_api3('Contribution', 'getsingle', [
    'id' => $contribution_id,
  ]);

  $email = civicrm_api3('Email', 'getsingle', [
    'contact_id' => $contribution['contact_id'],
    'is_primary' => 1,
  ]);

  Civi::log()->info('Symbiocivicrm.createsite', [
    'email' => $email,
    'contribution' => $contribution,
  ]);

  $client = new \GuzzleHttp\Client([
    'base_uri' => $base_uri,
  ]);

  $field_id = Civi::settings()->get('symbiocivicrm_domain_name_fieldid');
  $site_url = $contribution['custom_' . $field_id];

  if ($suffix = Civi::settings()->get('symbiocivicrm_domain_suffix')) {
    $site_url .= '.' . $suffix;
  }

  // In REST terms, this should be a PUT, but hosting_restapi expects a POST.
  $response = $client->post('/hosting/api/site', [
    'form_params' => [
      'url' => $site_url,
      'invoice' => $contribution['invoice_id'],
      'email' => $email['email'],
      'crmhost' => CIVICRM_UF_BASEURL,
    ],
  ]);

  return civicrm_api3_create_success([
    'code' => $response->getStatusCode(),
    'body' => $response->getBody()->getContents(),
  ]);
}
