<?php

namespace App\Services;

use Exception;

class MortgageCalculatorService
{
    public function calculate($data)
    {
        $propertyPrice = $data['property_price'];
        $downPayment = $data['down_payment'];
        $annualInterestRate = $data['annual_interest_rate'] / 100;
        $amortizationPeriod = $data['amortization_period'];
        $paymentSchedule = $data['payment_schedule'];

        $cmhcInsurance = $this->calculateCMHCInsurance($propertyPrice, $downPayment);

        if ($cmhcInsurance === NULL) {
            throw new Exception('The minimum down payment in Canada is between 5% and 10%.');
        }
        $principal = $propertyPrice + $cmhcInsurance - $downPayment;

        $paymentPerPaymentSchedule = $this->calculatePayment($principal, $annualInterestRate, $amortizationPeriod, $paymentSchedule);

        return [
            'cmhc_insurance' => $cmhcInsurance,
            'total_mortgage' => $principal,
            'payment_per_payment_schedule' => $paymentPerPaymentSchedule,
        ];
    }

    private function calculateCMHCInsurance($propertyPrice, $downPayment)
    {
        $downPaymentPercentage = $downPayment / $propertyPrice * 100;
        $mortgageBeforeCMHC = $propertyPrice - $downPayment;

        if ($downPaymentPercentage >= 20) {
            return 0;
        } else if ($downPaymentPercentage >= 15) {
            return $mortgageBeforeCMHC * 0.028;
        } else if ($downPaymentPercentage >= 10) {
            return $mortgageBeforeCMHC * 0.031;
        } else if ($downPaymentPercentage >= 5) {
            return $mortgageBeforeCMHC * 0.04;
        } else {
            return NULL;
        }
    }

    private function calculatePayment($principal, $annualInterestRate, $amortizationPeriod, $paymentSchedule)
    {
        // Calculate number of payments per year and total number of payments
        switch ($paymentSchedule) {
            case 'accelerated_bi_weekly':
                $paymentsPerYear = 26;
                break;
            case 'bi_weekly':
                $paymentsPerYear = 26;
                break;
            case 'monthly':
                $paymentsPerYear = 12;
                break;
            default:
                throw new Exception('Invalid payment schedule');
        }

        $totalNumberOfPayments = $paymentsPerYear * $amortizationPeriod;
        $r = $annualInterestRate / $paymentsPerYear;

        return $principal * ($r * pow((1 + $r), $totalNumberOfPayments)) / (pow((1 + $r), $totalNumberOfPayments) - 1);
    }
}
