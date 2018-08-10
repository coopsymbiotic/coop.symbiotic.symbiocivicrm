<?php

use CRM_Symbiocivicrm_ExtensionUtil as E;

/**
 * Settings metadata file
 */
return [
  'symbiocivicrm_aegir_signup_page' => [
    'group_name' => 'domain',
    'group' => 'symbiocivicrm',
    'name' => 'symbiocivicrm_aegir_signup_page',
    'type' => 'Array',
    'default' => NULL,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => E::ts('Aegir Signup Contribution Pages'),
    'description' => E::ts('CiviCRM Contribution Pages that are for Aegir signups.'),
    'help_text' => '',
    'quick_form_type' => 'Select',
    'html_type' => 'Select',
    'html_attributes' => array(
      'multiple' => 1,
      'class' => 'crm-select2',
    ),
    'pseudoconstant' => array(
      'api_entity' => 'ContributionPage',
      'api_field' => 'title',
    ),
  ],
  'symbiocivicrm_force_recur' => [
    'group_name' => 'domain',
    'group' => 'symbiocivicrm',
    'name' => 'symbiocivicrm_force_recur',
    'type' => 'Array',
    'default' => NULL,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => E::ts('Force recurring contribution'),
    'description' => E::ts('Force the recurring option on these CiviCRM Contribution Pages. Usually not required for membership pages, since a membership can be forced to be required.'),
    'help_text' => '',
    'quick_form_type' => 'Select',
    'html_type' => 'Select',
    'html_attributes' => array(
      'multiple' => 1,
      'class' => 'crm-select2',
    ),
    'pseudoconstant' => array(
      'api_entity' => 'ContributionPage',
      'api_field' => 'title',
    ),
  ],
  'symbiocivicrm_contribute_submit_button' => [
    'group_name' => 'domain',
    'group' => 'symbiocivicrm',
    'name' => 'symbiocivicrm_contribute_submit_button',
    'type' => 'Array',
    'default' => NULL,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => E::ts('Submit on Contribution Pages'),
    'description' => E::ts('Rename the "contribute" button to "submit".'),
    'help_text' => '',
    'quick_form_type' => 'Select',
    'html_type' => 'Select',
    'html_attributes' => array(
      'multiple' => 1,
      'class' => 'crm-select2',
    ),
    'pseudoconstant' => array(
      'api_entity' => 'ContributionPage',
      'api_field' => 'title',
    ),
  ],
];
