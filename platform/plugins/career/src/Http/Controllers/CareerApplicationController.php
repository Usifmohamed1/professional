<?php

namespace ArchiElite\Career\Http\Controllers;

use ArchiElite\Career\Models\CareerApplication;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CareerApplicationController extends BaseController
{
    public function index(): View
    {
        PageTitle::setTitle(trans('plugins/career::career.application.applications'));

        $applications = CareerApplication::query()
            ->with('career')
            ->latest()
            ->paginate(20);

        return view('plugins/career::applications.index', compact('applications'));
    }

    public function download(CareerApplication $application): StreamedResponse
    {
        abort_unless(Storage::disk('public')->exists($application->cv_path), 404);

        return Storage::disk('public')->download(
            $application->cv_path,
            $application->cv_original_name ?: basename($application->cv_path)
        );
    }

    public function destroy(CareerApplication $application, BaseHttpResponse $response): BaseHttpResponse
    {
        $application->delete();

        return $response->withDeletedSuccessMessage();
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = array_filter((array) $request->input('ids', []));

        if (! $ids) {
            return back()->with('error_msg', trans('core/base::notices.no_select'));
        }

        CareerApplication::query()
            ->whereKey($ids)
            ->each(fn (CareerApplication $application) => $application->delete());

        return back()->with('success_msg', trans('core/base::notices.delete_success_message'));
    }
}
