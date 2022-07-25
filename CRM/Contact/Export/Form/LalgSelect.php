<?php

/**
 * This class is for printing Labels
 * using a customised version of Mailing Labels - Print.
 *
 * The only purpose is to have our own form name for hooks etc
 */
class CRM_Contact_Export_Form_LalgSelect extends CRM_Contact_Export_Form_Select {

  public function buildQuickForm() {
    parent::buildQuickForm();
    CRM_Utils_System::setTitle('Export LALG Mailing Labels');
  }
  
  // Would like to customise form template, but too hard - 
  // We'll use hook_civicrm_buildForm() to add js to tweak it.
} 
  
