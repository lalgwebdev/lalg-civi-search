<?php

/**
 * This class is for printing Membership Cards
 * using a customised version of PDF/Letter merge.
 *
 * Unfortunately the current version of  CRM_Contact_Form_Task_PDF
 * is not written with extending in mind.  Ideally we could
 * change the form template and defaults here, and handle
 * postProcessing.
 *
 * It is useful to have our own form name for hooks etc
 */
class CRM_Contact_Form_Task_LalgPrintCards extends CRM_Contact_Form_Task_PDF {

  public function buildQuickForm() {
    parent::buildQuickForm();
    CRM_Utils_System::setTitle('Print LALG Membership Cards');
  }

  // Woud like to customise form template
  // We'll use hook_civicrm_buildForm() to add js
  // to tweak it.

  // Would like to handle clearing the flag here, but
  // parent::postProcess calls civiExit() instead of
  // returning.  But it does call hook_civicrm_postProcess()
  // so we'll use that.

}
