* Retrieve account information and remaining account balance
* This endpoint returns the user’s account information – most notably the current balance of the user.
* Calling this service before and after each collection in order to retrieve the current limits and/or
* balance is highly discouraged. The recommended approach is as follows:
* 
* Only a successful payment collection transaction will affect the account balance.
* The corresponding endpoint will also return the current account balance after the collection in its result payload.
* For unsuccessful payment transactions, the account balance will not be affected.
* The error message returns a verbose message as to why the transaction failed.
* There is no need to recheck the account after each error.

# Usage

```php

$account = new \CamooPay\Lib\Account('token', 'secret');

$response = $account->read();

// get S3P Model
/** @var \Maviance\S3PApiClient\Model\Account $accountModel */
$accountModel = $response->firstOrFail();
$balance = $accountModel->getBalance();
$remainingLimit = $accountModel->getLimitRemaining();

// or as Array
$accountData = $response->toArray();

```