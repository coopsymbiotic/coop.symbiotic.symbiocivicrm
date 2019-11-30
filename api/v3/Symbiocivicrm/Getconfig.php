<?php

/**
 * Adjust Metadata for Symbiocivicrm.Getconfig action.
 *
 * The metadata is used for setting defaults, documentation & validation.
 *
 * @param array $params
 *   Array of parameters determined by getfields.
 */
function civicrm_api3_symbiocivicrm_getconfig_spec($params) {

}

/**
 * Symbiocivicrm.Getconfig API.
 *
 * @param array $params
 *
 * @return array
 *   API result descriptor; return items are alert codes/messages
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_symbiocivicrm_getconfig($params) {

  $contribution = civicrm_api3('Contribution', 'Getsingle', [
    'invoice_id' => $params['invoice_id'],
  ]);

  $contact = civicrm_api3('Contact', 'Getsingle', [
    'contact_id' => $contribution['contact_id'],
  ]);

  $settings = [];
  $settings['organization'] = [
    'name' => $contact['display_name'],
    'street_address' => $contact['street_address'],
    'city' => $contact['city'],
    'state_province_id' => $contact['state_province_id'],
    'country_id' => $contact['country_id'],
    'phone' => $contact['phone'],
    'email' => $contact['email'],
  ];

  if ($contact['contact_type'] == 'Individual') {
    $settings['individual'] = [
      'email' => $contact['email'],
      'first_name' => $contact['first_name'],
      'last_name' => $contact['last_name'],
    ];
  }
  elseif ($contact['contact_type'] == 'Organization') {
    // If org contribution (on behalf of), fetch the first employee linked
    // we usually assume orgs have only one employee, since it's a new signup.
    // CiviCRM doesn't give us a better way to fetch this unfortunately (soft credit?).
    $contact_id_org = $contact['contact_id'];

    $result = civicrm_api3('Relationship', 'Get', [
      'relationship_type_id' => 4, // Employee of
      'is_active' => 1,
      'contact_id_b' => $contact_id_org,
      // 'api.Contact.get' => ['id' => "\$value.contact_id_a"], // not working?
    ]);

    $contact = civicrm_api3('Contact', 'Getsingle', [
      'id' => $result[0]['contact_id_a'],
    ]);

    $settings['individual'] = [
      'email' => $contact['email'],
      'first_name' => $contact['first_name'],
      'last_name' => $contact['last_name'],
    ];
  }
  else {
    throw new Exception('Unexpected contact type: ' . $contact['contact_type']);
  }

  // Site information
  $site_name_fieldid = Civi::settings()->get('symbiocivicrm_site_name_fieldid');
  $site_locale_fieldid = Civi::settings()->get('symbiocivicrm_aegir_server_fieldid');

  $settings['site'] = [
    'name' => $contribution['custom_' . $site_name_fieldid],
    'locale' => $contribution['custom_' . $site_locale_fieldid],
  ];

  return civicrm_api3_create_success($settings);
}
