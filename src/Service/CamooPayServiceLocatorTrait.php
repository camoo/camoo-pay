<?php
declare(strict_types=1);

namespace CamooPay\Service;


/**
 * Trait ModelLocatorTrait
 * @author CamooSarl
 */
trait CamooPayServiceLocatorTrait
{
    /**
     * gets adapter factory
     *
     * @return ServiceFactory
     */
    public function getCamooPayLocator(): ServiceFactory
    {
        return ServiceFactory::create();
    }
}