<?php

namespace IQnection\Payment;

use IQnection\Payment\Payment as PaymentModel;
use IQnection\AuthnetPayment\Client as AuthnetClient;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms;

class CreditCardPayment extends PaymentModel
{
	private static $table_name = 'CreditCardPayment';
	
	private static $PaymentMethod = 'Credit Card Payment';
	
	private static $payment_processor;
	
	private static $db = [
		'TransactionId' => 'Varchar(255)',
		'AuthorizationCode' => 'Varchar(50)',
		'Message' => 'Text',
		'Response' => 'Text'
	];
	
	public function getCMSFields()
	{
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Response', Forms\LiteralField::create('Response','<pre>'.print_r(unserialize($this->Response),1).'</pre>'));
		return $fields;
	}
	
	public function Process($data = array())
	{
		$client = $this->getPaymentProcessor();
		return $client->Process($data, $this);
	}
	
	public function getPaymentProcessor()
	{
		if ( (!$paymentProcessor = $this->Config()->get('payment_processor')) || (!class_exists($paymentProcessor)) )
		{
			user_error('Payment processor class "'.$paymentProcessor.'" does not exist');
		}
		$client = Injector::inst()->create($paymentProcessor);
		return $client;
	}
}