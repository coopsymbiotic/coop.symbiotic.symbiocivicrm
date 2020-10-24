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
  if (empty($params['invoice_id'])) {
    throw new Exception("Missing invoice_id");
  }

  $contribution = \Civi\Api4\Contribution::get(false)
    ->addSelect('*', 'Spark.Language', 'Spark.Language:name', 'Spark.Spark_Status')
    ->addWhere('trxn_id', 'LIKE', '%' . $params['invoice_id'] . '%')
    ->execute()
    ->first();

  if (empty($contribution)) {
    $contribution = \Civi\Api4\Contribution::get(false)
      ->addSelect('*', 'Spark.Language', 'Spark.Language:name', 'Spark.Spark_Status')
      ->addWhere('invoice_id', 'LIKE', '%' . $params['invoice_id'] . '%')
      ->execute()
      ->first();
  }

  if (empty($contribution)) {
    Civi::log()->warning('Symbiocivicrm.getconfig: payment reference not found: ' . $params['invoice_id']);
    thrown new Exception("Payment reference not found.");
  }

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
      'sequential' => 1,
    ]);

    $contact = civicrm_api3('Contact', 'Getsingle', [
      'id' => $result['values'][0]['contact_id_a'],
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

  // Get the field api4 ID, group.name
  // Would not normally be necessary, but the custom fields were created pre-api4,
  // so we did not normalize the 'name' properties. Requires fixing in a db upgrade.
  $customField = \Civi\Api4\CustomField::get(false)
    ->addSelect('name', 'custom_group_id:name')
    ->addWhere('id', '=', $site_locale_fieldid)
    ->execute()
    ->first();

  $cfID = $customField['name'] . '.' . $customField['custom_group_id:name'];
  $t = $contribution[$cfID];
  $locale = CRM_Symbiotic_Utils::getLocaleFromValue($t);

  $cfSiteName = \Civi\Api4\CustomField::get(false)
    ->addSelect('name', 'custom_group_id:name')
    ->addWhere('id', '=', $site_name_fieldid)
    ->execute()
    ->first();

  $cfID = $customField['name'] . '.' . $customField['custom_group_id:name'];
  $site_name = $contribution[$cfID];

  $settings['site'] = [
    'name' => $site_name,
    'locale' => $locale,
  ];

  return civicrm_api3_create_success($settings);
}
