<?php

namespace IQnection\Payment;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms;
use SilverStripe\Core\ClassInfo;

class SiteConfig extends DataExtension
{
	private static $db = array(
		'EnabledPaymentMethods' => 'Text'
	);
	
	public function updateCMSFields(Forms\FieldList $fields)
	{
		$paymentMethods = array();
		foreach(ClassInfo::subclassesFor(Payment::class) as $paymentClass)
		{
			if ($paymentClass == Payment::class) { continue; }
			$paymentName = $paymentClass::Config()->get('PaymentMethod');
			$paymentMethods[$paymentClass] = Forms\FormField::name_to_label($paymentName);
		}
		$fields->addFieldToTab('Root.Payments', Forms\CheckboxSetField::create('EnabledPaymentMethods','Enable Payment Methods')
			->setSource($paymentMethods) );
		$fields->addFieldToTab('Root.Payments', Forms\Tabset::create('Methods') );
	}
}