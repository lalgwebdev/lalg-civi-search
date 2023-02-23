<?php
namespace Civi\Api4\Action\Contact;
use Civi\Api4\Generic\Result;

/**
 * Custom Action to Cancel/Delete one or more Members to the Trash, and cancel Memberships. 
 *  It also tidies up any remaining empty Household or orphaned Individual(s), 
 *  depending on original Contact(s) specified, and removes any Drupal logins.
 *
 * It is designed to be called from Search Kit's apiBatch facility, whose documentation 
 * says that it is called once per row, but actually it is called with a 'where' clause 
 * including an array of the selected Ids.
 *
 * This is a port of the previous Task for use with Data Processor.
 *
 * @see \Civi\Api4\Generic\AbstractAction
 */
class LalgDeleteMembers extends \Civi\Api4\Generic\AbstractAction {
	
  /**
   * Where clause for a further API call, with Id of the Contact to be deleted..
   *   .. because this is the way that Searck Kit apiBatch facility delivers the Id.
   *
   * We define this parameter just by declaring this variable. It will appear in the _API Explorer_,
   * and a getter/setter are magically provided: `$this->setXxxx()` and `$this->getXxxx()`.
   */
  protected $where;			// Format: [["id","IN",["<id>", "<id>", ...]]];
  
  /**
   * Id for a simple, single, Contact Id.  API Explorer uses this - useful for testing.
   *
   * @var int
   */
  protected $contactId; 
  
  /**
   * Every action must define a _run function to perform the work and place results in the Result object.
   *
   * The overall process is:
   * Check whether all necessary dependent Contacts are included
   *	Add any Household where all members deleted
   *    Add any members where Household deleted
   * For each Household - cancel associated Membership
   * For each Individual - delete any associated Drupal Login.
   * Delete all Contacts to Trash.
   *
   * @param $this->where 
   * @param Result $result
   */
  public function _run(Result $result) {
dpm($this);

// Construct the array of Contact Ids depending on the parameter(s) given.
    if (isset($this->contactId)) {						// Use the Single Id 
	  $_contactIds = [$this->contactId];
	}
	elseif (isset($this->where)) {						// Use the array in the 'where' clause
	  $_contactIds = $this->where[0][2];
	}
	else {
	  throw new Exception('No valid Id parameter detected');
	}
dpm($_contactIds);

/**
  *  For each Household on the list 
  *    add any members not already on it.  
  */
	foreach ($_contactIds as $cid) {
		//Check if is Household
		$cType = \Civi\Api4\Contact::get()
		->addSelect('contact_type')
		->addWhere('id', '=', $cid)
		->execute()
		->first()['contact_type'];

		if ($cType !== 'Household') { break; } 

dpm('Getting Relationships');		
		$relns = \Civi\Api4\Relationship::get()
			->addSelect('contact_id_a')
			->addWhere('contact_id_b', '=', $cid)
			->addWhere('contact_id_a.is_deleted', '=', FALSE)
			->execute();
dpm($relns);		

		// For each Relationship
		foreach ($relns as $reln) {
			// Get related Individual
			$memberId = $reln['contact_id_a'];
			// If not on the list, then add.
			if (!in_array($memberId, $_contactIds)) {
dpm ('Adding Individual: ' . $memberId);
				$_contactIds[] = (int)$memberId;
			}
		}
	}
dpm($_contactIds);

/**
 *  For each Individual on the list 
 *     check if their Household will be empty  
 */
	foreach ($_contactIds as $cid) {
dpm('Loop 1 : ' . $cid);
		// Check if is Individual
		$cType = \Civi\Api4\Contact::get()
		->addSelect('contact_type')
		->addWhere('id', '=', $cid)
		->execute()
		->first()['contact_type'];

		if ($cType !== 'Individual') { break; } 
		
		// Get related Household
		$hhId = \Civi\Api4\Relationship::get()
		->addSelect('contact_id_b')
		->addWhere('contact_id_a', '=', $cid)
		->execute()
		->first()['contact_id_b'];

		// If HH on the list GOTO next Individual
		if (in_array($hhId, $_contactIds)) { break;}
		
		// Else For each Relationship
		$relns = \Civi\Api4\Relationship::get()
            ->addSelect('contact_id_a')
			->addWhere('contact_id_b', '=', $hhId)
			->execute();		
dpm($relns);

		foreach ($relns as $reln) {
			// Get related Individual
			$memberId = $reln['contact_id_a'];
dpm('Loop 2 : ' . $memberId);
			// If related Individual not on the list GoTo next Relationship
			if (!in_array($memberId, $_contactIds)) { break 2; }
		}	
		// (All members are on the list) so add HH to list
dpm('Adding Household: ' . $hhId);
		$_contactIds[] = (int)$hhId;
	}
dpm($_contactIds);


/**
 * Tidy up other items:
 *   Cancel associated Membership for Households
 *   Delete associated Drupal Users
 */
	// For each Contact on the list
	foreach ($_contactIds as $cid) {
		// Split on Contact Type
		$cType = \Civi\Api4\Contact::get()
		->addSelect('contact_type')
		->addWhere('id', '=', $cid)
		->execute()
		->first()['contact_type'];
		
		if ($cType == 'Household') {
			// Cancel associated Membership
			$memId = \Civi\Api4\Membership::get()
			->addSelect('id')
			->addWhere('contact_id', '=', $cid)
			->execute()
			->first()['id'];

dpm('Cancelling Membership. Id: ' . $memId);
			if($memId) {
				$memResult = \Civi\Api4\Membership::update()
				->addValue('id', $memId)
				->addValue('contact_id', $cid)
				->addValue('status_id', 6)						//Cancelled
				->addValue('is_override', TRUE)
				->execute();
			}
		}

		else {								// Is Individual
			// Delete Drupal Account
			try {
				$userId = \Civi\Api4\UFMatch::get()
				->addWhere('contact_id', '=', $cid)
				->execute()
				->first()['uf_id'];		
				
				if ($userId) {
					$user = \Drupal\user\Entity\User::load($userId); // get the User Entity
dpm('Deleting Drupal User');
					if ($user) {
						$user->delete();
					}
				}
			}
			catch (Exception $e) {
				// Throws error if no User, so ignore it
				//dpm($e);
			}
		}
	}
	
/**
 *  Finally - Delete all Contacts on the list - to Trash.
 */
dpm('Deleting Contacts');
	$delResult = \Civi\Api4\Contact::delete()
	->addWhere('id', 'IN', $_contactIds)
	->execute();
dpm($delResult);		

	// And return a result.
    $result[] = [
      'deleted' => 'Deleted ' . sizeof($_contactIds) . ' Contacts.',
    ];
dpm($result);
  }

  /**
   *  Cancel Household's Membership.  Flows down to Individuals as usual.
   */
	function _cancelHHMembership($hhId) {
		// Get Membership Id
		$memId = \Civi\Api4\Membership::get()
		->addSelect('id')
		->addWhere('contact_id', '=', $hhId)
		->execute()
		->first()['id'];
	
		// Cancel Membership
dpm('Cancelling Membership. Id: ' . $memId);
		if($memId) {
			$memResult = \Civi\Api4\Membership::update()
			->addValue('id', $memId)
			->addValue('contact_id', $cid)
			->addValue('status_id', 6)						//Cancelled
			->addValue('is_override', TRUE)
			->execute();
		}
	}

  /**
   * Declare ad-hoc field list for this action.
   *
   * @return array
   */
  public static function fields() {
    return [
      ['name' => 'deleted'],
    ];
  }

}
