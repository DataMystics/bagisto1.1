<?php

namespace Webkul\Paystack\Http\Controllers;

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
        $customer['amount'] = $arr['amount'];
        $customer['reference'] = $arr['cart_id'];
        $customer['email'] = $arr['email'];

       
        // dd($customer);
        $this->redirectToGateway($customer);

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
            session()->flash('error', $e->getMessage());
            return redirect()->route('shop.checkout.cart.index');
        }        
    }
}
