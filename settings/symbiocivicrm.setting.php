<?php

use CRM_Symbiocrm_ExtensionUtil as E;

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
  'symbiocivicrm_site_name_fieldid' => [
    'group_name' => 'domain',
    'group' => 'symbiocivicrm',
    'name' => 'symbiocivicrm_site_name_fieldid',
    'type' => 'String',
    'default' => NULL,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => E::ts('Site Name'),
    'description' => E::ts('Contribution custom field for the human-readable name for the CiviCRM site.'),
    'help_text' => '',
    'html_type' => 'select',
    'pseudoconstant' => [
      'callback' => 'CRM_Symbiotic_Utils::getContributionCustomFields',
    ],
    'settings_pages' => [
      'aegir' => [
        'weight' => 15,
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
        'weight' => 16,
      ],
    ],
  ],
  'symbiocivicrm_domain_name_fieldid' => [
    'group_name' => 'domain',
    'group' => 'symbiocivicrm',
    'name' => 'symbiocivicrm_domain_name_fieldid',
    'type' => 'Integer',
    'default' => NULL,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => E::ts('Domain name custom field'),
    'description' => E::ts('Custom field for the domain name requested by the user. You must create a custom field applicable for contributions, and include that field in the contribution page profiles.'),
    'help_text' => '',
    'html_type' => 'select',
    'pseudoconstant' => [
      'callback' => 'CRM_Symbiotic_Utils::getContributionCustomFields',
    ],
    'settings_pages' => [
      'aegir' => [
        'weight' => 17,
      ],
    ],
  ],
  'symbiocivicrm_aegir_server_fieldid' => [
    'group_name' => 'domain',
    'group' => 'symbiocivicrm',
    'name' => 'symbiocivicrm_aegir_server_fieldid',
    'type' => 'Integer',
    'default' => NULL,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => E::ts('Aegir server custom field'),
    'description' => E::ts('Custom field for the domain name requested by the user. You must create a custom field applicable for contributions, and include that field in the contribution page profiles. The structure of this field is a bit unusual, double-check the code of getAegirServer().'),
    'help_text' => '',
    'html_type' => 'select',
    'pseudoconstant' => [
      'callback' => 'CRM_Symbiotic_Utils::getContributionCustomFields',
    ],
    'settings_pages' => [
      'aegir' => [
        'weight' => 18,
      ],
    ],
  ],
  'symbiocivicrm_expired_help' => [
    'group_name' => 'domain',
    'group' => 'symbiocivicrm',
    'name' => 'symbiocivicrm_expired_help',
    'type' => CRM_Utils_Type::T_STRING,
    // @todo This is not working, would prefer a wysiwyg
    'html_type' => 'wysiwyg',
    'attributes' => ['rows' => 2, 'cols' => 40],
    'default' => NULL,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => E::ts('Expiration/Renewal Help'),
    'description' => E::ts('Help text displayed in-app to admins when their membership/hosting has expired.'),
    'help_text' => '',
    'html_type' => 'text',
    'settings_pages' => [
      'aegir' => [
        'weight' => 20,
      ],
    ],
  ],
  'symbiocivicrm_cancellation_help' => [
    'group_name' => 'domain',
    'group' => 'symbiocivicrm',
    'name' => 'symbiocivicrm_cancellation_help',
    'type' => CRM_Utils_Type::T_STRING,
    // @todo This is not working, would prefer a wysiwyg
    'html_type' => 'wysiwyg',
    'attributes' => ['rows' => 2, 'cols' => 40],
    'default' => NULL,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => E::ts('Cancellation Help'),
    'description' => E::ts('Help text displayed in-app to admins when their membership/hosting will renew automatically, but they may want to cancel.'),
    'help_text' => '',
    'html_type' => 'text',
    'settings_pages' => [
      'aegir' => [
        'weight' => 20,
      ],
    ],
  ],
  'symbiocivicrm_membership_types' => [
    'group_name' => 'domain',
    'group' => 'symbiocivicrm',
    'name' => 'symbiocivicrm_membership_types',
    'type' => 'Array',
    'default' => NULL,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => E::ts('Hosting-related membership types'),
    'description' => '',
    'help_text' => '',
    'html_type' => 'select',
    'html_attributes' => [
      'multiple' => 1,
      'class' => 'crm-select2',
    ],
    'pseudoconstant' => [
      'callback' => 'CRM_Member_PseudoConstant::membershipType',
    ],
    'settings_pages' => [
      'aegir' => [
        'weight' => 25,
      ],
    ],
  ],
  'symbiocivicrm_welcome_template_id' => [
    'group_name' => 'domain',
    'group' => 'symbiocivicrm',
    'name' => 'symbiocivicrm_welcome_template_id',
    'type' => 'Integer',
    'default' => NULL,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => E::ts('Welcome Message Template'),
    'description' => '',
    'help_text' => '',
    'html_type' => 'select',
    'html_attributes' => [
      'multiple' => 0,
      'class' => 'crm-select2',
    ],
    'pseudoconstant' => [
      'callback' => 'CRM_Symbiotic_Utils::listOfMessageTemplates',
    ],
    'settings_pages' => [
      'aegir' => [
        'weight' => 30,
      ],
    ],
  ],
];
