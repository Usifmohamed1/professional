<?php

namespace ArchiElite\Career\Http\Controllers;

use ArchiElite\Career\Http\Requests\CareerApplicationRequest;
use ArchiElite\Career\Models\CareerApplication;
use ArchiElite\Career\Models\Career;
use Botble\Base\Http\Controllers\BaseController;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\RedirectResponse;

class PublicController extends BaseController
{
    public function careers()
    {
        $title = theme_option('career_careers_seo_title') ?: __('Careers');
        $description = theme_option('career_careers_seo_description');

        SeoHelper::setTitle($title);

        if ($description) {
            SeoHelper::setDescription($description);
        }

        Theme::breadcrumb()->add($title, route('public.careers'));

        $careers = Career::query()
            ->wherePublished()->latest()
            ->paginate(10);

        return Theme::scope('career.careers', compact('careers'), 'plugins/career::themes.careers')->render();
    }

    public function postApply(Career $career, CareerApplicationRequest $request): RedirectResponse
    {
        abort_unless($career->status->getValue() === 'published', 404);

        $cv = $request->file('cv');
        $path = $cv->store(sprintf('career-applications/%s', $career->getKey()), 'public');

        CareerApplication::query()->create([
            'career_id' => $career->getKey(),
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'cv_path' => $path,
            'cv_original_name' => $cv->getClientOriginalName(),
            'cv_mime_type' => $cv->getClientMimeType(),
            'cv_size' => $cv->getSize(),
        ]);

        return redirect($career->url . '#career-application')
            ->with('career_application_success', trans('plugins/career::career.application.success'));
    }
}
