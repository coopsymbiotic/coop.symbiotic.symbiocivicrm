<?php

namespace Civi\Api4\Action\Symbiocivicrm;

/**
 * Helper action for signups
 *
 * Checks whether the invoice_id is related to an active membership.
 */
class Getstatus extends \Civi\Api4\Generic\AbstractAction {

  /**
   * Invoice ID
   *
   * @var string
   */
  protected $invoice_id = NULL;

  public function _run(\Civi\Api4\Generic\Result $result) {
    // Copied from Getconfig
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
      \Civi::log()->warning('Symbiocivicrm.getstatus: payment reference not found: ' . $this->invoice_id);
      throw new \Exception("Payment reference not found.");
    }

    // This assumes that anyone with a valid membership therefore has a valid hosting membership
    // (and not some other type of membership)
    // Ideally we should have a setting for the member org, or valid member types.
    $result = civicrm_api3('Membership', 'get', [
      'sequential' => 1,
      'active_only' => 1,
      'contact_id' => $contribution['contact_id'],
    ]);

    $status = [];

    if (!empty($result['values'])) {
      $status['membership'] = $result['values'][0];
    }

    // Fetch the latest membership, so that we can find an expiry date
    $result = civicrm_api3('Membership', 'get', [
      'sequential' => 1,
      'contact_id' => $contribution['contact_id'],
      'options' => ['sort' => "end_date DESC"],
    ]);

    $status = [];

    if (!empty($result['values'])) {
      $status['membership'] = $result['values'][0];
      $status['membership']['help_expiration'] = html_entity_decode(\Civi::settings()->get('symbiocivicrm_expired_help'));
      $status['membership']['help_cancellation'] = html_entity_decode(\Civi::settings()->get('symbiocivicrm_cancellation_help'));
    }

    $result->exchangeArray($status);
  }

  public static function permissions() {
    $permissions = parent::permissions();
    return $permissions;
  }

}
