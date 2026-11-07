<?php

namespace Botble\RealEstate\Http\Controllers\Settings;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Breadcrumb;
use Botble\RealEstate\Http\Requests\Settings\InvoiceTemplateSettingRequest;
use Botble\RealEstate\Supports\InvoiceHelper;
use Illuminate\Support\Facades\File;

class InvoiceTemplateSettingController extends BaseSettingController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/real-estate::settings.invoice_template.name'));
    }

    public function edit(InvoiceHelper $invoiceHelper)
    {
        $this->pageTitle(trans('plugins/real-estate::settings.invoice_template.name'));

        Assets::addScriptsDirectly('vendor/core/core/setting/js/email-template.js');

        $content = $invoiceHelper->getInvoiceTemplate();
        $variables = $invoiceHelper->getVariables();

        return view('plugins/real-estate::invoices.template', compact('content', 'variables'));
    }

    public function update(InvoiceTemplateSettingRequest $request)
    {
        BaseHelper::saveFileData(storage_path('app/templates/invoice.tpl'), $request->input('content'), false);

        return $this
            ->httpResponse()
            ->withUpdatedSuccessMessage();
    }

    public function reset()
    {
        File::delete(storage_path('app/templates/invoice.tpl'));

        return $this
            ->httpResponse()
            ->setMessage(trans('core/setting::setting.email.reset_success'));
    }

    public function preview(InvoiceHelper $invoiceHelper)
    {
        $invoice = $invoiceHelper->getDataForPreview();

        return $invoiceHelper->streamInvoice($invoice);
    }
}
