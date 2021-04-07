<?php

class CRM_LalgCiviSearch {

  /**
   * This function clears the print flag and creates an Activity to record the print.
   * @param  mixed $cids Contact IDS - Array or comma seperated integers
   * Note - these are the ids of people, not the households.
   */
  public function clear_print_flag($cids) {
    if (!is_array($cids)) {
      $cids = explode(",", $cids);
    }

    foreach ($cids as $cid) {
		// Set printfield off
		// Get Id of join table entry
		$entityTagId = civicrm_api3('EntityTag', 'getvalue', [
		  'return' => "id",
		  'entity_table' => "civicrm_contact",
		  'tag_id' => "Print Card",
		  'entity_id' => $cid,
		]);
		// Delete
		$result = civicrm_api3('EntityTag', 'delete', [
		  'id' => $entityTagId,
		  'contact_id' => $cid,
		]);
		// Create Activity
		$result = civicrm_api3('Activity', 'create', [
		  'activity_type_id' => 55,				// "Print Membership Card",
		  'status_id' => 2,						// "Completed",
		  'target_id' => $cid,
		  'subject' => "LALG Membership Card",
		]);
//		dpm($result);
    }
  }

}
