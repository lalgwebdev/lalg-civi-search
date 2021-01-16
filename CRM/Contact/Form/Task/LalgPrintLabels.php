<?php

/**
 * This class is for printing Labels
 * using a customised version of Mailing Labels - Print.
 *
 * The only purpose is to have our own form name for hooks etc
 */
class CRM_Contact_Form_Task_LalgPrintLabels extends CRM_Contact_Form_Task_Label {

  public function buildQuickForm() {
    parent::buildQuickForm();
    CRM_Utils_System::setTitle('Print LALG Mailing Labels');
  }
  
  // Would like to customise form template, but too hard - 
  // We'll use hook_civicrm_buildForm() to add js to tweak it.
} 
  
