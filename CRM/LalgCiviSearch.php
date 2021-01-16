<?php

class CRM_LalgCiviSearch {

  /**
   * This function clears the print flag and creates an Activity to record the print.
   * @param  mixed $cids Contact IDS - Array or comma seperated integers
   * Note - these are the ids of people, not the households.
   */
  public function clear_print_flag($cids) {
	// Get the id of the Send_Membership_Documents custom Field.  
    static $tagid;
    if (!$tagid) {
      $tagid = civicrm_api3('Tag', 'getvalue', [
        'return' => "id",
        'name' => "Print Card",
      ]);
      $tagid = CRM_Utils_Type::escape($tagid, 'Int');
    }

    if (!is_array($cids)) {
      $cids = explode(",", $cids);
    }
	
    foreach ($cids as $cid) {
		// Set printfield off
		$result = civicrm_api3('EntityTag', 'delete', [
		  'tag_id' => $tagid,
		  'entity_id' => $cid,
		]);
		// Create Activity
		$result = civicrm_api3('Activity', 'create', [
//		  'source_contact_id' => "user_contact_id",
		  'activity_type_id' => 55,				// "Print Membership Card",
		  'status_id' => 2,						// "Completed",
		  'target_id' => $cid,
		  'subject' => "LALG Membership Card",
		]);
//		dpm($result);
    }
  }

}
