<?php

function civicrm_api3_job_symbiocivicrmdisableexpiredsites($params) {
  $responses = [];
  $hosting_mtypes = Civi::settings()->get('symbiocivicrm_membership_types');

  if (empty($hosting_mtypes)) {
    throw new Exception('symbiocivicrm_membership_types setting not set (Aegir settings)');
  }

  // Find CiviCRM Spark memberships that have expired recently
  $memberships = \Civi\Api4\Membership::get(FALSE)
    ->addWhere('membership_type_id', 'IN', $hosting_mtypes)
    ->addWhere('status_id:name', '=', 'Expired')
    ->addOrderBy('end_date', 'DESC')
    ->setLimit(40) // @todo for now
    ->execute();

  foreach ($memberships as $membership) {
    // Find the first membership payment for this user
    $result = civicrm_api3('MembershipPayment', 'get', [
      'sequential' => 1,
      'options' => ['sort' => "contribution_id ASC", 'limit' => 1],
      'membership_id' => $membership['id'],
    ]);

    foreach ($result['values'] as $payment) {
      // Find the invoice_id for that membership
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

      // Now double-check if there hasn't been a more recent payment
      $latest = \Civi\Api4\Contribution::get(false)
        ->addSelect('id', 'receive_date')
        ->addWhere('Spark.Domain_name', '=', $contribution['Spark.Domain_name'])
        ->addOrderBy('receive_date', 'DESC')
        ->setLimit(1)
        ->execute()
        ->first();

      $d1 = new DateTime($latest['receive_date']);
      $d2 = new DateTime();
      $interval = $d1->diff($d2);

      if ($interval->days < 40) {
        // Hosting is being paid by another membership
        // Find the membership associated to $latest
        $result = civicrm_api3('MembershipPayment', 'get', [
          'sequential' => 1,
          'options' => ['sort' => "contribution_id ASC", 'limit' => 1],
          'contribution_id' => $latest['id'],
        ]);

        // Cancel this old membership
        \Civi\Api4\Membership::update(false)
          ->addValue('status_id:name', 'Cancelled')
          ->addValue('is_override', 1)
          ->addWhere('id', '=', $membership['id'])
          ->execute();

        $responses[] = $site_url . ': membership ID ' . $membership['id'] . ' is now being paid by membership ID: ' . $result['values'][0]['membership_id'] . ' - membership cancelled';
        continue;
      }

      $client = new \GuzzleHttp\Client([
        // Mostly being lazy, avoid havings to try/catch, we do getStatusCode
        'http_errors' => false,
        'base_uri' => 'https://' . $aegir_server,
      ]);

      $response = $client->post('/hosting/api/site/disable', [
        'form_params' => [
          'url' => $site_url,
          'invoice' => $invoice_id,
        ],
      ]);

      $response_code = $response->getStatusCode();
      $responses[] = $site_url . ': [' . $response_code . '; ' . $aegir_server . '] ' . $response->getBody()->getContents();

      if ($response_code == 200) {
        // Set the membership status to 'disabled'
        \Civi\Api4\Membership::update(false)
          ->addValue('status_id:name', 'Hosting Disabled')
          ->addValue('is_override', 1)
          ->addWhere('id', '=', $membership['id'])
          ->execute();
      }
      else {
        // Try the real invoice_id
        $response = $client->post('/hosting/api/site/disable', [
          'form_params' => [
            'url' => $site_url,
            'invoice' => $contribution['invoice_id'],
          ],
        ]);

        $response_code = $response->getStatusCode();
        $responses[] = $site_url . ': [' . $response_code . '; ' . $aegir_server . '] ' . $response->getBody()->getContents();

        if ($response_code == 200) {
          // Set the membership status to 'disabled'
          \Civi\Api4\Membership::update(false)
            ->addValue('status_id:name', 'Hosting Disabled')
            ->addValue('is_override', 1)
            ->addWhere('id', '=', $membership['id'])
            ->execute();
        }
      }
    }
  }

  $output = implode('<br>', $responses);
  return civicrm_api3_create_success($output);
}
