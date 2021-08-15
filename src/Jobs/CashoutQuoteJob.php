<?php
declare(strict_types=1);

namespace CamooPay\Jobs;

use Maviance\S3PApiClient\Model\Quote;
use CamooPay\Services\CashoutApi;
use CamooPay\Services\CamooPayServiceLocatorTrait;
use Throwable;

class CashoutQuoteJob
{
    use CamooPayServiceLocatorTrait;

    private CashoutApi $cashoutApi;
    private const SERVICE_NAME = 'Cashout';
    private const MODEL_NAME = 'Quote';
    private string $token;
    private string $secret;

    public function __construct(string $token, string $secret)
    {
        $this->cashoutApi = $this->getCamooPayLocator()
            ->get(self::SERVICE_NAME, $token, $secret, self::MODEL_NAME);
        $this->token = $token;
        $this->secret = $secret;
    }

    public function handle(string $referenceId, string $paymentId, string $phoneNumber, float $amount, string $email): ?array
    {
        try {
            $result = $this->cashoutApi->requestQuote($amount, $paymentId);
            /** @var Quote $entity */
            $entity = $result->first();
            $quoteId = $entity->getQuoteId();

            $collector = new CashoutChargeJob($this->token, $this->secret);
            $chargeResult = $collector->handle($referenceId, $quoteId, $phoneNumber, $email);
        } catch (Throwable $exception) {
            echo $exception->getMessage();
            return null;
        }
        return $chargeResult;
    }
}
