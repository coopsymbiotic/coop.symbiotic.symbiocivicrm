<?php

class CRM_Symbiotic_Utils {

  /**
   * Returns a keyed array of contribution custom fields.
   * Helper function for the settings.
   */
  public static function getContributionCustomFields() {
    $fields = [];
    $fields[''] = ts('- Select -');

    $result = civicrm_api3('Contribution', 'getfields');

    foreach ($result['values'] as $key => $val) {
      if (!empty($val['id']) && empty($val['is_core_field'])) {
        $fields[$val['id']] = $val['title'];
      }
    }

    return $fields;
  }

  /**
   *
   */
  public static function getAegirServer($value) {
    $server = NULL;

    $og = civicrm_api3('CustomField', 'get', [
      'id' => '271',
      'return' => 'option_group_id',
      'sequential' => 1,
    ])['values'][0]['option_group_id'];

    $server = civicrm_api3('OptionValue', 'get', [
      'option_group_id' => $og,
      'value' => $value,
      'sequential' => 1,
    ])['values'][0]['description'];

    $parts = explode(';', $server);
    $server = $parts[0];

    return $server;
  }

  /**
   * Create the DNS entry
   */
  public static function createDnsHost($domain, $server) {
    // Strip the domain suffixes so that foo.civicrm.org becomes just 'foo'.
    $domain = preg_replace('/^([-a-zA-Z0-9]+).*$/', '${1}', $domain);
    $server = preg_replace('/^([-a-zA-Z0-9]+).*$/', '${1}', $server);

    $output = exec("sudo /root/bin/gandi-new-entry.sh $domain $server");

    Civi::log()->info("createDnsHost: [$domain -> $server] $output");
  }

}
