<?php

namespace App\Http\Controllers;

use App\Http\HttpClient\QuickTellerAPIClient;
use App\Http\HttpClient\SvaAPIClient;
use App\Repositories\QuickTellerAPIInterface;
use Illuminate\Http\Request;

class BillersController extends Controller
{
    /**
     * Create a new controller instance
     *
     * @param QuickTellerAPIInterface $apiService
     */
    public function __construct(QuickTellerAPIInterface $apiService)
    {
        $this->apiService = $apiService;
    }

    public function categories()
    {
        $response = $this->apiService->getCategories();
        return response()->json($response);
    }

    public function billers()
    {
        $response = $this->apiService->getBillers();
        return response()->json($response);
    }

    public function paymentItems(Request $request)
    {
        $billerId = $request->billerId;
        $response = $this->apiService->getPaymentItems($billerId);
        return response()->json($response);
    }

    public function validateCustomer()
    {
        $endpoint = 'validateCustomer';
        $data = [
            'requestReference' => env('REQUEST_REFERENCE_PREFIX') . 'GO0000035',
            'customerId' => '256773702727',
            'bankCbnCode' => env('CBN_CODE'),
            'amount' => 100000,
            'customerMobile' => '256773702727',
            'terminalId' => env('TERMINAL_ID'),
            'customerEmail' => 'baker@gopay.co.ug',
            'paymentCode' => '28310717'
        ];
        $auth = new QuickTellerAPIClient(env('SVA_API_URL'));
        $auth->addTerminalId();
        $response = $auth->svaPayments($endpoint, $data);
        return response()->json($response);
    }

    public function paymentAdvise()
    {
        $endpoint = 'sendAdviceRequest';
        $data = [
            'requestReference' => env('REQUEST_REFERENCE_PREFIX') . 'GO0000035',
            'customerId' => '256773702727',
            'bankCbnCode' => env('CBN_CODE'),
            'amount' => 100000,
            'customerMobile' => '256773702727',
            'terminalId' => env('TERMINAL_ID'),
            'customerEmail' => 'baker@gopay.co.ug',
            'paymentCode' => '28310717',
            'surcharge' => 0,
            'transactionRef' => 'BOU|WEB|3AOH0001|MTNMA|270220120920|925268'
        ];
        $transactionParams = '100000' . env('TERMINAL_ID') . env('REQUEST_REFERENCE_PREFIX') . 'GO0000035' . '256773702727' . '28310717';

        $auth = new QuickTellerAPIClient(env('SVA_API_URL'), $transactionParams);
        $auth->addTerminalId();
        $response = $auth->svaPayments($endpoint, $data);
        return response()->json($response);
    }

    public function requestReference()
    {
        $endpoint = 'transactions/' . env('REQUEST_REFERENCE_PREFIX') . 'GO0000008';
        $auth = new QuickTellerAPIClient(env('SVA_API_URL'));
        $auth->addTerminalId();
        $response = $auth->quickTeller($endpoint);
        return response()->json($response);
    }

    public function cashwithdrawal()
    {
        $endpoint = 'cashwithdrawal';
        $data = [
            'requestReference' => env('REQUEST_REFERENCE_PREFIX') . 'GO0000032',
            'customerId' => '256772685270',
            'bankCbnCode' => env('CBN_CODE'),
            'amount' => 100000,
            'customerMobile' => '256772685270',
            'terminalId' => env('TERMINAL_ID'),
            'customerEmail' => 'baker@gopay.co.ug',
            'paymentCode' => '28310716',
            'surcharge' => 0,
            'transactionRef' => 'BOU|WEB|3AOH0001|MTNMA|250220133049|862470',
            'pin' => '',
            'otp' => ''
        ];
        $transactionParams = '100000' . env('TERMINAL_ID') . env('REQUEST_REFERENCE_PREFIX') . 'GO0000032' . '256772685270' . '28310716' . '';

        $auth = new QuickTellerAPIClient(env('SVA_API_URL'), $transactionParams);
        $auth->addTerminalId();
        $response = $auth->svaPayments($endpoint, $data);
        return response()->json($response);
    }
}
