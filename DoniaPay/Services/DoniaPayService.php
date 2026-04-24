<?php
namespace Modules\DoniaPay\Services;
use Illuminate\Support\Facades\Http;
class DoniaPayService
{
    protected $apiKey;
    protected $apiUrl;
    public function __construct()
    {
        $this->apiKey = config('doniapay.api_key');
        $this->apiUrl = config('doniapay.api_url');
    }
    public function makePayment($data)
    {
        $payload = base64_encode(json_encode($data));
        $signature = hash_hmac('sha256', $payload, $this->apiKey);
        $response = Http::withHeaders([
            "X-Signature-Key" => $this->apiKey,
            "donia-signature" => $signature,
            "Content-Type"    => "application/json"
        ])->post($this->apiUrl . '/prepare', [
            'dp_payload' => $payload
        ]);
        return $response->json();
    }
    public function verifyPayment($transactionId)
    {
        $response = Http::withHeaders([
            "X-Signature-Key" => $this->apiKey,
            "Content-Type"    => "application/json"
        ])->post($this->apiUrl . '/confirm', [
            'transaction_id' => $transactionId
        ]);
        return $response->json();
    }
}
