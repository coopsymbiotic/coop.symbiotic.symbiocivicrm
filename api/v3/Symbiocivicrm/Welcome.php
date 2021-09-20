<?php

/**
 * Adjust Metadata for Welcome action.
 *
 * The metadata is used for setting defaults, documentation & validation.
 *
 * @param array $params
 *   Array of parameters determined by getfields.
 */
function civicrm_api3_symbiocivicrm_welcome_spec($params) {
  $params['loginurl'] = [
    'title' => 'Login URL',
    'description' => 'CiviCRM Login URL',
    'type' => CRM_Utils_Type::T_STRING,
    'required' => TRUE,
  ];
  $params['contribution_id'] = [
    'title' => 'Contribution ID',
    'description' => 'Contribution ID',
    'type' => CRM_Utils_Type::T_INT,
    'required' => TRUE,
  ];
}

/**
 * Symbiocivicrm.Welcome API.
 *
 * @param array $params
 *
 * @return array
 *   API result descriptor; return items are alert codes/messages
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_symbiocivicrm_welcome($params) {
  $contribution_id = $params['contribution_id'];

  $contribution = civicrm_api3('Contribution', 'getsingle', [
    'id' => $contribution_id,
  ]);

  $contact_id = $contribution['contact_id'];

  $formattedContactDetails = [];

  $email = NULL;

  if (empty($contact_id)) {
    throw new Exception("Contact not found.");
  }

  $contact = civicrm_api3('Contact', 'getsingle', [
    'id' => $contact_id,
  ]);

  // @todo Use the site language and send translated template, if available
  // @todo Hardcoded template ID
  $msg_template_id = 260;

  $formattedContactDetails[] = $contact;

  $template = civicrm_api3('MessageTemplate', 'getsingle', [
    'id' => $msg_template_id,
  ]);

  $subject = $template['msg_subject'];
  $html_message = $template['msg_html'];
  $text_message = $template['msg_text'];

  // Replace the token
  $loginurl_html = '<a href="' . $params['loginurl'] . '">' . $params['loginurl'] . '</a>';
  $html_message = preg_replace('/LOGINURL/', $loginurl_html, $html_message);
  $text_message = preg_replace('/LOGINURL/', $params['loginurl'], $text_message);

  list($sent, $activityId) = CRM_Activity_BAO_Activity::sendEmail(
    $formattedContactDetails,
    $subject,
    $text_message,
    $html_message,
    NULL,
    1 // cid=1, system user
  );

  return civicrm_api3_create_success([
    'code' => $response->getStatusCode(),
    'body' => $response->getBody()->getContents(),
  ]);
}
