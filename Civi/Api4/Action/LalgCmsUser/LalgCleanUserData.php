<?php
namespace Civi\Api4\Action\LalgCmsUser;
use \Civi\Api4\Generic\Result;
use \Civi\Api4\LalgCommon\LalgCleanCommon;

/**
 * Custom Action to clean all data related to a Drupal User.  Includes the Drupal User 
 * and the associated Contact record, Contributions, Memberships and Activities. 
 *  
 * This is a permanent delete, and non-reversible.
 *
 * It is designed to be called from Search Kit's apiBatch facility, whose documentation 
 * says that it is called once per row, but actually it is called with a 'where' clause 
 * including an array of the selected Ids.
 *
 * This is closely based on the equivalent Action for use with CiviCRM Contacts.
 *
 * @see \Civi\Api4\Generic\AbstractAction
 */
class LalgCleanUserData extends \Civi\Api4\Generic\AbstractAction {
	
  /**
   * Where clause for further API calls, with Id to be deleted...
   *   .. because this is the way that Search Kit apiBatch facility delivers the Id.
   *   IN FACT this is the Id in the lalg_cms_user table, not the User Id we want
   *     so will have to look it up.
   *
   * We define this parameter just by declaring this variable. 
   * It will appear in the _API Explorer_, with
   * a getter/setter magically provided: `$this->setXxxx()` and `$this->getXxxx()`.
   */
  protected $where;			// Format: [["id","IN",["<id>", "<id>", ...]]];
  
  /**
   * Id for a simple, single, User Id.  API4 Explorer uses this - useful for testing.
   *
   * @var int
   */
  protected $tableId; 
  
  /**
   * Every action must define a _run function to perform the work and place results in the Result object.
   *
   * The overall process is:
   *   Get the User Id(s).  Then For Each:
   *     Get the CiviCRM Contact Id
   *     Delete Activities
   *     Delete Contributions
   *     Delete Contact Record.  Flows down to Address, Email, Membership, Groups
   *   With the Drupal User Id
   *     Reassign Nodes to Anonymous
   *     Delete Drupal User.
   *
   * @param $this->where 
   * @param Result $result
   */
  public function _run(Result $result) {
// dpm($this);

// Construct the array of User Ids depending on the parameter(s) given.
    $_userIds = [];
    if (isset($this->tableId)) {					// Use the Single Id 
	  $_userIds[] = $this->getUserId($this->tableId);
	}
	elseif (isset($this->where)) {					// Get the array in the 'where' clause
	  $tableIds = $this->where[0][2];
	  foreach ($tableIds as $id) {
	    $_userIds[] = $this->getUserId($id);	
	  }
	}
	else {
	  throw new Exception('No valid Id parameter detected');
	}
// dpm($_userIds);

/**
  *  Process actions for all Users 
  */
	foreach ($_userIds as $uid) {
		
	// Get the CiviCRM Contact Id (if any)
      $matches = \Civi\Api4\UFMatch::get()
        ->addSelect('contact_id')
        ->addWhere('uf_id', '=', $uid)
        ->execute();
		
 	// Check and get Contact Id, Then clean Contact data
      if ($matches->first()) {
        $cid = $matches->first()['contact_id'];		
// dpm("Cleaning Contact Id: " . $cid);
        LalgCleanCommon::cleanContactData($cid);
	  }

    // And then clean User data
// dpm("Cleaning User Id: " . $uid);
      LalgCleanCommon::cleanUserData($uid);
    }  

	// Re-sync the LalgCmsUser cached table of users
	\Civi\Api4\LalgCmsUser::sync()
      ->execute();
	
	// And return a result.
    $result[] = [
      'deleted' => 'Deleted ' . sizeof($_userIds) . ' Users.',
    ];
// dpm($result);
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

  /**
   * Get the Drupal User Id from the LalgCmsUser table Id
   *
   * @return User Id
   */
  function getUserId($tableId) {
    $userIds = \Civi\Api4\LalgCmsUser::get()
      ->addSelect('user_id')
      ->addWhere('id', '=', $tableId)
      ->execute();    
		
    return $userIds->first()['user_id'];
  }

}
