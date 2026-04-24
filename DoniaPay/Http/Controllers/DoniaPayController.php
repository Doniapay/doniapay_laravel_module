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
        $rawData = [
            "dn_su"  => route('doniapay.success'),
            "dn_cu"  => route('doniapay.cancel'),
            "dn_wu"  => route('doniapay.success'),
            "dn_am"  => "100",
            "dn_cn"  => "Customer Name",
            "dn_ce"  => "customer@mail.com",
            "dn_mt"  => json_encode(["order_id" => "123"]),
            "dn_rt"  => "GET"
        ];
        $res = $this->payService->makePayment($rawData);
        if (isset($res['status']) && $res['status'] == 1) {
            return redirect()->away($res['payment_url']);
        }
        return back()->with('error', 'Payment Initialization Failed');
    }
    public function success(Request $request)
    {
        $trxId = $request->transactionId ?? $request->payment_id;
        $res = $this->payService->verifyPayment($trxId);
        if ($res && ($res['status'] == 'COMPLETED' || $res['status'] == 1)) {
            return "Payment Successful! TrxID: " . $trxId;
        }
        return "Payment Verification Failed!";
    }
    public function cancel()
    {
        return "Payment Cancelled";
    }
}
