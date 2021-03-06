<?php

class CRM_Booking_BAO_AdhocCharges extends CRM_Booking_DAO_AdhocCharges {


    /**
   * takes an associative array and creates a adhoc charges object
   *
   * the function extract all the params it needs to initialize the create a
   * resource object. the params array could contain additional unused name/value
   * pairs
   *
   * @param array $params (reference ) an assoc array of name/value pairs
   * @param array $ids    the array that holds all the db ids
   *
   * @return object CRM_Booking_BAO_AdhocCharges object
   * @access public
   * @static
   */
  static function create(&$params) {
    $dao = new CRM_Booking_DAO_AdhocCharges();
    $dao->copyValues($params);
    return $dao->save();
  }


  static function getBookingAdhocCharges($bookingID){
    $params = array(1 => array( $bookingID, 'Integer'));

    $query = "
      SELECT civicrm_booking_adhoc_charges.id,
             civicrm_booking_adhoc_charges.booking_id,
             civicrm_booking_adhoc_charges.item_id,
             civicrm_booking_adhoc_charges.quantity
      FROM civicrm_booking_adhoc_charges
      WHERE 1
      AND  civicrm_booking_adhoc_charges.booking_id = %1";

    $charges = array();
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    while ($dao->fetch()) {
      $charges[$dao->id] = array(
        'id' => $dao->id,
        'booking_id' => $dao->booking_id,
        'item_id' => $dao->item_id,
        'quantity' => $dao->quantity,
      );
    }
    return $charges;
  }


}
