<?php

namespace Webkul\Paystack\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Webkul\Paystack\Helpers\Ipn;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Transformers\OrderResource;
use Unicodeveloper\Paystack\Facades\Paystack;
use Webkul\Sales\Repositories\OrderRepository;

class StandardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected Ipn $ipnHelper
    ) {}

    /**
     * Redirects to the paystack.
     *
     * @return \Illuminate\View\View
     */
    public function redirect()
    {
        $arr = [];
        $paystackStandard = app('Webkul\Paystack\Payment\Standard');

        foreach ($paystackStandard->getFormFields() as $name => $value) {
            $arr[$name] = $value;  // Append each $name => $value pair to the array
        }

        $customer['currency'] = $arr['currency_code'];
        $amount = $arr['amount']; // Assuming $arr['amount'] is '56.000'
        $amountInCents = $amount * 100; // Multiply by 100
        
        // Format the result to 3 decimal places
        $customer['amount'] = number_format($amountInCents, 3, '.', '');
        //generate a a random string something like TR00012123
        $customer['reference'] = 'TR'.rand(100000,999999);
        // $customer['reference'] = $arr['cart_id'];
        $customer['email'] = $arr['email'];

       
        // dd($customer);
        return $this->redirectToGateway($customer);

        return view('paystack::standard-redirect');
    }

    /**
     * Cancel payment from paystack.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel()
    {
        session()->flash('error', trans('shop::app.checkout.cart.paystack-payment-cancelled'));

        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Success payment.
     *
     * @return \Illuminate\Http\Response
     */
    public function success()
    {
        $cart = Cart::getCart();

        $data = (new OrderResource($cart))->jsonSerialize();

        $order = $this->orderRepository->create($data);

        Cart::deActivateCart();

        session()->flash('order_id', $order->id);

        return redirect()->route('shop.checkout.onepage.success');
    }

    /**
     * paystack IPN listener.
     *
     * @return \Illuminate\Http\Response
     */
    public function ipn()
    {
        $this->ipnHelper->processIpn(request()->all());
    }


    public function redirectToGateway($customer)
    {
        try{
            return Paystack::getAuthorizationUrl($customer)->redirectNow();
        }catch(\Exception $e) {
            Log::alert($e->getMessage());
            session()->flash('error', $e->getMessage());
            return redirect()->route('shop.checkout.cart.index');
        }        
    }
}
