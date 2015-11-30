<?php

/**
 * A custom contact search
 */
class CRM_Guestsearch_Form_Search_Guestsearch extends CRM_Contact_Form_Search_Custom_Base implements CRM_Contact_Form_Search_Interface {

  function __construct(&$formValues) {
    parent::__construct($formValues);
  }

  /**
   * Prepare a set of search fields
   *
   * @param CRM_Core_Form $form modifiable
   * @return void
   */
  function buildForm(&$form) {
    CRM_Utils_System::setTitle(ts('Recently Registered Guest (Unassigned)'));


/*    $form->add('text', 'name', ts('Name'), TRUE
    );

    $form->setDefaults(array(
      'name' => '',
    ));
*/
    /**
     * if you are using the standard template, this array tells the template what elements
     * are part of the search criteria
     */
    $form->assign('elements', array('name'));
  }

  /**
   * Get a list of summary data points
   *
   * @return mixed; NULL or array with keys:
   *  - summary: string
   *  - total: numeric
   */
  function summary() {
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
  function &columns() {
    // return by reference
    $columns = array(
      ts('Contact Type') => 'contact_sub_type',
      ts('Name') => 'sort_name',
      ts('Phone') => 'phone',
      ts('Email') => 'email',
      ts('Date of visit') => CIVICRM_GUESTSEARCH_CUSTOM_COLUMN_NAME,
    );
    return $columns;
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
  function all($offset = 0, $rowcount = 0, $sort = NULL, $includeContactIDs = FALSE, $justIDs = FALSE) {
    // SELECT clause must include contact_id as an alias for civicrm_contact.id
    $sort = "date." . CIVICRM_GUESTSEARCH_CUSTOM_COLUMN_NAME;
    return $this->sql($this->select(), $offset, $rowcount, $sort, $includeContactIDs, NULL);
  }

  /**
   * Construct a SQL SELECT clause
   *
   * @return string, sql fragment with SELECT arguments
   */
  function select() {
    return "
      contact_a.id                  as contact_id  ,
      contact_a.contact_sub_type    as contact_sub_type,
      contact_a.sort_name           as sort_name,
      phone.phone ,
      email.email  ,
      date.
    " . CIVICRM_GUESTSEARCH_CUSTOM_COLUMN_NAME;
  }

  /**
   * Construct a SQL FROM clause
   *
   * @return string, sql fragment with FROM and JOIN clauses
   */
  function from() {
    return "
      FROM      civicrm_contact contact_a
      LEFT JOIN civicrm_activity_contact a ON (a.contact_id = contact_a.id)
      LEFT JOIN civicrm_phone phone ON (phone.contact_id = contact_a.id)
      LEFT JOIN civicrm_email email ON (email.contact_id = contact_a.id)
      LEFT JOIN " . CIVICRM_GUESTSEARCH_CUSTOM_TABLE_NAME . " date ON (date.entity_id = contact_a.id)
    ";
  }

  /**
   * Construct a SQL WHERE clause
   *
   * @param bool $includeContactIDs
   * @return string, sql fragment with conditional expressions
   */
  function where($includeContactIDs = false) {
    $params = array();

    $where = "contact_a.contact_sub_type = 'Guest' AND activity_id IS NULL";

    return $where;

  }

  /**
   * Determine the Smarty template for the search screen
   *
   * @return string, template path (findable through Smarty template path)
   */
  function templateFile() {
    return 'CRM/Contact/Form/Search/Custom.tpl';
  }

  /**
   * Modify the content of each row
   *
   * @param array $row modifiable SQL result row
   * @return void
   */

}
