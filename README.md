# CamooPay plugin for PHP Application

## Installation

You can install this plugin into your PHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require camoo/camoo-pay
```

# Usage

```php
use CamooPay\Exception\CamooPayCashoutException;
use CamooPay\Lib\CashOut;

$cashOut = new CashOut('token', 'secret', 'orange');
$phoneNumber = '690000000';
$amount = 850;
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