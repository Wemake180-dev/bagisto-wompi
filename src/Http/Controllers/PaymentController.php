<?php

namespace Webkul\Wompi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
// Removed shop controller import
use Webkul\Wompi\Payment\Wompi;
use Webkul\Wompi\Repositories\WompiTransactionRepository;

class PaymentController extends Controller
{
    /**
     * Wompi payment instance.
     */
    protected $wompiPayment;

    /**
     * Wompi transaction repository.
     */
    protected $transactionRepository;

    /**
     * Order repository.
     */
    protected $orderRepository;

    public function __construct(
        Wompi $wompiPayment,
        WompiTransactionRepository $transactionRepository,
        OrderRepository $orderRepository
    ) {
        $this->wompiPayment = $wompiPayment;
        $this->transactionRepository = $transactionRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Redirect to Wompi payment gateway.
     */
    public function redirect(Request $request)
    {
        try {
            Log::info('Wompi: Starting redirect process');
            
            $cart = Cart::getCart();

            if (! $cart) {
                Log::warning('Wompi: No cart found, redirecting to cart page');
                return redirect()->route('shop.checkout.cart.index')
                    ->with('error', 'Your cart is empty.');
            }
            
            Log::info('Wompi: Cart found', ['cart_id' => $cart->id, 'total' => $cart->grand_total]);

            // Generate payment data for Widget Checkout
            $reference = 'wompi_'.$cart->id.'_'.time();
            $amountInCents = (int) ($cart->grand_total * 100);
            $currency = $cart->cart_currency_code;
            
            Log::info('Wompi: Generated payment data', [
                'reference' => $reference,
                'amount_cents' => $amountInCents,
                'currency' => $currency
            ]);
            
            $publicKey = $this->wompiPayment->getPublicKey();
            
            Log::info('Wompi: Got public key', ['public_key' => substr($publicKey, 0, 20) . '...']);

            // Generate integrity signature for Widget
            $signature = $this->wompiPayment->generateWidgetSignature(
                $reference, 
                $amountInCents, 
                $currency
            );
            
            Log::info('Wompi: Generated signature', ['signature' => substr($signature, 0, 20) . '...']);

            // Store transaction reference for later tracking
            $this->transactionRepository->createTransaction([
                'order_id'             => null, // Will be updated when order is created
                'wompi_transaction_id' => null, // Will be updated from webhook
                'wompi_reference'      => $reference,
                'amount_in_cents'      => $amountInCents,
                'currency'             => $currency,
                'status'               => 'PENDING',
                'payment_method'       => 'wompi',
                'customer_email'       => $cart->customer_email,
                'response_data'        => [],
            ]);

            Log::info('Wompi: About to show Widget Checkout page');

            // Show Widget Checkout page
            return view('wompi::payment.redirect', [
                'cart'          => $cart,
                'reference'     => $reference,
                'amount'        => $amountInCents,
                'currency'      => $currency,
                'publicKey'     => $publicKey,
                'signature'     => $signature,
                'customerEmail' => $cart->customer_email,
                'customerName'  => $cart->customer->name ?? '',
            ]);

        } catch (\Exception $e) {
            Log::error('Wompi: Exception in redirect', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return redirect()->route('shop.checkout.onepage.index')
                ->with('error', 'Payment initialization failed. Please try again.');
        }
    }

    /**
     * Handle successful payment return.
     */
    public function success(Request $request)
    {
        try {
            $transactionId = $request->get('id');

            if ($transactionId) {
                // Get transaction status from Wompi
                $statusData = $this->wompiPayment->getTransactionStatus($transactionId);

                if ($statusData && $statusData['data']['status'] === 'APPROVED') {
                    // Find the transaction in our database
                    $transaction = $this->transactionRepository->findByWompiId($transactionId);

                    if ($transaction && $transaction->order_id) {
                        $order = $this->orderRepository->find($transaction->order_id);

                        if ($order) {
                            // Clear cart and redirect to order success
                            Cart::deActivateCart();

                            return redirect()->route('shop.checkout.success', $order->id)
                                ->with('success', 'Your payment was processed successfully!');
                        }
                    }
                }
            }

            return view('wompi::payment.success');

        } catch (\Exception $e) {
            Log::error('Wompi: Exception in success handler', [
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('shop.home.index')
                ->with('warning', 'Payment was processed, but there was an issue. Please check your order status.');
        }
    }

    /**
     * Handle cancelled payment return.
     */
    public function cancel(Request $request)
    {
        try {
            $transactionId = $request->get('id');

            if ($transactionId) {
                $transaction = $this->transactionRepository->findByWompiId($transactionId);

                if ($transaction) {
                    $this->transactionRepository->updateTransactionStatus(
                        $transaction->id,
                        'CANCELLED',
                        ['cancelled_at' => now(), 'user_cancelled' => true]
                    );
                }
            }

            return redirect()->route('shop.checkout.onepage.index')
                ->with('error', 'Payment was cancelled. Please try again if you wish to complete your purchase.');

        } catch (\Exception $e) {
            Log::error('Wompi: Exception in cancel handler', [
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('shop.checkout.onepage.index')
                ->with('error', 'Payment was cancelled.');
        }
    }

    /**
     * Handle Wompi webhook notifications.
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            $signature = $request->header('X-Signature');
            $eventData = $request->all();

            // Validate webhook signature
            if (! $this->wompiPayment->verifyWebhookSignature($eventData, $signature)) {
                Log::warning('Wompi: Invalid webhook signature', [
                    'signature' => $signature,
                    'data'      => $eventData,
                ]);

                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Process the webhook event
            $processed = $this->wompiPayment->processWebhook($eventData);

            if ($processed) {
                Log::info('Wompi: Webhook processed successfully', [
                    'event'          => $eventData['event'] ?? 'unknown',
                    'transaction_id' => $eventData['data']['transaction']['id'] ?? null,
                ]);

                return response()->json(['status' => 'success']);
            }

            return response()->json(['error' => 'Failed to process webhook'], 400);

        } catch (\Exception $e) {
            Log::error('Wompi: Exception in webhook handler', [
                'message' => $e->getMessage(),
                'data'    => $request->all(),
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get transaction status via AJAX.
     */
    public function getTransactionStatus(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'transaction_id' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error'    => 'Invalid request',
                    'messages' => $validator->errors(),
                ], 422);
            }

            $transactionId = $request->get('transaction_id');
            $statusData = $this->wompiPayment->getTransactionStatus($transactionId);

            if (! $statusData) {
                return response()->json([
                    'error' => 'Failed to get transaction status',
                ], 400);
            }

            // Update local transaction status
            $transaction = $this->transactionRepository->findByWompiId($transactionId);
            if ($transaction) {
                $newStatus = $statusData['data']['status'];

                if ($transaction->status !== $newStatus) {
                    $this->transactionRepository->updateTransactionStatus(
                        $transaction->id,
                        $newStatus,
                        $statusData
                    );
                }
            }

            return response()->json([
                'status' => 'success',
                'data'   => $statusData['data'],
            ]);

        } catch (\Exception $e) {
            Log::error('Wompi: Exception getting transaction status', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Internal server error',
            ], 500);
        }
    }
}
