<?php


namespace IQnection\Payment;

use SilverStripe\ORM;
use SilverStripe\Forms;

/**
 * Example of adding payment fields to checkout form
 $paymentMethod = singleton('PayeezyCreditCardPayment');
foreach($paymentMethod->PaymentFields() as $paymentField)
{
	$fields->push( $paymentField );
}
*/

/**
 * Example of processing payment in form handler
 // validate the payment submission
$inValid = $Payment->ValidateSubmission($data);
if (count($inValid))
{
	foreach($inValid as $fieldName => $message)
	{
		$form->addErrorMessage($fieldName,$message,'bad');
	}
	return $this->redirectback();
}
		
// process the payment
$Payment->Amount = $Booking->FinalPrice();
$Payment->Process($data);
switch($Payment->Status)
{
	default:
	case 'Pending':
	{
		user_error('Pending status has not been setup yet');
		return $this;
		break;
	}
	case 'Failed':
	{
		$form->sessionMessage('There was an error processing your payment, "'.$Payment->Message.'". If the problem persists, please contact the site administrator','bad');
		return $this->redirectback();
		break;
	}
	case 'Declined':
	{
		$form->sessionMessage("Your payment was declined, please check with your bank and try again. Message:".$Payment->Message,'bad');
		return $this->redirectback();
		break;
	}
	case 'Success':
	{
		$Booking->PaymentID = $Payment->ID;
		$Booking->BookingPageID = $this->ID;
		$Booking->Complete();				
		Session::set('Form_PaymentForm.data',false);
		return $this->redirect($this->Link('thanks/'.$Booking->HashedID()));
		break;
	}
}
*/

class Payment extends ORM\DataObject
{
	private static $table_name = 'Payment';
	
	private static $PaymentMethod = 'Not Specified';
	
	private static $db = array( 
		"Amount" => "Currency",
		"Status" => "Enum('Pending,Failed,Declined,Success','Pending')",
		"PaidObjectID" => "Int",
		"PaidObjectType" => "Varchar(255)",
		'Log' => 'Text'
	);
	
	private static $has_one = [
		'Page' => \Page::class
	];
		
	private static $summary_fields = array(
		'Created.Nice' => 'Date',
		'Amount' => 'Price'
	);
	
	private static $default_sort = 'Created DESC';
	
	public function getCMSFields()
	{
		$fields = parent::getCMSFields();
		$fields->removeByName(['LinkTracking','FileTracking','Log']);
		$fields->addFieldToTab('Root.Log', Forms\LiteralField::create('log-display','<div style="max-width:1000px"><pre><xmp>'.print_r(json_decode($this->Log,1),1).'</xmp></pre></div>') );		
		$this->extend('updateCMSFields',$fields);
		return $fields;
	}
	
	public function AddLogEntry($message)
	{
		$log = json_decode($this->Log,1);
		if (!$log)
		{
			$log = [];
		}
		$log[strtotime('now')][] = $message;
		$this->Log = json_encode($log);
	}
	
	public function getPaidObject()
	{
		if ( ($objType = $this->PaidObjectType) && ($objID = $this->PaidObjectID) && (class_exists($objType)) )
		{
			return $objType::get()->byId($objID);
		}
	}
	
	public function setPaidObject($object)
	{
		$this->PaidObjectType = get_class($object);
		$this->PaidObjectID = $object->ID;
		$this->write();
		return $this;
	}
	
	/**
	 * Builds the form fields needed for payment
	 * fields should be returned in an array to ba parsed and added to the page's form
	 * @returns array
	 */
	public function PaymentFields($defaults=array())
	{
		return array();
	}
	
	/**
	 * @returns array of required field names
	 */
	public function PaymentRequiredFields()
	{
		return array();
	}
	
	/**
	 * checks the submission and makes sure all values are good before processing the payment
	 * this method should be called from the form handler.
	 * any array values returned are invalid fields
	 * an empty array means the submission is valid
	 * @param $data - submitted form data
	 * @returns array
	 */
	public function ValidateSubmission($data = array())
	{
		return array();
	}
	
	/**
	 * 
	 * the process method should validate the payment and process it with any 3rd party
	 * upon success, write the payment object to the database 
	 * @param $args array - data passed to process the payment
	 * @returns object Payment $this
	 */
	public function Process($args=array())
	{
		return $this;
	}
	
	public function PaymentMethodTitle()
	{
		return $this->config()->PaymentMethod;
	}
	
	public function OnSuccessfulPayment()
	{
		$this->extend('updateOnSuccessfulPayment');
		return $this;
	}
	
	public function canCreate($member = null,$context=[]) { return false; }
	public function canDelete($member = null,$context=[]) { return true; }
	public function canEdit($member = null,$context=[])   { return false; }
	public function canView($member = null,$context=[])   { return true; }
	
	public function Method()
	{
		return $this->PaymentMethod;
	}
}

	
	