<?php

namespace Database\Seeders;

use Botble\Base\Facades\MetaBox;
use Botble\Base\Supports\BaseSeeder;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\Slug\Facades\SlugHelper;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends BaseSeeder
{
    public function run(): void
    {
        Category::query()->truncate();
        Slug::query()->where('reference_type', Category::class)->delete();
        DB::table('meta_boxes')->where('reference_type', Category::class)->delete();

        $categories = [
            'Apartment',
            'Villa',
            'Condo',
            'House',
            'Land',
            'Vacation/Summer Homes',
            'Commercial property',
        ];

        $icons = [
            'ti ti-home',
            'ti ti-calendar',
            'ti ti-shopping-cart',
            'ti ti-chart-bar',
            'ti ti-mail',
            'ti ti-map',
            'ti ti-bell',
        ];

        foreach ($categories as $key => $category) {
            $category = Category::query()->create([
                'name' => $category,
                'order' => $key,
                'is_default' => $key === 0,
            ]);

            MetaBox::saveMetaBoxData($category, 'icon', $this->fake()->unique()->randomElement($icons));

            SlugHelper::createSlug($category);
        }

        $categoryIdsByName = Category::query()->pluck('id', 'name');
        $properties = Property::query()->get();

        foreach ($properties as $property) {
            $normalizedName = mb_strtolower($property->name);
            $categoryName = match (true) {
                str_contains($normalizedName, 'office') => 'Commercial property',
                str_contains($normalizedName, 'land') => 'Land',
                str_contains($normalizedName, 'villa') => 'Villa',
                str_contains($normalizedName, 'apartment') => 'Apartment',
                str_contains($normalizedName, 'condo') => 'Condo',
                str_contains($normalizedName, 'seaside'), str_contains($normalizedName, 'retreat') => 'Vacation/Summer Homes',
                default => 'House',
            };

            $property->categories()->sync([$categoryIdsByName[$categoryName]]);
            $property->save();
        }

        $projects = Project::query()->get();

        foreach ($projects as $project) {
            $project->categories()->sync([Category::query()->inRandomOrder()->value('id')]);
            $project->save();
        }
    }
}
