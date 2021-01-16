<?php
use CRM_LalgCiviSearch_ExtensionUtil as E;

/**
 ****************************  LALG Custom Search  ********************
 * For use as start of the Delete Members workflow
 * Extends 'Basic' Custom Search
 */
class CRM_LalgCiviSearch_Form_Search_DeleteMembers extends CRM_Contact_Form_Search_Custom_Basic implements CRM_Contact_Form_Search_Interface {

   /**
   * Class constructor.
   *
   * @param array $formValues
   */
  public function __construct(&$formValues) {
    parent::__construct($formValues);

    $this->_columns = array(
      '' => 'contact_type',
      ts('Name') => 'sort_name',
      ts('Address') => 'street_address',
      ts('City') => 'city',
      ts('Postcode') => 'postal_code',
      ts('Email') => 'email',
    );
  }
  
  /**
   * Build the Search Form
   *   @param CRM_Core_Form $form
   */
  public function buildForm(&$form) {
	parent::buildForm($form);
    CRM_Utils_System::setTitle(E::ts('Contacts to Delete'));
  }  
  
  /**
   * Modify the tasklist
   *
   * Specify the tasks to be available.  The important bit is $key below.
   */
  public function buildTaskList($form) {
    $tasks = parent::buildTaskList($form);
	//dpm($tasks);
    $newtasks = [];
    foreach ($tasks as $key => $title) {
      if ($title == 'LALG - Delete Members') {
        $newTasks[$key] = $title;
      }
    }
    return $newTasks;
  }  

}


