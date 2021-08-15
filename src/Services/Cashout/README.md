# Execute payment collection
This Class executes a payment collection. Any collection will reduce the agent balance by service amount plus the service fee.

# Usage

```php
use CamooPay\Exception\CamooPayCashoutException;
use CamooPay\Lib\CashOut;

$cashOut = new CashOut('token', 'secret', 'mtn');
$phoneNumber = '670000000';
$amount = 700;
$customerEmail = 'end-customer@email.cm';
        try {
            $payment $cashOut->charge($phoneNumber, $amount, $customerEmail);

        } catch (CamooPayCashoutException $exception) {
            $message = $exception->getMessage();
            echo $message
        }
        
        var_dump($payment);

// Now verify the status
$transactionNumber = $payment['ptn'];
$checkPayment = new \CamooPay\Lib\CheckPayment('token', 'secret');
$verify = $checkPayment->check($transactionNumber);
var_dump($verify);


```
