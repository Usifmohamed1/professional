<x-plugins-payment::payment-method
    :name="\Botble\Payment\Enums\PaymentMethodEnum::INSTAPAY"
    :label="get_payment_setting('name', 'instapay', trans('plugins/payment::payment.payment_via_instapay'))"
>
    <p class="mb-1">
        <strong>{{ trans('plugins/payment::payment.instapay_number') }}:</strong>
        {{ get_payment_setting('number', 'instapay') ?: trans('plugins/payment::payment.instapay_number_not_configured') }}
    </p>

    <p class="mb-1">
        <strong>{{ trans('plugins/payment::payment.instapay_account') }}:</strong>
        {{ get_payment_setting('account', 'instapay') ?: trans('plugins/payment::payment.instapay_account_not_configured') }}
    </p>

    <div class="mt-3">
        <label class="form-label" for="instapay_transfer_receipt">
            {{ trans('plugins/payment::payment.instapay_transfer_receipt') }}
        </label>
        <input
            class="form-control"
            id="instapay_transfer_receipt"
            name="instapay_transfer_receipt"
            type="file"
            accept="image/*"
            data-instapay-transfer-receipt
        >
    </div>

    <script>
        (function () {
            function toggleInstapayReceiptRequired() {
                var receipt = document.querySelector('[data-instapay-transfer-receipt]');

                if (! receipt) {
                    return;
                }

                var selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked');
                receipt.required = selectedPaymentMethod && selectedPaymentMethod.value === 'instapay';
            }

            toggleInstapayReceiptRequired();
            document.addEventListener('change', function (event) {
                if (event.target && event.target.name === 'payment_method') {
                    toggleInstapayReceiptRequired();
                }
            });
        })();
    </script>
</x-plugins-payment::payment-method>
