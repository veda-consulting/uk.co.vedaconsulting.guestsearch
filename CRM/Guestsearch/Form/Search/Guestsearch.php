<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2015                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2015
 * $Id$
 *
 */
class CRM_Guestsearch_Form_Search_Guestsearch extends CRM_Contact_Form_Search_Custom_Base implements CRM_Contact_Form_Search_Interface {
  /**
   * @param $formValues
   */
  public function __construct(&$formValues) {
    parent::__construct($formValues);

    if (!isset($formValues['state_province_id'])) {
      $this->_stateID = CRM_Utils_Request::retrieve('stateID', 'Integer',
        CRM_Core_DAO::$_nullObject
      );
      if ($this->_stateID) {
        $formValues['state_province_id'] = $this->_stateID;
      }
    }

    $this->_columns = array(
      ts('Contact ID') => 'contact_id',
      ts('Contact Type') => 'contact_type',
      ts('Name') => 'sort_name',
      ts('State') => 'state_province',
    );
  }

  /**
   * @param CRM_Core_Form $form
   */
  public function buildForm(&$form) {

    CRM_Utils_System::setTitle(ts('My Search Title'));


    $form->add('text', 'name', ts('Name'), TRUE
    );

    $form->setDefaults(array(
      'name' => '',
    ));

    /**
     * if you are using the standard template, this array tells the template what elements
     * are part of the search criteria
     */
    $form->assign('elements', array('name'));

    /**
     * You can define a custom title for the search form
     */
   // $this->setTitle('My Search Title');

    /**
     * if you are using the standard template, this array tells the template what elements
     * are part of the search criteria
     */
   // $form->assign('elements', array('household_name', 'state_province_id'));
  }

  /**
   * @return array
   */
  function &columns() {
    // return by reference
    $columns = array(
      ts('Name') => 'sort_name',
      ts('Phone') => 'phone',
      ts('Email') => 'email',
      ts('Date of visit') => CIVICRM_GUESTSEARCH_CUSTOM_COLUMN_NAME,
    );
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
    return $this->all($offset, $rowcount, null, FALSE, TRUE);
  }

  /**
   * @param int $offset
   * @param int $rowcount
   * @param null $sort
   * @param bool $includeContactIDs
   * @param bool $justIDs
   *
   * @return string
   */
  public function all($offset = 0, $rowcount = 0, $sort = NULL, $includeContactIDs = FALSE, $justIDs = FALSE) {
    if ($justIDs) {
      $selectClause = "contact_a.id as contact_id";
      $sort = 'contact_a.id';
    }
    else {
      $selectClause = "
      contact_a.id                  as contact_id  ,
      contact_a.contact_sub_type    as contact_sub_type,
      contact_a.sort_name           as sort_name,
      phone.phone ,
      email.email  ,
      date.
    " . CIVICRM_GUESTSEARCH_CUSTOM_COLUMN_NAME;
    }

    return $this->sql($selectClause,
      $offset, $rowcount, NULL,
      $includeContactIDs, NULL
    );
  }

  /**
   * @return string
   */

  /**
   * @param bool $includeContactIDs
   *
   * @return string
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
  function where($includeContactIDs = false) {
    $params = array();
    $where = "contact_a.contact_sub_type = 'Guest' AND activity_id IS NULL ORDER BY date." . CIVICRM_GUESTSEARCH_CUSTOM_COLUMN_NAME . " ASC ";
    return $where;
  }

  /**
   * @return string
   */
  public function templateFile() {
    return 'CRM/Contact/Form/Search/Custom.tpl';
  }

  /**
   * @return array
   */
  public function setDefaultValues() {
    /*return array(
      'household_name' => '',
    );*/
  }

  /**
   * @param $row
   */
  public function alterRow(&$row) {
    //$row['sort_name'] .= ' ( altered )';
  }

  /**
   * @param $title
   */
  public function setTitle($title) {
    /*if ($title) {
      CRM_Utils_System::setTitle($title);
    }
    else {
      CRM_Utils_System::setTitle(ts('Search'));
    }*/
  }
}
