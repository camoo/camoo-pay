<?php

declare(strict_types=1);

namespace CamooPay\Jobs;

use CamooPay\Services\CamooPayServiceLocatorTrait;
use CamooPay\Services\CashIn\CashinApi;
use Maviance\S3PApiClient\Model\Quote;
use Throwable;

class CashInQuoteJob
{
    use CamooPayServiceLocatorTrait;

    private const SERVICE_NAME = 'Cashin';

    private const MODEL_NAME = 'Quote';

    private CashinApi $cashInApi;

    public function __construct(private string $token, private string $secret)
    {
        $this->cashInApi = $this->getCamooPayLocator()
            ->get(self::SERVICE_NAME, $token, $secret, self::MODEL_NAME);
    }

    public function handle(
        string $referenceId,
        string $paymentId,
        string $phoneNumber,
        float $amount,
        string $email
    ): ?array {
        try {
            $result = $this->cashInApi->requestQuote($amount, $paymentId);
            /** @var Quote $entity */
            $entity = $result->first();
            $quoteId = $entity->getQuoteId();

            $collector = new CashInChargeJob($this->token, $this->secret);
            $chargeResult = $collector->handle($referenceId, $quoteId, $phoneNumber, $email);
        } catch (Throwable $exception) {
            echo $exception->getMessage();

            return null;
        }

        return $chargeResult;
    }
}
