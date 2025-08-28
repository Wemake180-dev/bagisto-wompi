<?php

namespace Webkul\Wompi\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Sales\Models\Order;
use Webkul\Wompi\Contracts\WompiTransaction as WompiTransactionContract;

class WompiTransaction extends Model implements WompiTransactionContract
{
    /**
     * The table associated with the model.
     */
    protected $table = 'wompi_transactions';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'wompi_transaction_id',
        'wompi_reference',
        'amount_in_cents',
        'currency',
        'status',
        'payment_method',
        'customer_email',
        'response_data',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'amount_in_cents' => 'integer',
        'response_data'   => 'array',
    ];

    /**
     * Get the order that this transaction belongs to.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Find a transaction by Wompi transaction ID.
     */
    public function findByWompiId(string $wompiId)
    {
        return $this->where('wompi_transaction_id', $wompiId)->first();
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
    public function updateStatus(int $id, string $status, array $additionalData = [])
    {
        $transaction = $this->find($id);

        if ($transaction) {
            $transaction->status = $status;

            if (! empty($additionalData)) {
                $transaction->response_data = array_merge(
                    $transaction->response_data ?? [],
                    $additionalData
                );
            }

            return $transaction->save();
        }

        return false;
    }

    /**
     * Get transactions by order ID.
     */
    public function getByOrderId(int $orderId)
    {
        return $this->where('order_id', $orderId)->get();
    }
}
