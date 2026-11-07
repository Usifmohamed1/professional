<?php

namespace Botble\RealEstate\Http\Controllers\Fronts;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Media\Facades\RvMedia;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Services\Gateways\BankTransferPaymentService;
use Botble\Payment\Services\Gateways\CodPaymentService;
use Botble\Payment\Services\Gateways\InstapayPaymentService;
use Botble\RealEstate\Http\Requests\CheckoutRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CheckoutController extends BaseController
{
    public function postCheckout(CheckoutRequest $request)
    {
        $returnUrl = $request->input('return_url');

        $currency = $request->input('currency', config('plugins.payment.payment.currency'));
        $currency = strtoupper($currency);

        $data = [
            'error' => false,
            'message' => false,
            'amount' => $request->input('amount'),
            'currency' => $currency,
            'type' => $request->input('payment_method'),
            'charge_id' => null,
        ];

        session()->put('selected_payment_method', $data['type']);

        $paymentData = apply_filters(PAYMENT_FILTER_PAYMENT_DATA, [], $request);

        switch ($request->input('payment_method')) {
            case PaymentMethodEnum::COD:
                $codPaymentService = app(CodPaymentService::class);
                $data['charge_id'] = $codPaymentService->execute($paymentData);
                $data['message'] = trans('plugins/payment::payment.payment_pending');
                $data['checkoutUrl'] = $request->input('callback_url') . '?charge_id=' . $data['charge_id'];

                break;

            case PaymentMethodEnum::BANK_TRANSFER:
                $bankTransferPaymentService = app(BankTransferPaymentService::class);
                $data['charge_id'] = $bankTransferPaymentService->execute($paymentData);
                $data['message'] = trans('plugins/payment::payment.payment_pending');
                $data['checkoutUrl'] = $request->input('callback_url') . '?charge_id=' . $data['charge_id'];

                break;

            case PaymentMethodEnum::INSTAPAY:
                $upload = RvMedia::handleUpload(
                    $request->file('instapay_transfer_receipt'),
                    0,
                    auth('account')->user()->upload_folder
                );

                if ($upload['error']) {
                    $data['error'] = true;
                    $data['message'] = $upload['message'];

                    break;
                }

                $instapayPaymentService = app(InstapayPaymentService::class);
                $data['charge_id'] = $instapayPaymentService->execute($paymentData);
                $data['message'] = trans('plugins/payment::payment.payment_pending');
                $data['checkoutUrl'] = $request->input('callback_url') . '?charge_id=' . $data['charge_id'];

                $receiptData = $upload['data'];

                DB::table('re_instapay_payment_proofs')->insert([
                    'account_id' => auth('account')->id(),
                    'package_id' => Arr::first((array) Arr::get($paymentData, 'order_id')),
                    'amount' => $request->input('amount'),
                    'phone' => get_payment_setting('number', PaymentMethodEnum::INSTAPAY),
                    'receipt_path' => data_get($receiptData, 'url') ?: data_get($receiptData, 'path'),
                    'receipt_data' => json_encode([
                        'charge_id' => $data['charge_id'],
                        'payment_method' => PaymentMethodEnum::INSTAPAY,
                        'file' => $receiptData,
                    ]),
                    'status' => 'pending',
                    'notes' => sprintf('Checkout payment charge: %s', $data['charge_id']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                break;

            default:
                $data = apply_filters(PAYMENT_FILTER_AFTER_POST_CHECKOUT, $data, $request);

                break;
        }

        if ($checkoutUrl = Arr::get($data, 'checkoutUrl')) {
            return $this
                ->httpResponse()
                ->setError($data['error'])
                ->setNextUrl($checkoutUrl)
                ->withInput()
                ->setMessage($data['message']);
        }

        if ($data['error'] || ! $data['charge_id']) {
            return $this
                ->httpResponse()
                ->setError()
                ->setNextUrl($returnUrl)
                ->withInput()
                ->setMessage($data['message'] ?: trans('plugins/real-estate::account.checkout_error'));
        }

        $callbackUrl = $request->input('callback_url') . '?' . http_build_query($data);

        return redirect()->to($callbackUrl)->with('success_msg', trans('plugins/payment::payment.checkout_success'));
    }
}
