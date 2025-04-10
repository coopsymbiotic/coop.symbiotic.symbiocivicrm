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

    // @todo Use the site language and send translated template, if available
    // @todo Hardcoded template ID
    $msg_template_id = 260;

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
      'bcc' => 'mathieu@civicrm.org,mathieu@bidon.ca,josh@civicrm.org', // @todo setting
    ];

    list($sent, $subject, $text, $html) = \CRM_Core_BAO_MessageTemplate::sendTemplate($sendTemplateParams);
    \Civi::log()->info('Symbiocrm/Welcome: sending email to ' . $contact['email'] . ', link=' . $this->loginurl . ', result = ' . $sent);

    // Create an Email Activity
    civicrm_api3('Activity', 'create', [
      'activity_type_id' => 3, // Email
      'source_contact_id' => $contact_id,
      'subject' => $subject,
      'details' => $html,
    ]);

    $result->exchangeArray(['sent' => $sent]);
  }

  public static function permissions() {
    $permissions = parent::permissions();
    return $permissions;
  }

}
