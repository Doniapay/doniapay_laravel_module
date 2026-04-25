<?php
namespace Modules\DoniaPay\Services;
use Illuminate\Support\Facades\Http;
class DoniaPayService
{
    protected $apiKey;
    protected $apiUrl;
    protected $apiDomain = "doniapay.com";
    public function __construct()
    {
        $this->apiKey = config('doniapay.api_key');
        $this->apiUrl = config('doniapay.api_url');
        if (strpos($this->apiUrl, $this->apiDomain) === false) {
            throw new \Exception("Security Alert: Invalid Api.");
        }
    }
    public function makePayment($genericData)
    {
        $mappedData = [
            "dn_su"  => $genericData['success_url'] ?? '',
            "dn_cu"  => $genericData['cancel_url'] ?? '',
            "dn_wu"  => $genericData['success_url'] ?? '',
            "dn_am"  => $genericData['amount'] ?? '0',
            "dn_cn"  => $genericData['name'] ?? 'Customer',
            "dn_ce"  => $genericData['email'] ?? '',
            "dn_mt"  => $genericData['meta'] ?? json_encode([]),
            "dn_rt"  => "GET"
        ];
        $payload = base64_encode(json_encode($mappedData));
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
