<?php

namespace App\Repositories;

interface QuickTellerAPIInterface
{
    /**
     * Retrives a list of all categories
     */
    public function getCategories();

    /**
     * Retrieves a list of all billers
     */
    public function getBillers();

    /**
     * Retrieves a list of all payment items
     */
    public function getPaymentItems($billerId);
}
