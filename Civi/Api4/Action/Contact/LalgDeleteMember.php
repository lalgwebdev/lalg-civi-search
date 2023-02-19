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
   * Id of the Contact to be deleted..
   *
   * We define this parameter just by declaring this variable. It will appear in the _API Explorer_,
   * and a getter/setter are magically provided: `$this->setXxxx()` and `$this->getXxxx()`.
   *
   * @required
   * @var int
   */
  protected $contactId ;

  /**
   * Every action must define a _run function to perform the work and place results in the Result object.
   *
   * @param $this->contactId
   * @param Result $result
   */
  public function _run(Result $result) {
	  
dpm($this);
	  
    $dname = civicrm_api4('Contact', 'get', [
      'select' => ['display_name'],
      'where' => [
        ['id', '=', $this->contactId],
      ],
    ]);
//	$result[0] = $dname[0]['display_name'];
	
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
