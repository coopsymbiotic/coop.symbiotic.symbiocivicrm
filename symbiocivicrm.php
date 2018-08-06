<?php

require_once 'symbiocivicrm.civix.php';
use CRM_Symbiocivicrm_ExtensionUtil as E;

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function symbiocivicrm_civicrm_config(&$config) {
  _symbiocivicrm_civix_civicrm_config($config);

  // [ML] Il faut quand même configurer cette option pour CiviCase,
  // car ça vérifie explicitement ce répertoire pour les fichiers xml.
  $config->customTemplateDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates';
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function symbiocivicrm_civicrm_xmlMenu(&$files) {
  _symbiocivicrm_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function symbiocivicrm_civicrm_install() {
  return _symbiocivicrm_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function symbiocivicrm_civicrm_uninstall() {
  return _symbiocivicrm_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function symbiocivicrm_civicrm_enable() {
  return _symbiocivicrm_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function symbiocivicrm_civicrm_disable() {
  return _symbiocivicrm_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function symbiocivicrm_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _symbiocivicrm_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function symbiocivicrm_civicrm_managed(&$entities) {
  return _symbiocivicrm_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function symbiocivicrm_civicrm_caseTypes(&$caseTypes) {
  _symbiocivicrm_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function symbiocivicrm_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _symbiocivicrm_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_buildForm() is a completely overkill way.
 * Searches for an override class named after the initial $formName
 * and calls its buildForm().
 *
 * Ex: for a $formName "CRM_Case_Form_CaseView", it will:
 * - try to find * CRM/Symbiotic/Case/Form/CaseView.php,
 * - require_once the file, instanciate an object, and
 * - call its buildForm() function.
 *
 * Why so overkill? My buildForm() implementations tend to become
 * really big and numerous, and even if I split up into multiple
 * functions, it still makes a really long php file.
 */
function symbiocivicrm_civicrm_buildForm($formName, &$form) {
  $formName = str_replace('CRM_', 'CRM_Symbiotic_', $formName);
  $parts = explode('_', $formName);
  $filename = dirname(__FILE__) . '/' . implode('/', $parts) . '.php';

  if (file_exists($filename)) {
    require_once $filename;
    $foo = new $formName;

    if (method_exists($foo, 'buildForm')) {
      $foo->buildForm($form);
    }
  }
}

/**
 * Implements hook_civicrm_pageRun() is a completely overkill way.
 * Searches for an override class named after the initial $formName
 * and calls its buildForm().
 *
 * Ex: for a $formName "CRM_Case_Form_CaseView", it will:
 * - try to find * CRM/Symbiotic/Case/Page/CaseView.php,
 * - require_once the file, instanciate an object, and
 * - call its pageRun() function.
 *
 * See @symbiocivicrm_civicrm_buildForm() for more background info.
 */
function symbiocivicrm_civicrm_pageRun(&$page) {
  $pageName = get_class($page);
  $pageName = str_replace('CRM_', 'CRM_Symbiotic_', $pageName);
  $parts = explode('_', $pageName);
  $filename = dirname(__FILE__) . '/' . implode('/', $parts) . '.php';

  if (file_exists($filename)) {
    require_once $filename;
    $foo = new $pageName;

    if (method_exists($foo, 'pageRun')) {
      $foo->pageRun($form);
    }
  }
}
