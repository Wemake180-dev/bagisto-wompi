<?php

namespace Webkul\Wompi\Listeners;

use Illuminate\Support\Facades\Log;
use Webkul\Sales\Models\Order as OrderModel;
use Webkul\Wompi\Payment\Wompi;
use Webkul\Wompi\Repositories\WompiTransactionRepository;

class Order
{
    /**
     * Wompi transaction repository.
     */
    protected $transactionRepository;

    /**
     * Wompi payment instance.
     */
    protected $wompiPayment;

    public function __construct(
        WompiTransactionRepository $transactionRepository,
        Wompi $wompiPayment
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->wompiPayment = $wompiPayment;
    }

    /**
     * Update transaction with order ID after order is created.
     */
    public function updateTransactionOrderId(OrderModel $order): void
    {
        try {
            // Check if this order uses Wompi payment method
            if (
                $order->payment &&
                $order->payment->method === 'wompi'
            ) {
                // Find pending transaction for this cart/customer
                $transactions = $this->transactionRepository->model()
                    ->where('customer_email', $order->customer_email)
                    ->where('order_id', null)
                    ->where('status', 'PENDING')
                    ->where('created_at', '>=', now()->subHours(1)) // Within last hour
                    ->get();

                foreach ($transactions as $transaction) {
                    // Update the transaction with the order ID
                    $this->transactionRepository->update([
                        'order_id' => $order->id,
                    ], $transaction->id);

                    Log::info('Wompi: Updated transaction with order ID', [
                        'transaction_id' => $transaction->wompi_transaction_id,
                        'order_id'       => $order->id,
                    ]);

                    break; // Only update the first matching transaction
                }
            }
        } catch (\Exception $e) {
            Log::error('Wompi: Error updating transaction order ID', [
                'order_id' => $order->id,
                'message'  => $e->getMessage(),
            ]);
        }
    }

    /**
     * Process payment after order payment is saved.
     */
    public function processPayment($payment): void
    {
        try {
            if ($payment->method === 'wompi') {
                $order = $payment->order;

                // Get transaction for this order
                $transaction = $this->transactionRepository->findOneWhere([
                    'order_id' => $order->id,
                ]);

                if ($transaction) {
                    // Check current status from Wompi API
                    $statusData = $this->wompiPayment->getTransactionStatus(
                        $transaction->wompi_transaction_id
                    );

                    if ($statusData && isset($statusData['data']['status'])) {
                        $currentStatus = $statusData['data']['status'];

                        // Update transaction status if changed
                        if ($transaction->status !== $currentStatus) {
                            $this->transactionRepository->updateTransactionStatus(
                                $transaction->id,
                                $currentStatus,
                                $statusData
                            );

                            Log::info('Wompi: Updated transaction status', [
                                'transaction_id' => $transaction->wompi_transaction_id,
                                'old_status'     => $transaction->status,
                                'new_status'     => $currentStatus,
                            ]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Wompi: Error processing payment', [
                'payment_id' => $payment->id,
                'message'    => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle order cancellation.
     */
    public function cancelTransaction(OrderModel $order): void
    {
        try {
            if (
                $order->payment &&
                $order->payment->method === 'wompi'
            ) {
                $transaction = $this->transactionRepository->findOneWhere([
                    'order_id' => $order->id,
                ]);

                if ($transaction && in_array($transaction->status, ['PENDING', 'APPROVED'])) {
                    // Update transaction status to cancelled
                    $this->transactionRepository->updateTransactionStatus(
                        $transaction->id,
                        'CANCELLED',
                        ['cancelled_at' => now(), 'order_cancelled' => true]
                    );

                    Log::info('Wompi: Transaction cancelled due to order cancellation', [
                        'transaction_id' => $transaction->wompi_transaction_id,
                        'order_id'       => $order->id,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Wompi: Error cancelling transaction', [
                'order_id' => $order->id,
                'message'  => $e->getMessage(),
            ]);
        }
    }
}
