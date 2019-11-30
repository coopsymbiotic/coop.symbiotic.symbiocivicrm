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
    'html_type' => 'select',
    'html_attributes' => [
      'multiple' => 1,
      'class' => 'crm-select2',
    ],
    'pseudoconstant' => [
      'callback' => 'CRM_Contribute_PseudoConstant::contributionPage',
    ],
    'settings_pages' => [
      'aegir' => [
        'weight' => 12,
      ],
    ],
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
    'html_type' => 'select',
    'html_attributes' => [
      'multiple' => 1,
      'class' => 'crm-select2',
    ],
    'pseudoconstant' => [
      'callback' => 'CRM_Contribute_PseudoConstant::contributionPage',
    ],
    'settings_pages' => [
      'aegir' => [
        'weight' => 13,
      ],
    ],
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
    'html_type' => 'select',
    'html_attributes' => array(
      'multiple' => 1,
      'class' => 'crm-select2',
    ),
    'pseudoconstant' => [
      'callback' => 'CRM_Contribute_PseudoConstant::contributionPage',
    ],
    'settings_pages' => [
      'aegir' => [
        'weight' => 14,
      ],
    ],
  ],
  'symbiocivicrm_domain_suffix' => [
    'group_name' => 'domain',
    'group' => 'symbiocivicrm',
    'name' => 'symbiocivicrm_domain_suffix',
    'type' => 'String',
    'default' => NULL,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => E::ts('Domain name suffix'),
    'description' => E::ts('Example: enter example.org so that all sites are created as site1.example.org. If left empty, it will let the user enter any kind of domain, but the domain should exist before the creation of the site, otherwise Aegir/Letsencrypt will not be able to create an https certificate.'),
    'help_text' => '',
    'html_type' => 'text',
    'settings_pages' => [
      'aegir' => [
        'weight' => 15,
      ],
    ],
  ],
  'symbiocivicrm_domain_name' => [
    'group_name' => 'domain',
    'group' => 'symbiocivicrm',
    'name' => 'symbiocivicrm_domain_name',
    'type' => 'Integer',
    'default' => NULL,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => E::ts('Domain name custom field'),
    'description' => E::ts('Custom field for the domain name requested by the user. You must create a custom field applicable for contributions, and include that field in the contribution page profilse. If a suffix is present, it will be added to this domain.'),
    'help_text' => '',
    'html_type' => 'select',
    'pseudoconstant' => [
      'callback' => 'CRM_Symbiotic_Utils::getContributionCustomFields',
    ],
    'settings_pages' => [
      'aegir' => [
        'weight' => 16,
      ],
    ],
  ],
];
