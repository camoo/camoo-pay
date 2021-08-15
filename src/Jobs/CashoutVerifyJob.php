<?php
declare(strict_types=1);

namespace CamooPay\Jobs;

use CamooPay\Services\CamooPayServiceLocatorTrait;
use CamooPay\Services\Verify\VerifyApi;

/**
 * This class is not completed. Status Handling should be done in the Backend according to the App Structure.
 * @author CamooSarl
 * @license MIT
 */
final class CashoutVerifyJob
{
    use CamooPayServiceLocatorTrait;

    private VerifyApi $verifyApi;
    private const SERVICE_NAME = 'Cashout';
    private const MODEL_NAME = 'object';

    /**
     * Payment was properly processed and confirmed by service provider
     */
    private const GOOD_STATUS = 'success';
    /**
     * Payment has failed
     */
    private const BAD_STATUS = 'errored';
    /**
     * Payment has been sent to service provider, but confirmation has not yet been received. This is the default status.
     */
    private const WAITING_STATUS = 'pending';

    /**
     * Payment has been reversed (will only occur during reconciliation process)
     */
    private const RESERVED_STATUS = 'reserved';

    public function __construct(string $token, string $secret)
    {
        $this->verifyApi = $this->getCamooPayLocator()->get(self::SERVICE_NAME, $token, $secret, self::MODEL_NAME);
    }

    public function handle(string $transactionNumber): ?array
    {
        $result = $this->verifyApi->verify($transactionNumber);
        return $result->get(0);
    }
}
