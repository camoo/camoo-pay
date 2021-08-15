# Get the current payment collection status
Use this Class to retrieve the current payment status by either transaction number (PTN) 

# Usage

```php
$transactionNumber = '641de51a-119a-1893-ccm1-61a2636e833';
$checkPayment = new \CamooPay\Lib\CheckPayment('token', 'secret');
$verify = $checkPayment->check($transactionNumber);
if ($verify['status'] === 'SUCCESS') {
  // handle Success
}

if ($verify['status'] === 'ERRORED') {
   // handle error
}
```