<?php

namespace Civi\Api4\Action\Symbiocivicrm;

/**
 * Helper action for signups
 */
class Welcome extends \Civi\Api4\Generic\AbstractAction {

  /**
   * Login URL
   *
   * @var string
   */
  protected $loginurl = NULL;

  /**
   * Invoice ID
   *
   * @var string
   */
  protected $invoice_id = NULL;

  public function _run(\Civi\Api4\Generic\Result $result) {
    $contribution = null;

    // Copied from Getconfig
    $contribution = \Civi\Api4\Contribution::get(false)
      ->addSelect('contact_id')
      ->addWhere('invoice_id', '=', $this->invoice_id)
      ->execute()
      ->first();

    if (empty($contribution)) {
      $contribution = \Civi\Api4\Contribution::get(false)
        ->addSelect('contact_id')
        ->addWhere('trxn_id', 'LIKE', '%' . $this->invoice_id . '%')
        ->execute()
        ->first();
    }

    $contact_id = $contribution['contact_id'];

    if (empty($contact_id)) {
      throw new \Exception("Contact not found.");
    }

    \Civi::log()->info('Symbiocrm/Welcome: found contact_id: ' . $contact_id);

    $contact = civicrm_api3('Contact', 'getsingle', [
      'id' => $contact_id,
    ]);

    // If the contribution is linked to an Organization, cc all employees
    // This works for Spark because it's mostly new orgs, but we should fix to check
    // for a soft-credit.
    $cc = [];
    $target_contact_ids = [];
    $target_contact_ids[] = $contact_id;

    if ($contact['contact_type'] == 'Organization') {
      $relationships = \Civi\Api4\Relationship::get(FALSE)
        ->addSelect('contact_id_a', 'contact_id_a.email_primary.email')
        ->addWhere('relationship_type_id:name', '=', 'Employee of')
        ->addWhere('contact_id_b', '=', $contact_id)
        ->execute();
      foreach ($relationships as $rel) {
        $cc[] = $rel['contact_id_a.email_primary.email'];
        $target_contact_ids[] = $rel['contact_id_a'];
      }
    }

    // @todo Use the site language and send translated template, if available
    $msg_template_id = \Civi::settings()->get('symbiocivicrm_welcome_template_id');

    $tplParams = [
      'civicrm_site_login_url' => $this->loginurl,
    ];

    $sendTemplateParams = [
      'messageTemplateID' => $msg_template_id,
      'tplParams' => $tplParams,
      'isTest' => 0,
      'from' => "CiviCRM Spark <spark@civicrm.org>", // @todo setting? "$domainValues[0] <$domainValues[1]>",
      'toEmail' => $contact['email'],
      'toName' => $contact['display_name'],
      'cc' => implode(',', $cc),
      'bcc' => 'mathieu@civicrm.org,josh@civicrm.org', // @todo setting
    ];

    list($sent, $subject, $text, $html) = \CRM_Core_BAO_MessageTemplate::sendTemplate($sendTemplateParams);
    \Civi::log()->info('Symbiocrm/Welcome: sending email to ' . $contact['email'] . ', link=' . $this->loginurl . ', result = ' . $sent);

    // Create an Email Activity
    $source_contact_id = \CRM_Core_BAO_Domain::getDomain()->contact_id;
    \Civi\Api4\Activity::create(FALSE)
      ->addValue('activity_type_id', 3)
      ->addValue('target_contact_id', $target_contact_ids)
      ->addValue('source_contact_id', $source_contact_id)
      ->addValue('subject', $subject ?? 'Spark Welcome')
      ->addValue('details', $html ?? 'Spark Welcome')
      ->execute();

    $result->exchangeArray(['sent' => $sent]);
  }

  public static function permissions() {
    $permissions = parent::permissions();
    return $permissions;
  }

}
