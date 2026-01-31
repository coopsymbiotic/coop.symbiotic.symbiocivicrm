<?php

function civicrm_api3_job_symbiocivicrmdisableexpiredsites($params) {
  $responses = [];
  $hosting_mtypes = Civi::settings()->get('symbiocivicrm_membership_types');

  if (empty($hosting_mtypes)) {
    throw new Exception('symbiocivicrm_membership_types setting not set (Aegir settings)');
  }

  // Find CiviCRM Spark memberships that expired in the last 3 months
  // (sometimes there is backlog or snafu or whatnot)
  $endDate = new \DateTime();
  $endDate->modify('-3 months');

  $memberships = \Civi\Api4\Membership::get(FALSE)
    ->addWhere('membership_type_id', 'IN', $hosting_mtypes)
    ->addWhere('status_id:name', 'IN', ['Cancelled', 'Expired'])
    ->addWhere('end_date', '>', $endDate->format('Y-m-d'))
    ->addWhere('end_date', '<', date('Y-m-d'))
    ->addOrderBy('end_date', 'DESC')
    ->execute();

  foreach ($memberships as $membership) {
    // Find the first membership payment for this user
    $result = civicrm_api3('MembershipPayment', 'get', [
      'sequential' => 1,
      'options' => ['sort' => "contribution_id ASC", 'limit' => 1],
      'membership_id' => $membership['id'],
    ]);

    foreach ($result['values'] as $payment) {
      // Find the invoice_id for that membership, it is used to authenticate Aegir API requests
      $contribution = \Civi\Api4\Contribution::get(false)
        ->addSelect('id', 'trxn_id', 'invoice_id', 'Spark.Domain_name', 'Spark.Language')
        ->addWhere('id', '=', $payment['contribution_id'])
        ->addOrderBy('receive_date', 'ASC')
        ->setLimit(1)
        ->execute()
        ->first();

      $aegir_server = CRM_Symbiotic_Utils::getAegirServer($contribution['Spark.Language']);
      // trxn_id might have two IDs from Stripe
      $invoice_id = explode(',', $contribution['trxn_id'])[0];

      // @todo Hardcoded custom fields from CiviCRM Spark
      $site_url = $contribution['Spark.Domain_name'];

      if ($suffix = Civi::settings()->get('symbiocivicrm_domain_suffix')) {
        $site_url .= '.' . $suffix;
      }

      $try_invoice_ids = [
        // This is the first part of the trxn_id
        $invoice_id,
        // Sometimes it was this
        $contribution['invoice_id'],
        // And sometimes there were bugs and we sent both Stripe identifiers
        $contribution['trxn_id'],
      ];

      foreach ($try_invoice_ids as $try_id) {
        $response_code = _civicrm_api3_job_symbiocivicrmdisableexpiredsites_disable($aegir_server, $site_url, $try_id, $responses);

        if ($response_code == 200) {
          // Set the membership status to 'disabled'
          \Civi\Api4\Membership::update(false)
            ->addValue('status_id:name', 'Hosting Disabled')
            ->addValue('is_override', 1)
            ->addWhere('id', '=', $membership['id'])
            ->execute();
          break;
        }
      }
    }
  }

  $output = implode('<br>', $responses);
  return civicrm_api3_create_success($output);
}

function _civicrm_api3_job_symbiocivicrmdisableexpiredsites_disable($aegir_server, $url, $invoice_id, &$responses) {
  $client = new \GuzzleHttp\Client([
    // Mostly being lazy, avoid havings to try/catch, we do getStatusCode
    'http_errors' => false,
    'base_uri' => 'https://' . $aegir_server,
  ]);

  $response = $client->post('/hosting/api/site/disable', [
    'form_params' => [
      'url' => $url,
      'invoice' => $invoice_id,
    ],
  ]);

  $response_code = $response->getStatusCode();
  $responses[] = "$url : [$response_code; $aegir_server] " . $response->getBody()->getContents();

  return $response_code;
}
