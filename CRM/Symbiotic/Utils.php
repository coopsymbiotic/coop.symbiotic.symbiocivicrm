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

}
