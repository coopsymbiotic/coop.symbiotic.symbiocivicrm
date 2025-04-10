<?php

namespace Civi\Api4\Action\Symbiocivicrm;

/**
 * Helper action for signups
 */
class Getconfig extends \Civi\Api4\Generic\AbstractAction {

  /**
   * Invoice ID
   *
   * @var string
   */
  protected $invoice_id = NULL;

  public function _run(\Civi\Api4\Generic\Result $result) {
    // @todo Lookup the customfield names instead of hardcoding.
    $contribution = \Civi\Api4\Contribution::get(false)
      ->addSelect('*', 'Spark.Language', 'Spark.Language:name', 'Spark.Spark_Status', 'Spark.Site_Name')
      ->addWhere('invoice_id', '=', $this->invoice_id)
      ->execute()
      ->first();

    if (empty($contribution)) {
      $contribution = \Civi\Api4\Contribution::get(false)
        ->addSelect('*', 'Spark.Language', 'Spark.Language:name', 'Spark.Spark_Status', 'Spark.Site_Name')
        ->addWhere('trxn_id', 'LIKE', '%' . $this->invoice_id . '%')
        ->execute()
        ->first();
    }

    if (empty($contribution)) {
      \Civi::log()->warning('Symbiocivicrm.getconfig: payment reference not found: ' . $this->invoice_id);
      throw new \Exception("Payment reference not found.");
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
        'sequential' => 1,
      ]);

      if (empty($result['values'])) {
        throw new Exception("Symbiocivicrm.Getconfig: Membership is On Behalf Of, but could not the Individual related to this organisation");
      }

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
      throw new Exception("Symbiocivicrm.Getconfig: Unexpected contact type: {$contact['contact_type']}");
    }

    // Site information
    $site_name_fieldid = \Civi::settings()->get('symbiocivicrm_site_name_fieldid');
    $site_locale_fieldid = \Civi::settings()->get('symbiocivicrm_aegir_server_fieldid');

    // Get the field api4 ID, group.name
    // Would not normally be necessary, but the custom fields were created pre-api4,
    // so we did not normalize the 'name' properties. Requires fixing in a db upgrade.
    $customField = \Civi\Api4\CustomField::get(false)
      ->addSelect('name', 'custom_group_id:name')
      ->addWhere('id', '=', $site_locale_fieldid)
      ->execute()
      ->first();

    $cfID = $customField['custom_group_id:name'] . '.' . $customField['name'];
    $t = $contribution[$cfID];
    $locale = \CRM_Symbiotic_Utils::getLocaleFromValue($t);

    $cfSiteName = \Civi\Api4\CustomField::get(false)
      ->addSelect('name', 'custom_group_id:name')
      ->addWhere('id', '=', $site_name_fieldid)
      ->execute()
      ->first();

    // @todo Unhardcode field name
    // $cfID = $customField['custom_group_id:name'] . '.' . $customField['name'];
    $site_name = $contribution['Spark.Site_Name'];

    $settings['site'] = [
      'name' => $site_name,
      'locale' => $locale,
    ];

    $result->exchangeArray($settings);
  }

  public static function permissions() {
    $permissions = parent::permissions();
    return $permissions;
  }

}
