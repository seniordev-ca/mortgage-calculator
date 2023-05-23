<?php

namespace App\Http\Controllers;

use App\Services\MortgageCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MortgageCalculatorController extends Controller
{
    private $mortgageService;

    public function __construct(MortgageCalculatorService $mortgageService)
    {
        $this->mortgageService = $mortgageService;
    }

    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_price' => 'required|numeric|min:1',
            'down_payment' => 'required|numeric|min:1',
            'annual_interest_rate' => 'required|numeric|min:1',
            'amortization_period' => 'required|numeric|min:5|max:30',
            'payment_schedule' => 'required|string|in:accelerated bi-weekly,bi-weekly,monthly',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $result = $this->mortgageService->calculate($request->all());
            return response()->json($result);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }
}
