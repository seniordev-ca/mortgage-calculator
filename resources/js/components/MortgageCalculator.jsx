import React, { useState } from 'react';
import ReactDOM from 'react-dom/client';
import Button from 'react-bootstrap/Button';

const MortgageCalculator = () => {
  const [propertyPrice, setPropertyPrice] = useState('');
  const [downPayment, setDownPayment] = useState('');
  const [interestRate, setInterestRate] = useState('');
  const [amortizationPeriod, setAmortizationPeriod] = useState('5');
  const [paymentSchedule, setPaymentSchedule] = useState('monthly');
  const [payment, setPayment] = useState('');
  const [errors, setErrors] = useState({});

  const handleSubmit = async (event) => {
    event.preventDefault();

    const formData = new FormData();
    formData.append('property_price', propertyPrice);
    formData.append('down_payment', downPayment);
    formData.append('annual_interest_rate', interestRate);
    formData.append('amortization_period', amortizationPeriod);
    formData.append('payment_schedule', paymentSchedule);
    formData.append('asdfasdfasdf', 'helloworld!');

    try {
      const response = await axios.post('/api/calculate', {
        property_price: propertyPrice,
        down_payment: downPayment,
        annual_interest_rate: interestRate,
        amortization_period: amortizationPeriod,
        payment_schedule: paymentSchedule,
      }, {
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
      });

      console.log(response)
      if (response.data) {
        setPayment(response.data);
        setErrors({});
      } else {
        throw new Error(data.message);
      }
    } catch (error) {
      console.log(error.message)
      setErrors(error.response.data);
      setPayment('')
    }
  };

  return (
    <div className="max-w-xl mx-auto p-6 bg-white rounded-xl shadow-md flex items-center space-x-4">
      <div className="flex-shrink-0 w-full">
        <h2 className='font-bold mb-3'>Mortgage Payment Calculator</h2>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700">
                Property Price
            </label>
            {errors?.property_price ? <span className="text-red-500 text-xs italic">{errors.property_price}</span> : ''}
            <input
                type="number"
                value={propertyPrice}
                onChange={(e) => setPropertyPrice(e.target.value)}
                className="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700">
              Down Payment
            </label>
            {errors?.down_payment ? <span className="text-red-500 text-xs italic">{errors.down_payment}</span> : ''}
            <input
                type="number"
                value={downPayment}
                onChange={(e) => setDownPayment(e.target.value)}
                className="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700">
              Interest Rate
            </label>
            {errors?.annual_interest_rate ? <span className="text-red-500 text-xs italic">{errors.annual_interest_rate}</span> : ''}
            <input
                type="number"
                value={interestRate}
                onChange={(e) => setInterestRate(e.target.value)}
                className="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700">
                Amortization Period
            </label>
            {errors?.amortization_period ? <span className="text-red-500 text-xs italic">{errors.amortization_period}</span> : ''}
            <select
              value={amortizationPeriod}
              onChange={(e) => setAmortizationPeriod(e.target.value)}
              className="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
              <option value="5">5 years</option>
              <option value="10">10 years</option>
              <option value="15">15 years</option>
              <option value="20">20 years</option>
              <option value="25">25 years</option>
              <option value="30">30 years</option>
            </select>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700">
                Payment Schedule
            </label>
            {errors?.payment_schedule ? <span className="text-red-500 text-xs italic">{errors.payment_schedule}</span> : ''}
            <select
              value={paymentSchedule}
              onChange={(e) => setPaymentSchedule(e.target.value)}
              className="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
              <option value="">--Select Payment Schedule--</option>
              <option value="accelerated_bi_weekly">Accelerated Bi-Weekly</option>
              <option value="bi_weekly">Bi-Weekly</option>
              <option value="monthly">Monthly</option>
            </select>
          </div>
          <button type="submit" className="w-full px-3 py-2 rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
              Calculate
          </button>
        </form>
        {payment && (
          <div>
            <div>CMHC insurance: {payment.cmhc_insurance_premium}</div>
            <div>Total mortgage: {payment.total_mortgage}</div>
            <div>Payment per schedule: {payment.payment_per_schedule}</div>
          </div>
        )}
        {errors.error && <span className="text-red-500 text-xs italic">{errors.error}</span>}
      </div>
    </div>
  );
};

if (document.getElementById('mortgageCalculator')) {
  ReactDOM.createRoot(document.getElementById('mortgageCalculator')).render(		
    <MortgageCalculator />		
  );
}
