<?php
namespace Modules\DoniaPay\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\DoniaPay\Services\DoniaPayService;
class DoniaPayController extends Controller
{
    protected $payService;
    public function __construct(DoniaPayService $payService)
    {
        $this->payService = $payService;
    }
    public function initiatePayment(Request $request)
    {
        $orderInfo = [
            "success_url" => route('doniapay.success'),
            "cancel_url"  => route('doniapay.cancel'),
            "amount"      => "100",
            "name"        => "Customer Name",
            "email"       => "customer@email.com",
            "meta"        => json_encode(["order_id" => "123"])
        ];
        $res = $this->payService->makePayment($orderInfo);
        if (isset($res['status']) && $res['status'] == 1) {
            return redirect()->away($res['payment_url']);
        }
        return back()->with('error', 'Payment Initialization Failed');
    }
    public function success(Request $request)
    {
        $trxId = $request->transactionId ?? $request->payment_id;
        $res = $this->payService->verifyPayment($trxId);
        if ($res && (isset($res['status']) && ($res['status'] == 'COMPLETED' || $res['status'] == 1))) {
            return "Payment Successful! TrxID: " . $trxId;
        }
        return "Payment Verification Failed!";
    }
    public function cancel()
    {
        return "Payment Cancelled";
    }
}
