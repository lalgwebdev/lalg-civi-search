<?php

/**
 * This class is for Cancelling Members/Memberships
 * using a customised version of Delete.
 *
 * The basic operation is to
 *	Delete to Trash the Contact involved
 *	Delete to Trash the associated Household if this was the last member of that HH
 *	Set the Membership to Cancelled if HH was deleted.
 *
 * It is also useful to have our own form name for hooks etc
 */
class CRM_Contact_Form_Task_LalgDeleteMembers extends CRM_Contact_Form_Task_Delete {

// Set Form Title
  public function buildQuickForm() {
    parent::buildQuickForm();
    CRM_Utils_System::setTitle('Cancel Members');
  }

/**
 * Check whether all necessary dependent Contacts are included
 *	Add any Household where all members deleted
 *  Add any members where Household deleted
 */
 
  public function preProcess() {
	parent::preProcess();
	// dpm($this->_contactIds);
	
	// For each Household on the list
	foreach ($this->_contactIds as $cid) {
		$cType = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $cid, 'contact_type');
		if ($cType !== 'Household') { break; } 
		
		// For each Relationship
		$result = civicrm_api3('Relationship', 'get', [
			'sequential' => 1,
			'contact_id_b' => $cid,
		]);
		// dpm($result);
		foreach ($result['values'] as $reln) {
			// Get related Individual
			$memberId = $reln['contact_id_a'];
			//dpm ($memberId);
			// If not on the list, then add.
			if (!in_array($memberId, $this->_contactIds)) {
				$this->_contactIds[] = (int)$memberId;
			}
		}
	
	}
	//dpm($this->_contactIds);
	
	// For each Individual on the list
	foreach ($this->_contactIds as $cid) {
		//dpm('Loop 1 : ' . $cid);
		$cType = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $cid, 'contact_type');
		if ($cType !== 'Individual') { break; } 
		
		// Get related Household
		$hhId = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Relationship', $cid, 'contact_id_b', 'contact_id_a');
		// If HH on the list GOTO next Individual
		if (in_array($hhId, $this->_contactIds)) { break;}
		
		// Else For each Relationship
		$result = civicrm_api3('Relationship', 'get', [
			'sequential' => 1,
			'contact_id_b' => $hhId,
		]);
		// dpm($result);
		foreach ($result['values'] as $reln) {
			// Get related Individual
			$memberId = $reln['contact_id_a'];
			//dpm('Loop 2 : ' . $memberId);
			// If related Individual not on the list GoTo next Individual on list
			if (!in_array($memberId, $this->_contactIds)) { break 2; }
		}	
		// (All members are on the list) so add HH to list
		$this->_contactIds[] = (int)$hhId;
	}
	//dpm($this->_contactIds);
		
  }

  
/**
 * Tidy up after doing the deletes
 *   Block associated Drupal Users
 *   Cancel associated Membership for Households
 */
  public function postProcess() {
	//dpm($this->_contactIds);

	// For each Contact on the list
	foreach ($this->_contactIds as $cid) {
		// Split on Contact Type
		$cType = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $cid, 'contact_type');
		if ($cType == 'Household') {
			// Cancel associated Membership
			$memId = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_Membership', $cid, 'id', 'contact_id');
			if($memId) {
				$result = civicrm_api3('Membership', 'create', [
				  'contact_id' => $cid,
				  'id' => $memId,
				  'status_id' => "Cancelled",
				  'is_override' => 1,
				]);
			}
		}

		else {
			// Block Drupal Account
			try {
				$result = civicrm_api3('User', 'get', [
				  'sequential' => 1,
				  'contact_id' => $cid,
				]);
//				dpm($result);
				$userId = $result['values'][0]['id'];
				if ($userId) {
					$empty = NULL;
					user_block_user_action($empty , array('uid' => $userId));		// First param must be a variable
				}
			}
			catch (Exception $e) {
				// Throws error if no User, so ignore it
			}
		}
	}
	parent::postProcess();
  }

}


