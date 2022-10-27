<?php

declare(strict_types=1);

namespace CamooPay\Lib;

use Cake\Utility\Text;
use CamooPay\Exception\CamooPayCashoutException;
use CamooPay\Http\ResponseInterface;
use CamooPay\Jobs\BillQuoteJob;
use CamooPay\Services\Bill\BillApi;
use CamooPay\Services\CamooPayServiceLocatorTrait;

final class Bill
{
    use CamooPayServiceLocatorTrait;

    private const SERVICE_NAME = 'Bill';

    private BillApi $billApi;

    private string $country;

    private string $token;

    private string $secret;

    public function __construct(string $token, string $secret, string $country = 'CM')
    {
        $this->billApi = $this->getCamooPayLocator()->get(self::SERVICE_NAME, $token, $secret);
        $this->country = $country;
        $this->token = $token;
        $this->secret = $secret;
    }

    public function pay(string $serviceNumber, string $merchant, int $serviceId, float $amount, string $email): ?array
    {
        $handler = $this->getHandler($serviceNumber, $merchant, $serviceId);

        /** @var \Maviance\S3PApiClient\Model\Bill|null $details */
        $details = $handler->first();
        if (null === $details) {
            return null;
        }
        $paymentId = $details->getPayItemId();
        $referenceId = Text::uuid();

        if ($paymentId === null) {
            throw new CamooPayCashoutException('Payment Id could not be not retrieved !');
        }

        return (new BillQuoteJob($this->token, $this->secret))
            ->handle($referenceId, $paymentId, $phoneNumber, $amount, $email);
    }

    private function getHandler(string $serviceNumber, string $merchant, int $serviceId): ResponseInterface
    {
        return $this->billApi->get($serviceNumber, $merchant, $serviceId);
    }
}
