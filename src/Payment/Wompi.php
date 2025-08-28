<?php

namespace Webkul\Wompi\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Webkul\Payment\Payment\Payment;
use Webkul\Wompi\Repositories\WompiTransactionRepository;

class Wompi extends Payment
{
    /**
     * Payment method code.
     */
    protected $code = 'wompi';

    /**
     * Wompi transaction repository.
     */
    protected $transactionRepository;

    public function __construct(WompiTransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Return Wompi redirect url.
     */
    public function getRedirectUrl()
    {
        return route('wompi.redirect');
    }

    /**
     * Get Wompi API base URL.
     */
    public function getApiBaseUrl(): string
    {
        return $this->getConfigData('sandbox')
            ? 'https://api-sandbox.wompi.pa/v1'
            : 'https://api.wompi.pa/v1';
    }

    /**
     * Get public key for Wompi integration.
     */
    public function getPublicKey(): string
    {
        return $this->getConfigData('sandbox')
            ? $this->getConfigData('sandbox_public_key')
            : $this->getConfigData('public_key');
    }

    /**
     * Get private key for Wompi integration.
     */
    public function getPrivateKey(): string
    {
        return $this->getConfigData('sandbox')
            ? $this->getConfigData('sandbox_private_key')
            : $this->getConfigData('private_key');
    }

    /**
     * Get acceptance token from Wompi.
     */
    public function getAcceptanceToken(): ?string
    {
        try {
            $publicKey = $this->getPublicKey();
            $url = "{$this->getApiBaseUrl()}/merchants/{$publicKey}";
            
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();

                return $data['data']['presigned_acceptance']['acceptance_token'] ?? null;
            }

            Log::error('Wompi: Failed to get acceptance token', [
                'status'   => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Wompi: Exception getting acceptance token', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Create a Wompi transaction.
     */
    public function createTransaction(array $sessionData = []): ?array
    {
        try {
            $cart = $this->getCart();
            $acceptanceToken = $this->getAcceptanceToken();

            if (! $acceptanceToken) {
                throw new \Exception('Unable to obtain acceptance token');
            }

            $reference = 'wompi_'.$cart->id.'_'.time();
            $amountInCents = (int) ($cart->grand_total * 100);

            $payload = [
                'acceptance_token' => $acceptanceToken,
                'amount_in_cents'  => $amountInCents,
                'currency'         => $cart->cart_currency_code,
                'customer_email'   => $cart->customer_email,
                'reference'        => $reference,
                'payment_method'   => [
                    'type' => 'CLAVE'
                ], // Use CLAVE for direct processing
                'signature'        => $this->generateSignature($reference, $amountInCents, $cart->cart_currency_code),
            ];

            // Add session data if provided (from Wompi JS)
            if (! empty($sessionData['session_id'])) {
                $payload['session_id'] = $sessionData['session_id'];
            }

            if (! empty($sessionData['device_id'])) {
                $payload['customer_data'] = [
                    'device_id' => $sessionData['device_id'],
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->getPrivateKey(),
                'Content-Type'  => 'application/json',
            ])->post("{$this->getApiBaseUrl()}/transactions", $payload);

            if ($response->successful()) {
                $data = $response->json();

                // Store transaction in our database
                $this->transactionRepository->createTransaction([
                    'order_id'             => null, // Will be updated when order is created
                    'wompi_transaction_id' => $data['data']['id'],
                    'wompi_reference'      => $reference,
                    'amount_in_cents'      => $amountInCents,
                    'currency'             => $cart->cart_currency_code,
                    'status'               => $data['data']['status'],
                    'payment_method'       => 'wompi',
                    'customer_email'       => $cart->customer_email,
                    'response_data'        => $data,
                ]);

                return $data;
            }

            Log::error('Wompi: Transaction creation failed', [
                'status'   => $response->status(),
                'response' => $response->body(),
                'payload'  => $payload,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Wompi: Exception creating transaction', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Generate signature for transaction integrity.
     */
    protected function generateSignature(string $reference, int $amountInCents, string $currency): string
    {
        $privateKey = $this->getPrivateKey();
        $concatenatedString = "{$reference}{$amountInCents}{$currency}{$privateKey}";

        return hash('sha256', $concatenatedString);
    }

    /**
     * Generate integrity signature for Wompi Widget Checkout.
     */
    public function generateWidgetSignature(string $reference, int $amountInCents, string $currency): string
    {
        $privateKey = $this->getPrivateKey();

        // Widget signature format: reference + amountInCents + currency + privateKey
        $concatenatedString = "{$reference}{$amountInCents}{$currency}{$privateKey}";

        return hash('sha256', $concatenatedString);
    }

    /**
     * Verify webhook signature.
     */
    public function verifyWebhookSignature(array $event, string $signature): bool
    {
        try {
            $privateKey = $this->getPrivateKey();
            $concatenatedString = '';

            // Reconstruct the signature string according to Wompi's specification
            ksort($event);
            foreach ($event as $key => $value) {
                if (is_array($value)) {
                    ksort($value);
                    $concatenatedString .= $key.json_encode($value);
                } else {
                    $concatenatedString .= $key.$value;
                }
            }

            $concatenatedString .= $privateKey;
            $expectedSignature = hash('sha256', $concatenatedString);

            return hash_equals($expectedSignature, $signature);
        } catch (\Exception $e) {
            Log::error('Wompi: Exception verifying webhook signature', [
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get transaction status from Wompi.
     */
    public function getTransactionStatus(string $transactionId): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->getPublicKey(),
            ])->get("{$this->getApiBaseUrl()}/transactions/{$transactionId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Wompi: Failed to get transaction status', [
                'transaction_id' => $transactionId,
                'status'         => $response->status(),
                'response'       => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Wompi: Exception getting transaction status', [
                'transaction_id' => $transactionId,
                'message'        => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Check if payment method is available.
     */
    public function isAvailable(): bool
    {
        if (! parent::isAvailable()) {
            return false;
        }

        // Check if required configuration is present
        $publicKey = $this->getPublicKey();
        $privateKey = $this->getPrivateKey();

        return ! empty($publicKey) && ! empty($privateKey);
    }

    /**
     * Get payment method image.
     *
     * @return string
     */
    public function getImage()
    {
        $url = $this->getConfigData('image');

        return $url ? \Illuminate\Support\Facades\Storage::url($url) : bagisto_asset('images/wompi-logo.png', 'shop');
    }

    /**
     * Get payment method additional details.
     */
    public function getAdditionalDetails(): array
    {
        $details = parent::getAdditionalDetails();

        // Add Wompi specific information
        $details[] = [
            'title' => 'Supported Payment Methods',
            'value' => 'Credit/Debit Cards (Visa, MasterCard), Clave (Panama)',
        ];

        $details[] = [
            'title' => 'Security',
            'value' => '3D Secure authentication for enhanced security',
        ];

        return $details;
    }

    /**
     * Process webhook event.
     */
    public function processWebhook(array $eventData): bool
    {
        try {
            $transactionId = $eventData['data']['transaction']['id'] ?? null;

            if (! $transactionId) {
                Log::error('Wompi: Webhook missing transaction ID', ['event_data' => $eventData]);

                return false;
            }

            // Find the transaction in our database
            $transaction = $this->transactionRepository->findByWompiId($transactionId);

            if (! $transaction) {
                Log::error('Wompi: Transaction not found for webhook', [
                    'transaction_id' => $transactionId,
                ]);

                return false;
            }

            $newStatus = $eventData['data']['transaction']['status'] ?? 'UNKNOWN';

            // Update transaction status
            $this->transactionRepository->updateTransactionStatus(
                $transaction->id,
                $newStatus,
                $eventData
            );

            Log::info('Wompi: Webhook processed successfully', [
                'transaction_id' => $transactionId,
                'new_status'     => $newStatus,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Wompi: Exception processing webhook', [
                'message'    => $e->getMessage(),
                'event_data' => $eventData,
            ]);

            return false;
        }
    }
}
