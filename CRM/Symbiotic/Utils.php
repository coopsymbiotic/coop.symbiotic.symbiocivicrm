<?php

class CRM_Symbiotic_Utils {

  /**
   * Returns a keyed array of contribution text custom fields.
   * Helper function for the settings.
   */
  public static function getContributionCustomFields() {
    $fields = [];
    $fields[''] = ts('- Select -');

    $result = civicrm_api3('Contribution', 'getfields');

    foreach ($result['values'] as $key => $val) {
      if (CRM_Utils_Array::value('data_type', $val) == 'String') {
        $fields[$val['id']] = $val['title'];
      }
    }

    return $fields;
  }

}
