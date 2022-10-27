<?php

declare(strict_types=1);

namespace CamooPay\Jobs;

use CamooPay\Services\CamooPayServiceLocatorTrait;
use CamooPay\Services\Cashout\CashoutApi;
use Maviance\S3PApiClient\Model\Quote;
use Throwable;

final class BillQuoteJob
{
    use CamooPayServiceLocatorTrait;

    private const SERVICE_NAME = 'Cashout';

    private const MODEL_NAME = 'Quote';

    private CashoutApi $cashoutApi;

    private string $token;

    private string $secret;

    public function __construct(string $token, string $secret)
    {
        $this->cashoutApi = $this->getCamooPayLocator()
            ->get(self::SERVICE_NAME, $token, $secret, self::MODEL_NAME);
        $this->token = $token;
        $this->secret = $secret;
    }

    public function handle(
        string $referenceId,
        string $paymentId,
        string $phoneNumber,
        float $amount,
        string $email,
        string $serviceNumber
    ): ?array {
        try {
            $result = $this->cashoutApi->requestQuote($amount, $paymentId);
            /** @var Quote $entity */
            $entity = $result->first();
            $quoteId = $entity->getQuoteId();

            $collector = new PayBillJob($this->token, $this->secret);
            $chargeResult = $collector->handle($referenceId, $quoteId, $phoneNumber, $email, $serviceNumber);
        } catch (Throwable $exception) {
            echo $exception->getMessage();

            return null;
        }

        return $chargeResult;
    }
}
