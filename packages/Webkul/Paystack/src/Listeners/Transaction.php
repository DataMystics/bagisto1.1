<?php

namespace Webkul\Paystack\Listeners;

use Webkul\Paystack\Payment\SmartButton;
use Webkul\Sales\Repositories\OrderTransactionRepository;

class Transaction
{
    /**
     * Create a new listener instance.
     *
     * @return void
     */
    public function __construct(
        // protected SmartButton $smartButton,
        protected OrderTransactionRepository $orderTransactionRepository
    ) {}

    /**
     * Save the transaction data for online payment.
     *
     * @param  \Webkul\Sales\Models\Invoice  $invoice
     * @return void
     */
    public function saveTransaction($invoice)
    {
        $data = request()->all();

       
        if ($invoice->order->payment->method == 'paystack_standard') {
            $this->orderTransactionRepository->create([
                'transaction_id' => $data['txn_id'],
                'status'         => $data['payment_status'],
                'type'           => $data['payment_type'],
                'payment_method' => $invoice->order->payment->method,
                'order_id'       => $invoice->order->id,
                'invoice_id'     => $invoice->id,
                'data'           => json_encode($data),
            ]);
        }
    }
}
