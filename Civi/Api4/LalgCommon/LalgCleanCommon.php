<?php
namespace Civi\Api4\LalgCommon;

/**
 * Common functions used in the Actions:
 *   LalgCleanContactData
 *   LalgCleanUserData
 */

class LalgCleanCommon {

/**
  *  Clean Data for CiviCRM Contact, including associated Activities, Contributions,
  *      Address, Email, Membership, membership of Groups, etc.
  */
  public static function cleanContactData($cid) {  

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
	
  // Permanently Delete the Contact record
  // Flows down to associated address, Email, Membership, membership of Groups.
//dpm('Deleting Contact Record');
    $results = \Civi\Api4\Contact::delete()
	  ->addWhere('id', '=', $cid)
	  ->setUseTrash(FALSE)
	  ->execute();
//dpm($results);	  
}  
		
		
/**
  *  Clean Data for Drupal User, and reassign any Nodes owned by that User to Anonymous.
  */	
    
  public static function cleanUserData($uid) {  

    if ($uid) {
      $user = \Drupal\user\Entity\User::load($uid); // get the User Entity
      if($user) {		
// dpm("Reassigning User's Nodes");
	    $nodes = \Drupal::entityTypeManager()
	      -> getStorage('node')
	      -> loadByProperties([
	           'uid' => $uid,
		     ]);
// dpm($nodes);             		  
	    foreach($nodes as $node) {
          $node->uid = 0;
	      $node->save(); 
// dpm($node);		    
	    } 
  
//dpm('Deleting Drupal User');			  
	    $user->delete();
      } 
    }
  }

}
	  