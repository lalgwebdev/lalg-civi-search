<?php

namespace Civi\Api4\Action\Contact;

use Civi\Api4\Generic\Result;

/**
 * Custom Action to Cancel/Delete a Member, with all Contributions, and tidy up any remaining 
 * empty Household or orphaned Individual(s) depending on original Contact specified.
 *
 * This is a skeleton which just returns the Contact's name, given the Id.
 *
 * @see \Civi\Api4\Generic\AbstractAction
 */
class LalgDeleteMember extends \Civi\Api4\Generic\AbstractAction {
	
  /**
   * Where clause for a further API call, with Id of the Contact to be deleted..
   *   .. because this is the way that Searck Kit apiBatch facility delivers the Id.
   *
   * We define this parameter just by declaring this variable. It will appear in the _API Explorer_,
   * and a getter/setter are magically provided: `$this->setXxxx()` and `$this->getXxxx()`.
   */
  protected $where = [["id","IN",["<id>"]]];
  
  /**
   * Id for a simple, single, Contact Id.  API Explorer uses this.
   *
   * We define this parameter just by declaring this variable. It will appear in the _API Explorer_,
   * and a getter/setter are magically provided: `$this->setXxxx()` and `$this->getXxxx()`.
   *
   * @var int
   */
  protected $contactId; 
  
  /**
   * Every action must define a _run function to perform the work and place results in the Result object.
   *
   * @param $this->where 
   * @param Result $result
   */
  public function _run(Result $result) {
// dpm($this);
    // Construct the Where clause depending on the parameter(s) given.
    if (isset($this->contactId)) {
	  $myWhere = [['id', 'IN', [$this->contactId]]];
	}
	elseif (isset($this->where)) {
	  $myWhere = $this->where;
	}
	else {
	  throw new Exception('No valid Id parameter detected');
	}
		
    $dname = civicrm_api4('Contact', 'get', [
      'select' => ['display_name'],
      'where' => $myWhere,
    ]);
// dpm($dname);
	
    $result[] = [
      'name' => 'Name of input Id:  ' . $dname[0]['display_name'],
    ];
  }

  /**
   * Declare ad-hoc field list for this action.
   *
   * @return array
   */
  public static function fields() {
    return [
      ['name' => 'name'],
    ];
  }

}
