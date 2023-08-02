<?php
namespace Civi\Api4\Action\Contact;
use Civi\Api4\Generic\Result;
use \Drupal\node\Entity\Node;
use \Drupal\user\Entity\User;

/**
 * Custom Action to clean all data related to a Contact.  Includes the Contact record,
 * associated Drupal User, Contributions, Memberships and Activities. 
 *  
 * This is a permanent delete, and non-reversible.
 *
 * It is designed to be called from Search Kit's apiBatch facility, whose documentation 
 * says that it is called once per row, but actually it is called with a 'where' clause 
 * including an array of the selected Ids.
 *
 * This is a port of the previous Task for use with VBO.
 *
 * @see \Civi\Api4\Generic\AbstractAction
 */
class LalgCleanContactData extends \Civi\Api4\Generic\AbstractAction {
	
  /**
   * Where clause for a further API calls, with Id of the Contact to be deleted..
   *   .. because this is the way that Search Kit apiBatch facility delivers the Id.
   *
   * We define this parameter just by declaring this variable. 
   * It will appear in the _API Explorer_, with
   * a getter/setter magically provided: `$this->setXxxx()` and `$this->getXxxx()`.
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
   *   Get the Contact Id(s).  Then For Each:
   *     Delete Activities
   *     Delete Contributions
   *     Get the Drupal User Id
   *       Reassign Nodes to Anonymous
   *       Delete Drupal User.
   *     Delete Contact Record.  Flows down to Address, Email, Membership, Groups
   *
   * @param $this->where 
   * @param Result $result
   */
  public function _run(Result $result) {
// dpm($this);

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
// dpm($_contactIds);

/**
  *  Process actions for all Contacts 
  */
	foreach ($_contactIds as $cid) {

    // Delete Activities where this Contact is the Target (i.e. not created by)
	  $results = \Civi\Api4\ActivityContact::get()
        ->addSelect('activity_id')
        ->addWhere('contact_id', '=', $cid)
        ->addWhere('record_type_id', '=', 3)
        ->addChain('name_me_0', \Civi\Api4\Activity::delete()
          ->addWhere('id', '=', '$activity_id'), 
        0)
        ->execute();

    // Delete Contributions by this Contact
	  $results = \Civi\Api4\Contribution::delete()
        ->addWhere('contact_id', '=', $cid)
        ->execute();
    
	// Get the Drupal User Id (if any)
      $results = \Civi\Api4\UFMatch::get()
        ->addSelect('uf_id')
        ->addWhere('contact_id', '=', $cid)
        ->execute();
	// Check and get User Id and User object
      if ($results->first()) {
        $userId = $results->first()['uf_id'];		
        if ($userId) {
          $user = \Drupal\user\Entity\User::load($userId); // get the User Entity
// dpm($userId);
          if($user) {
// dpm("Reassigning User's Nodes");
            $nodes = \Drupal::entityTypeManager()
	          -> getStorage('node')
			  -> loadByProperties([
			       'uid' => $userId,
			     ]);
// dpm($nodes);             		  
            foreach($nodes as $node) {
	          $node->uid = 0;
			  $node->save(); 
// dpm($node);		    
            } 
		  
// dpm('Deleting Drupal User');			  
            $user->delete();
          } 
        }
      }
	  
	// Permanently Delete the Contact record
	// Flows down to associated address, Email, Membership, membership of Groups.
// dpm('Deleting Contact Record');
	  $results = \Civi\Api4\Contact::delete()
        ->addWhere('id', '=', $cid)
		->setUseTrash(FALSE)
        ->execute();
// dpm($results);	  
	}  
	
	// Re-sync the LalgCmsUser cached table of users
	\Civi\Api4\LalgCmsUser::sync()
      ->execute();

	// And return a result.
    $result[] = [
      'deleted' => 'Deleted ' . sizeof($_contactIds) . ' Contacts.',
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
}