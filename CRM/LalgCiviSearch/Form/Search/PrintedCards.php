 <?php
use CRM_LalgCiviSearch_ExtensionUtil as E;

/**
 * ************  LALG Custome Search   ****************
 * Finds Individual Contacts who have the 'Send Printed Card' flag set.
 */
class CRM_LalgCiviSearch_Form_Search_PrintedCards extends CRM_Contact_Form_Search_Custom_Base implements CRM_Contact_Form_Search_Interface {

  /**
   * Class constructor.
   *
   * @param array $formValues
   */
  public function __construct(&$formValues) {
    parent::__construct($formValues);
  }

  /**
   * Prepare a set of search fields
   *
   * @param CRM_Core_Form $form modifiable
   * @return void
   */
  public function buildForm(&$form) {
    CRM_Utils_System::setTitle(E::ts('Printed Cards Required'));
  }

  /**
   * Get a list of summary data points
   *
   * @return mixed; NULL or array with keys:
   *  - summary: string
   *  - total: numeric
   */
  public function summary() {
    return NULL;
    // return array(
    //   'summary' => 'This is a summary',
    //   'total' => 50.0,
    // );
  }

  /**
   * Get a list of displayable columns
   *
   * @return array, keys are printable column headers and values are SQL column names
   */
  public function &columns() {
    // return by reference
    $columns = [
      E::ts('Contact Id') => 'contact_id',
      E::ts('Name') => 'sort_name',
      E::ts('Street Address') => 'street_address',
      E::ts('City') => 'city',
    ];
    return $columns;
  }

  /**
   * @param int $offset
   * @param int $rowcount
   * @param null $sort
   * @param bool $returnSQL
   *
   * @return string
   */
  public function contactIDs($offset = 0, $rowcount = 0, $sort = NULL, $returnSQL = FALSE) {
    return $this->all($offset, $rowcount, $sort, FALSE, TRUE);
  }

  /**
   * Construct a full SQL query which returns one page worth of results
   *
   * @param int $offset
   * @param int $rowcount
   * @param null $sort
   * @param bool $includeContactIDs
   * @param bool $justIDs
   * @return string, sql
   */
  public function all($offset = 0, $rowcount = 0, $sort = NULL, $includeContactIDs = FALSE, $justIDs = FALSE) {
    // This is the household contact so will group together
    // Could do something more fancy if we want to sort alphabetically by household name
    $sort = "reln.contact_id_b";
    // delegate to $this->sql(), $this->select(), $this->from(), $this->where(), etc.
    return $this->sql($this->select($justIDs), $offset, $rowcount, $sort, $includeContactIDs, NULL);
  }

  /**
   * Construct a SQL SELECT clause
   *
   * @return string, sql fragment with SELECT arguments
   */
  public function select($justIDs) {
    if ($justIDs) {
      // NB until PR13531 is merged, the 'as' must be lowercase in this next statement.
      return "contact_a.id as contact_id";
    }
    else {
      return "
        contact_a.id           AS contact_id,
        contact_a.display_name AS display_name,
        contact_a.contact_type AS contact_type,
        contact_a.sort_name    AS sort_name,
        address.street_address AS street_address,
        address.city           AS city
    ";
    }
  }

  /**
   * Construct a SQL FROM clause
   *
   * @return string, sql fragment with FROM and JOIN clauses
   */
  public function from() {
    static $hhmember;
    // Do static lookups first to simplify real search
    if (!$hhmember) {
      $hhmember = civicrm_api3('RelationshipType', 'getvalue', [
        'return' => "id",
        'name_a_b' => "Household Member of",
      ]);
      $hhmember = CRM_Utils_Type::escape($hhmember, 'Int');
    }
    return "
      FROM civicrm_contact                      AS contact_a
      LEFT JOIN civicrm_address                 AS address
        ON (address.contact_id = contact_a.id
            AND address.is_primary = 1)
      INNER JOIN civicrm_relationship           AS reln
        ON (reln.contact_id_a = contact_a.id
            AND reln.relationship_type_id = $hhmember)
      INNER JOIN civicrm_entity_tag             AS tag
        ON (entity_id = contact_a.id)
    ";
  }

  /**
   * Construct a SQL WHERE clause
   *
   * @param bool $includeContactIDs
   * @return string, sql fragment with conditional expressions
   */
  public function where($includeContactIDs = FALSE) {	  
    static $tagid;
	if (!$tagid) {
      $tagid = civicrm_api3('Tag', 'getvalue', [
        'return' => "id",
        'name' => "Print Card",
      ]);
      $tagid = CRM_Utils_Type::escape($tagid, 'Int');
    }
    return "contact_a.is_deleted = 0 AND tag.tag_id = $tagid";
  }

  /**
   * Determine the Smarty template for the search screen
   *
   * @return string, template path (findable through Smarty template path)
   */
  public function templateFile() {
    return 'CRM/Contact/Form/Search/Custom.tpl';
  }

  /**
   * Modify the tasklist
   *
   * @FIXME seems kludgy - we add the task via hook_civicrm_searchTasks and then
   * filter based on the name.  Seems that we should be able to directly
   * specify the tasks.  The important bit is $key below.
   */
  public function buildTaskList($form) {
    $tasks = parent::buildTaskList($form);
    $newtasks = [];
    foreach ($tasks as $key => $title) {
      if ($title == 'LALG - Print Membership Cards') {
        $newTasks[$key] = $title;
      }
    }
    return $newTasks;
  }

}
