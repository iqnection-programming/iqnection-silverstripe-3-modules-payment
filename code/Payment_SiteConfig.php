<?php

class Payment_SiteConfig extends DataExtension
{
	private static $db = array(
		'EnabledPaymentMethods' => 'Text'
	);
	
	public function updateCMSFields(&$fields)
	{
		$paymentMethods = array();
		foreach(ClassInfo::subclassesFor('Payment') as $paymentClass)
		{
			if ($paymentClass == 'Payment') { continue; }
			$paymentMethods[$paymentClass] = FormField::name_to_label($paymentClass);
		}
		$fields->addFieldToTab('Root.Payments', new CheckboxSetField('EnabledPaymentMethods','Enable Payment Methods',$paymentMethods) );
		$fields->addFieldToTab('Root.Payments', TabSet::create('Methods') );
	}
}