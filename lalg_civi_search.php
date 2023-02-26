<?php

require_once 'lalg_civi_search.civix.php';
use CRM_LalgCiviSearch_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function lalg_civi_search_civicrm_config(&$config) {
  _lalg_civi_search_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function lalg_civi_search_civicrm_xmlMenu(&$files) {
  _lalg_civi_search_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function lalg_civi_search_civicrm_install() {
  _lalg_civi_search_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function lalg_civi_search_civicrm_postInstall() {
  _lalg_civi_search_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function lalg_civi_search_civicrm_uninstall() {
  _lalg_civi_search_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function lalg_civi_search_civicrm_enable() {
  _lalg_civi_search_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function lalg_civi_search_civicrm_disable() {
  _lalg_civi_search_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function lalg_civi_search_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _lalg_civi_search_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function lalg_civi_search_civicrm_managed(&$entities) {
  _lalg_civi_search_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function lalg_civi_search_civicrm_caseTypes(&$caseTypes) {
  _lalg_civi_search_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function lalg_civi_search_civicrm_angularModules(&$angularModules) {
  _lalg_civi_search_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function lalg_civi_search_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _lalg_civi_search_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function lalg_civi_search_civicrm_entityTypes(&$entityTypes) {
  _lalg_civi_search_civix_civicrm_entityTypes($entityTypes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function lalg_civi_search_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function lalg_civi_search_civicrm_navigationMenu(&$menu) {
  _lalg_civi_search_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _lalg_civi_search_civix_navigationMenu($menu);
} // */


/************************************************************/
/*     LALG Functions added manually                        */
/************************************************************/

/**
 * Implements hook_civicrm_searchTasks().
 * Adds tasks for printing membership cards etc.
 */
function lalg_civi_search_civicrm_searchTasks($objectName, &$tasks) {
  if ($objectName == 'contact') {
    $tasks[] = [
      'title' => 'LALG - Print Membership Cards',
      'class' => 'CRM_Contact_Form_Task_LalgPrintCards'
    ];
    $tasks[] = [
      'title' => 'LALG - Print Labels',
      'class' => 'CRM_Contact_Form_Task_LalgPrintLabels'
    ];
    $tasks[] = [
      'title' => 'LALG - Export CSV Addresses',
      'class' => ['CRM_Contact_Export_Form_LalgSelect', 'CRM_Contact_Export_Form_LalgMap']
    ];
    $tasks[] = [
      'title' => 'LALG - Delete Members',
      'class' => 'CRM_Contact_Form_Task_LalgDeleteMembers'
    ];
  }
}

/**
 * Implements hook_civicrm_searchKitTasks().
 *
 * @param array[] $tasks
 * @param bool $checkPermissions
 * @param int|null $userID
 */
function lalg_civi_search_civicrm_searchKitTasks(array &$tasks, bool $checkPermissions, ?int $userID) {
// Registers the Action to Delete/Cancel Members and Memberships.  
// Specified for use in Search Kit apiBatch facility.
// The documentation says it is called once per row, but actually is called with an array of Ids.
  $tasks['Contact']['lalgDeleteMembers'] = [
    'title' => E::ts('LALG Delete Members'),
    'icon' => 'fa-trash',
    'apiBatch' => [
      'action' => 'lalgDeleteMembers', 				// Name of API action to call [once per row]
      'params' => NULL, 							// Optional array of additional api params
      'confirmMsg' => E::ts('This will delete the selected Contacts to the recycle bin, cancel Memberships, and delete any associated Drupal login account. ...................................................................................................................................................................................................................................................
It will also remove all members of a deleted Household, and any Household where all members are deleted. .....................................................................................................................................................................................................................................................................
Are you sure you want to delete %1 %2?'), // If omitted, the action will run immediately with no confirmation.  Forgive the horrible newline substitute.
      'runMsg' => E::ts('Deleting %1 %2...'),
      'successMsg' => E::ts('Successfully deleted %1 selected %2 (plus empty Households and orphaned Members).'),
      'errorMsg' => E::ts('An error occurred while attempting to delete %1 %2.'),
    ],
  ];
}

/**
 * Implements hook_civicrm_buildForm().
 * Adds js to our form
 */
function lalg_civi_search_civicrm_buildForm($formName, &$form) {
	  if ($formName == "CRM_Contact_Form_Task_LalgPrintCards") {
		Civi::resources()->addScriptFile(E::LONG_NAME, 'js/printcards.js');
	  }
	  if ($formName == "CRM_Contact_Form_Task_LalgPrintLabels") {
		Civi::resources()->addScriptFile(E::LONG_NAME, 'js/printlabels.js');
	  }  
	  if ($formName == "CRM_Contact_Export_Form_LalgSelect") {
		Civi::resources()->addScriptFile(E::LONG_NAME, 'js/exportCSV.js');
	  }  
	  if (strpos($_SERVER['REQUEST_URI'], "lalgwf=2" ) !== false) {
		Civi::resources()->addScriptFile(E::LONG_NAME, 'js/searchlabels.js');
	  }	  
	  if (strpos($_SERVER['REQUEST_URI'], "civicrm/dataprocessor_activity_search/membership_postal_reminders" ) !== false) {
		Civi::resources()->addScriptFile(E::LONG_NAME, 'js/searchreminders.js');
	  }	
	  if (strpos($_SERVER['REQUEST_URI'], "civicrm/dataprocessor_contact_search/delete_members" ) !== false) {
		Civi::resources()->addScriptFile(E::LONG_NAME, 'js/deletemembers.js');
	  }
}


/************************************************************/
// Batch Printing Membership Cards
/************************************************************/
/**
 * Implements hook_civicrm_postProcess().
 * Clears the printing flag if the upload button
 * (labelled "Download and clear flags") was used
 */
function lalg_civi_search_civicrm_postProcess($formName, &$form) {
  if ($formName == "CRM_Contact_Form_Task_LalgPrintCards") {
    $buttonName = $form->controller->getButtonName();
    if ($buttonName == '_qf_LalgPrintCards_upload') {
      CRM_LalgCiviSearch::clear_print_flag($form->_contactIds);
    }
  }
}


