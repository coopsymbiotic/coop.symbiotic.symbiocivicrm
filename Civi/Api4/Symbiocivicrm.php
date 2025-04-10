<?php

namespace Civi\Api4;

class Symbiocivicrm extends Generic\AbstractEntity {

  public static function permissions() {
    $permissions = parent::permissions();
    // Not really sure about this
    $permissions['default'] = ['access CiviCRM'];
    return $permissions;
  }

  /**
   * @param bool $checkPermissions
   * @return Generic\BasicGetFieldsAction
   */
  public static function getFields($checkPermissions = TRUE) {
    // Copied from Civi/Api4/System.php
    return (new Generic\BasicGetFieldsAction(__CLASS__, __FUNCTION__, function() {
      return [];
    }))->setCheckPermissions($checkPermissions);
  }

}
