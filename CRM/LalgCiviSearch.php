<?php

class CRM_LalgCiviSearch {

  /**
   * This function clears the print flag and creates an Activity to record the print.
   * @param  mixed $cids Contact IDS - Array or comma seperated integers
   * Note - these are the ids of people, not the households.
   */
  public static function clear_print_flag($cids) {
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

  /**
   * This function Sets the Print Reminder Activity from 'Scheduled' to Completed.
   * @param  mixed $cids Contact IDS - Array or comma seperated integers
   * Note - these are the ids of people, not the households.
   */  
  public static function clear_activities($cids) {
    if (!is_array($cids)) {
      $cids = explode(",", $cids);
    }
//dpm($cids);	
	// Get the (list of) relevant Activities
    foreach ($cids as $cid) {
		$activities = \Civi\Api4\Activity::get()
			->addJoin('Contact AS contact', 'LEFT', 'ActivityContact')
			->addWhere('activity_type_id', '=', 56)	// Print Postal Reminder
			->addWhere('status_id', '=', 1)			// Scheduled
			->addWhere('contact.id', '=', $cid)
			->execute();
//dpm($activities);	
		// Set Status of each to Completed		
		foreach ($activities as $activity) {
			$results = \Civi\Api4\Activity::update()
				->addValue('status_id', 2)			// Completed
				->addWhere('id', '=', $activity['id'])
				->execute();
		}
//dpm($results);
	}
  }	

}
