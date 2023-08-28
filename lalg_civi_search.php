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
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function lalg_civi_search_civicrm_install() {
  _lalg_civi_search_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function lalg_civi_search_civicrm_enable() {
  _lalg_civi_search_civix_civicrm_enable();
}


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
    $tasks[] = [
      'title' => 'LALG - Print Reminders',
      'class' => 'CRM_Contact_Form_Task_LalgPrintReminders',
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
// Registers Actions for use in Search Kit apiBatch facility.
// The documentation says it is called once per row, but actually is called with an array of Ids.
  $tasks['Contact']['lalgDeleteMembers'] = [
    'title' => E::ts('LALG Delete Members to Trash'),
    'icon' => 'fa-trash',
    'apiBatch' => [
      'action' => 'lalgDeleteMembers', 				// Name of API action to call [once per row]
      'params' => NULL, 							// Optional array of additional api params
      'confirmMsg' => E::ts('Are you sure you want to delete %1 %2?  Plus Membership and Drupal Login, etc.'), // If omitted, the action will run immediately with no confirmation.  
      'runMsg' => E::ts('Deleting %1 %2...'),
      'successMsg' => E::ts('Successfully deleted %1 selected %2 (plus empty Households and orphaned Members).'),
      'errorMsg' => E::ts('An error occurred while attempting to delete %1 %2.'),
    ],
  ];
  
  $tasks['Contact']['lalgCleanContactData'] = [
    'title' => E::ts('LALG Clean All Contact Data'),
    'icon' => 'fa-trash',
    'apiBatch' => [
      'action' => 'lalgCleanContactData', 				// Name of API action to call [once per row]
      'params' => NULL, 							// Optional array of additional api params
      'confirmMsg' => E::ts('Are you sure you want to Permanently Delete %1 %2?  Plus Contributions, Membership and Drupal Login, etc.'), // If omitted, the action will run immediately with no confirmation.  
      'runMsg' => E::ts('Deleting %1 %2...'),
      'successMsg' => E::ts('Successfully deleted %1 selected %2.'),
      'errorMsg' => E::ts('An error occurred while attempting to delete %1 %2.'),
    ],
  ];
  
  $tasks['LalgCmsUser']['lalgCleanUserData'] = [
    'title' => E::ts('LALG Clean All User Data'),
    'icon' => 'fa-trash',
    'apiBatch' => [
      'action' => 'lalgCleanUserData', 				// Name of API action to call [once per row]
      'params' => NULL, 							// Optional array of additional api params
      'confirmMsg' => E::ts('Are you sure you want to Permanently Delete %1 %2?  Plus CiviCRM Contact, Contributions, Membership, etc.'), // If omitted, the action will run immediately with no confirmation.  
      'runMsg' => E::ts('Deleting %1 %2...'),
      'successMsg' => E::ts('Successfully deleted %1 selected %2.'),
      'errorMsg' => E::ts('An error occurred while attempting to delete %1 %2.'),
    ],
  ];

}

/**
 * Implements hook_civicrm_buildForm().
 * Adds js to our form
 */
function lalg_civi_search_civicrm_buildForm($formName, &$form) {
	// Print Membership Cards
	  if ($formName == "CRM_Contact_Form_Task_LalgPrintCards") {
		Civi::resources()->addScriptFile(E::LONG_NAME, 'js/printcards.js');
	  }
  
    // Print/Export Newsletter Labels
	  if (strpos($_SERVER['REQUEST_URI'], "lalgwf=2" ) !== false) {
		Civi::resources()->addScriptFile(E::LONG_NAME, 'js/searchlabels.js');
	  }	 
	  if ($formName == "CRM_Contact_Form_Task_LalgPrintLabels") {
		Civi::resources()->addScriptFile(E::LONG_NAME, 'js/printlabels.js');
	  }	  
	  if ($formName == "CRM_Contact_Export_Form_LalgSelect") {
		Civi::resources()->addScriptFile(E::LONG_NAME, 'js/exportCSV.js');
	  }	  
	  
	// Print Reminders (OLD)  
	  // if (strpos($_SERVER['REQUEST_URI'], "civicrm/dataprocessor_activity_search/membership_postal_reminders" ) !== false) {
		// Civi::resources()->addScriptFile(E::LONG_NAME, 'js/searchreminders.js');
	  // }	
	  
	// Delete Members  
	  if (strpos($_SERVER['REQUEST_URI'], "civicrm/dataprocessor_contact_search/delete_members" ) !== false) {
		Civi::resources()->addScriptFile(E::LONG_NAME, 'js/deletemembers.js');
	  }
	  
	// Print Reminders (New)  
//dpm($_SERVER['REQUEST_URI']);  
	  if (strpos($_SERVER['REQUEST_URI'], "lalgwf=3" ) !== false) {
		Civi::resources()->addScriptFile(E::LONG_NAME, 'js/searchreminders.js');
	  }	  
//dpm($formName);
	  if ($formName == "CRM_Contact_Form_Task_LalgPrintReminders") {
		Civi::resources()->addScriptFile(E::LONG_NAME, 'js/printreminders.js');
	  }  
}

/************************************************************/
// Batch Printing Membership Cards etc.
/************************************************************/
/**
 * Implements hook_civicrm_postProcess().
 * Clears the printing flag if the upload button
 * (labelled "Download and clear flags") was used
 * And similarly for Print Reminder Activities
 */
function lalg_civi_search_civicrm_postProcess($formName, &$form) {
//dpm($formName);
//dpm($form);
// Print Membership Cards custom search
  if ($formName == "CRM_Contact_Form_Task_LalgPrintCards") {
    $buttonName = $form->controller->getButtonName();
    if ($buttonName == '_qf_LalgPrintCards_upload') {
      CRM_LalgCiviSearch::clear_print_flag($form->_contactIds);
    }
  }
  
// Print Reminders
  else if ($formName == "CRM_Contact_Form_Task_LalgPrintReminders") { 
	$buttonName = $form->controller->getButtonName();
    if ($buttonName == '_qf_LalgPrintReminders_upload') {  
      CRM_LalgCiviSearch::clear_activities($form->_contactIds);
    }
  }
}
