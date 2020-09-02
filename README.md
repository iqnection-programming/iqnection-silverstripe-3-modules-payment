# Payment module

This is a base class to be extended with specific Payment types or gateways. 

9/2/2020 - Added CreditCardPayment base class
To call the standard ```Process``` method, you will need to provide and set the payment processor class
```
IQnection\Payment\CreditCardPayment:
  payment_processor: 'MyPaymentProcessorClass'
```