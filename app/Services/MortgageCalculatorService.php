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

        $cmhcInsurance = $this->calculateCMHCInsurance($propertyPrice, $downPayment, $amortizationPeriod);

        $principal = $propertyPrice + $cmhcInsurance - $downPayment;

        $paymentPerPaymentSchedule = $this->calculatePayment($principal, $annualInterestRate, $amortizationPeriod, $paymentSchedule);

        return [
            'cmhc_insurance_premium' => $cmhcInsurance,
            'total_mortgage' => $principal,
            'payment_per_schedule' => $paymentPerPaymentSchedule,
        ];
    }

    public function calculateCMHCInsurance($propertyPrice, $downPayment, $amortizationPeriod)
    {
        if ($propertyPrice >= 1000000) {
            $minimumDownPayment = $propertyPrice * 0.2;
        } elseif ($propertyPrice >= 500000) {
            $minimumDownPayment = 500000 * 0.05 + ($propertyPrice - 500000) * 0.1;
        } else {
            $minimumDownPayment = $propertyPrice * 0.05;
        }

        if ($downPayment < $minimumDownPayment) {
            throw new Exception("Your minimum Down Payment: {$minimumDownPayment}, Check more details here: https://www.ratehub.ca/cmhc-insurance-british-columbia");
        }

        $downPaymentPercentage = $downPayment / $propertyPrice * 100;

        if ($amortizationPeriod > 25 && $downPaymentPercentage < 20) {
            throw new Exception("The maximum amortization period is 25 years for mortgages with less than 20% down.");
        }
        
        // If down payment is less than 20%, calculate CMHC insurance
        if ($downPaymentPercentage < 20) {
            $mortgageBeforeCMHC = $propertyPrice - $downPayment;

            if ($downPaymentPercentage >= 15) {
                $cmhcTaxRate = 0.028;
            } elseif ($downPaymentPercentage >= 10) {
                $cmhcTaxRate = 0.031;
            } else {
                $cmhcTaxRate = 0.04;
            }

            return $mortgageBeforeCMHC * $cmhcTaxRate;
        }

        return 0;
    }

    private function calculatePayment($principal, $annualInterestRate, $amortizationPeriod, $paymentSchedule)
    {
        // Calculate number of payments per year and total number of payments
        switch ($paymentSchedule) {
            case 'accelerated_bi_weekly':
                $paymentsPerYear = 26;
                break;
            case 'bi_weekly':
                $paymentsPerYear = 24;
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
