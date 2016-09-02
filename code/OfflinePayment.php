<?php


class OfflinePayment extends Payment
{
	private static $PaymentMethod = 'Offline Payment';
		
	public function Process($arg = array())
	{
		$this->Status = 'Success';
		$this->write();
		return parent::Process();
	}
}
