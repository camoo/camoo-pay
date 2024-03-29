<?php

declare(strict_types=1);

namespace CamooPay\Lib;

use CamooPay\Exception\CamooPayBillException;
use CamooPay\Http\ResponseInterface;
use CamooPay\Jobs\BillQuoteJob;
use CamooPay\Services\Bill\BillApi;
use CamooPay\Services\CamooPayServiceLocatorTrait;
use Maviance\S3PApiClient\Model\Bill as BillEntity;
use Maviance\S3PApiClient\Model\CollectionResponse;

final class Bill
{
    use CamooPayServiceLocatorTrait;

    private const SERVICE_NAME = 'Bill';

    private BillApi $billApi;

    private string $token;

    private string $secret;

    public function __construct(string $token, string $secret)
    {
        $this->billApi = $this->getCamooPayLocator()->get(self::SERVICE_NAME, $token, $secret);
        $this->token = $token;
        $this->secret = $secret;
    }

    public function pay(BillEntity $bill, string $referenceId, string $phoneNumber, string $email): ?CollectionResponse
    {
        $paymentId = $bill->getPayItemId();
        $serviceNumber = $bill->getServiceNumber();
        $amount = $bill->getAmountLocalCur() + $bill->getPenaltyAmount();

        if ($paymentId === null) {
            throw new CamooPayBillException('Payment Id could not be not retrieved !');
        }

        return (new BillQuoteJob($this->token, $this->secret))
            ->handle($referenceId, $paymentId, $phoneNumber, $amount, $email, $serviceNumber);
    }

    public function getHandler(string $serviceNumber, string $merchant, int $serviceId): ResponseInterface
    {
        return $this->billApi->get($serviceNumber, $merchant, $serviceId);
    }
}
