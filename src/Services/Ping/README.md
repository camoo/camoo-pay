* Check on the availability of the api.
* This endpoint simply checks the existence and validity of the request on the server by returning a valid
* response object or an error message. Its primary purpose is to provide a feedback on whether the API is available.
* It also provides the current server time and timezone

# Usage

```php

$healthCheck = new \CamooPay\Lib\Ping('token', 'secret');

$response = $healthCheck->check();

// get S3P Model
/** @var \Maviance\S3PApiClient\Model\Ping $pingModel */
$pingModel = $response->firstOrFail();
$version = $pingModel->getVersion();
$isAlive = $version === \CamooPay\Constant\Config::API_VERSION;
if($isAlive === false){
    // Handle server response
}

// Get server time
$time = $pingModel->getTime();
// or as Array
$pingData = $response->toArray();

```
