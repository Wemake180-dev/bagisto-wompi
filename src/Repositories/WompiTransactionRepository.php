<?php

namespace Webkul\Wompi\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Wompi\Contracts\WompiTransaction;

class WompiTransactionRepository extends Repository
{
    /**
     * Specify Model class name.
     */
    public function model(): string
    {
        return WompiTransaction::class;
    }

    /**
     * Find a transaction by Wompi transaction ID.
     */
    public function findByWompiId(string $wompiId)
    {
        return $this->findOneWhere(['wompi_transaction_id' => $wompiId]);
    }

    /**
     * Create a new Wompi transaction record.
     */
    public function createTransaction(array $data)
    {
        return $this->create($data);
    }

    /**
     * Update transaction status.
     */
    public function updateTransactionStatus(int $id, string $status, array $additionalData = [])
    {
        $transaction = $this->find($id);

        if ($transaction) {
            $updateData = ['status' => $status];

            if (! empty($additionalData)) {
                $updateData['response_data'] = array_merge(
                    $transaction->response_data ?? [],
                    $additionalData
                );
            }

            return $this->update($updateData, $id);
        }

        return false;
    }

    /**
     * Get transactions by order ID.
     */
    public function getByOrderId(int $orderId)
    {
        return $this->findWhere(['order_id' => $orderId]);
    }

    /**
     * Get pending transactions for status update.
     */
    public function getPendingTransactions()
    {
        return $this->findWhere(['status' => 'PENDING']);
    }
}
