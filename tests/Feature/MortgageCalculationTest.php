<?php

namespace Tests\Feature;

use App\Services\MortgageCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MortgageCalculationTest extends TestCase
{
    /**
     * Mortgage calculation test without CMHC insurance
     */
    public function testMortgageCalculation()
    {
        $response = $this->postJson('/api/calculate', [
            'property_price' => 500000,
            'down_payment' => 100000,
            'annual_interest_rate' => 5,
            'amortization_period' => 25,
            'payment_schedule' => 'monthly',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'payment_per_schedule',
                'total_mortgage',
                'cmhc_insurance_premium',
            ])
            ->assertJson([
                'cmhc_insurance_premium' => 0,
            ]);
    }

    /**
     * Mortgage calculation test with CMHC insurance
     */
    public function testMortgageCalculationWithCmhc()
    {
        $response = $this->postJson('/api/calculate', [
            'property_price' => 500000,
            'down_payment' => 50000,
            'annual_interest_rate' => 5,
            'amortization_period' => 25,
            'payment_schedule' => 'monthly',
        ]);

        $mortgageService = new MortgageCalculatorService();
        $cmhcInsurance = $mortgageService->calculateCMHCInsurance(500000, 50000, 25);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'payment_per_schedule',
                'total_mortgage',
                'cmhc_insurance_premium',
            ])
            ->assertJson([
                'cmhc_insurance_premium' => $cmhcInsurance,
            ]);
    }
}
