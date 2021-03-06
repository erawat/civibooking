<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.4                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2013                                |
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
 * @copyright CiviCRM LLC (c) 2004-2013
 *
 */

/**
 * This class generates form components for Resource
 *
 */
class CRM_Admin_Form_Preferences_Booking extends CRM_Core_Form {
  protected $_config = NULL;

  function preProcess() {
    parent::preProcess();
    CRM_Utils_System::setTitle(ts('Settings - Booking Preferences Configuration'));

	$configValue = CRM_Booking_BAO_BookingConfig::getConfig();
	$this->_config = $configValue;

    // load up javascript, css
    self::registerScripts();
  }

  /**
   * Function to build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm($check = FALSE) {
    parent::buildQuickForm();

    $timeRange =  CRM_Booking_Utils_DateTime::createTimeRange("00:00", "24:00");
    $timeOptions = array();
    foreach ($timeRange as $key => $time) {
      $option = date('G:i', $time);
      $timeOptions[$option] = $option;
    }

    $this->add('select', 'day_start_at', ts('Day starts at'),
      $timeOptions ,
      FALSE,
      array()
    );

    $this->add('select', 'day_end_at', ts('Day ends at'),
      $timeOptions,
      FALSE,
      array()
    );

    $this->add('select', 'time_period', ts('Time period'),
      array(10 => '10', 15 => '15', 20 => '20', 30 => '30', 60 => '60'),
      FALSE,
      array()
    );

    $this->add('text', 'cc_email_address', ts('CC'), array('size' => 50, 'maxlength' => 255), FALSE);
    $this->add('text', 'bcc_email_address', ts('BCC'), array('size' => 50, 'maxlength' => 255), FALSE);
    $this->add('checkbox', 'log_confirmation_email', ts('Log email?'));
    $this->add('text', 'slot_new_colour', ts('New Slot Colour'));
    $this->add('text', 'slot_being_edited_colour', ts('Slot Editing Colour'));
    $this->add('text', 'slot_booked_colour', ts('Booked Slot Colour'));
    $this->add('text', 'slot_provisional_colour', ts('Provisional Slot Colour'));


    $this->addFormRule(array('CRM_Admin_Form_Preferences_Booking', 'formRule'), $this);

    $this->addButtons(
      array(
        array(
          'type' => 'next',
          'name' => ts('Save'),
          'isDefault' => TRUE,
        ),
      )
    );
  }

  static function formRule($fields) {
    if (!empty($errors)) {
      return $errors;
    }

    return empty($errors) ? TRUE : $errors;
  }

  function setDefaultValues() {
    $defaults = array();
	  $defaults['day_start_at'] = date('G:i', strtotime($this->_config['day_start_at']));
	  $defaults['day_end_at'] = date('G:i', strtotime($this->_config['day_end_at']));
    $defaults['time_period'] = $this->_config['time_period'];
    $defaults['cc_email_address'] = CRM_Utils_Array::value('cc_email_address', $this->_config);
	  $defaults['bcc_email_address'] = CRM_Utils_Array::value('bcc_email_address', $this->_config);
    $defaults['log_confirmation_email'] = $this->_config['log_confirmation_email'];
    $defaults['slot_booked_colour'] = $this->_config['slot_booked_colour'];
    $defaults['slot_provisional_colour'] = $this->_config['slot_provisional_colour'];
    $defaults['slot_being_edited_colour'] = $this->_config['slot_being_edited_colour'];
    $defaults['slot_new_colour'] = $this->_config['slot_new_colour'];

    return $defaults;
  }


  /**
   * Function to process the form
   *
   * @access public
   *
   * @return None
   */
  public function postProcess() {
    CRM_Utils_System::flushCache();

	  // get values from form
	  $params = $this->exportValues();
	  $params['id'] = $this->_config['id'];
	  $params['day_start_at'] = date('His', strtotime($params['day_start_at']));
	  $params['day_end_at'] = date('His', strtotime($params['day_end_at']));
	  if(!isset($params['log_confirmation_email'])){
      $params['log_confirmation_email'] = 0;
    }

	  // submit to BAO for updating
	  $set = CRM_Booking_BAO_BookingConfig::create($params);

	  $url = CRM_Utils_System::url('civicrm/admin/setting/preferences/booking', 'reset=1');
	  // show message
	  CRM_Core_Session::setStatus(ts('The Booking configuration has been saved.'), ts('Saved'), 'success');
    $session = CRM_Core_Session::singleton();
    $session->replaceUserContext($url);
  }


  static function registerScripts() {
    static $loaded = FALSE;
    if ($loaded) {
      return;
    }
    $loaded = TRUE;

    CRM_Core_Resources::singleton()
      ->addStyleFile('uk.co.compucorp.civicrm.booking', 'css/booking.css', 92, 'page-header')
      ->addStyleFile('uk.co.compucorp.civicrm.booking', 'js/vendor/bgrins-spectrum/spectrum.css',93,'page-header')
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/vendor/bgrins-spectrum/spectrum.js',93,'page-header');
  }


}
