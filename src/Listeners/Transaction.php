<?php

namespace Webkul\Wompi\Listeners;

use Illuminate\Support\Facades\Log;
use Webkul\Sales\Models\Invoice;
use Webkul\Wompi\Repositories\WompiTransactionRepository;

class Transaction
{
    /**
     * Wompi transaction repository.
     */
    protected $transactionRepository;

    public function __construct(WompiTransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Complete transaction when invoice is created.
     */
    public function completeTransaction(Invoice $invoice): void
    {
        try {
            $order = $invoice->order;

            // Check if this order uses Wompi payment method
            if (
                $order->payment &&
                $order->payment->method === 'wompi'
            ) {
                $transaction = $this->transactionRepository->findOneWhere([
                    'order_id' => $order->id,
                ]);

                if ($transaction) {
                    // Update transaction with invoice information
                    $this->transactionRepository->updateTransactionStatus(
                        $transaction->id,
                        'COMPLETED',
                        [
                            'invoice_id'         => $invoice->id,
                            'invoice_created_at' => $invoice->created_at,
                            'completed_at'       => now(),
                        ]
                    );

                    Log::info('Wompi: Transaction completed with invoice creation', [
                        'transaction_id' => $transaction->wompi_transaction_id,
                        'order_id'       => $order->id,
                        'invoice_id'     => $invoice->id,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Wompi: Error completing transaction', [
                'invoice_id' => $invoice->id,
                'order_id'   => $invoice->order_id,
                'message'    => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle transaction status updates from webhooks or polling.
     */
    public function updateTransactionStatus(array $eventData): void
    {
        try {
            $transactionId = $eventData['transaction_id'] ?? null;
            $newStatus = $eventData['status'] ?? null;

            if (! $transactionId || ! $newStatus) {
                Log::warning('Wompi: Invalid event data for transaction status update', $eventData);

                return;
            }

            $transaction = $this->transactionRepository->findByWompiId($transactionId);

            if (! $transaction) {
                Log::warning('Wompi: Transaction not found for status update', [
                    'transaction_id' => $transactionId,
                ]);

                return;
            }

            // Only update if status has changed
            if ($transaction->status !== $newStatus) {
                $this->transactionRepository->updateTransactionStatus(
                    $transaction->id,
                    $newStatus,
                    array_merge($eventData, ['status_updated_at' => now()])
                );

                Log::info('Wompi: Transaction status updated', [
                    'transaction_id' => $transactionId,
                    'old_status'     => $transaction->status,
                    'new_status'     => $newStatus,
                ]);

                // If transaction is approved and order exists, trigger order processing
                if (
                    $newStatus === 'APPROVED' &&
                    $transaction->order_id &&
                    $transaction->order
                ) {
                    $this->handleApprovedTransaction($transaction);
                }
            }
        } catch (\Exception $e) {
            Log::error('Wompi: Error updating transaction status', [
                'event_data' => $eventData,
                'message'    => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle approved transaction processing.
     */
    protected function handleApprovedTransaction($transaction): void
    {
        try {
            $order = $transaction->order;

            // Update order status if needed
            if ($order->status === 'pending_payment') {
                $order->status = 'processing';
                $order->save();

                Log::info('Wompi: Order status updated to processing', [
                    'order_id'       => $order->id,
                    'transaction_id' => $transaction->wompi_transaction_id,
                ]);
            }

            // Trigger invoice generation if configured
            $paymentConfig = core()->getConfigData('sales.payment_methods.wompi');
            if ($paymentConfig['generate_invoice'] ?? false) {
                // This would trigger invoice generation
                // Implementation would depend on specific Bagisto version
                event('wompi.transaction.approved', [$transaction, $order]);
            }
        } catch (\Exception $e) {
            Log::error('Wompi: Error handling approved transaction', [
                'transaction_id' => $transaction->wompi_transaction_id,
                'order_id'       => $transaction->order_id,
                'message'        => $e->getMessage(),
            ]);
        }
    }
}
