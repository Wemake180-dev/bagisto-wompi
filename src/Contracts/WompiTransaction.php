<?php

namespace Webkul\Wompi\Contracts;

interface WompiTransaction
{
    /**
     * Find a transaction by Wompi transaction ID.
     */
    public function findByWompiId(string $wompiId);

    /**
     * Create a new Wompi transaction record.
     */
    public function createTransaction(array $data);

    /**
     * Update transaction status.
     */
    public function updateStatus(int $id, string $status, array $additionalData = []);

    /**
     * Get transactions by order ID.
     */
    public function getByOrderId(int $orderId);
}
