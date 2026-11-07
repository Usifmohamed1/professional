<?php

namespace Botble\RealEstate\Tests\Feature;

use Botble\RealEstate\Enums\ProjectStatusEnum;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectPriceFilterTest extends TestCase
{
    use RefreshDatabase;

    protected ProjectInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(ProjectInterface::class);
    }

    protected function createProject(string $name, ?float $priceFrom, ?float $priceTo): Project
    {
        $project = new Project();
        $project->forceFill([
            'name' => $name,
            'price_from' => $priceFrom,
            'price_to' => $priceTo,
            'status' => ProjectStatusEnum::SELLING,
        ]);
        $project->save();

        return $project;
    }

    public function testMaxPriceFiltersByPriceFrom(): void
    {
        $this->createProject('Affordable', 200000, 400000);
        $this->createProject('Expensive', 350000, 500000);

        $results = $this->repository->getProjects(
            ['max_price' => 300000],
            ['paginate' => ['per_page' => 50]]
        );

        $names = $results->pluck('name')->all();

        $this->assertContains('Affordable', $names);
        $this->assertNotContains('Expensive', $names);
    }

    public function testMinPriceFiltersByPriceTo(): void
    {
        $this->createProject('Low Range', 50000, 150000);
        $this->createProject('Mid Range', 100000, 500000);

        $results = $this->repository->getProjects(
            ['min_price' => 200000],
            ['paginate' => ['per_page' => 50]]
        );

        $names = $results->pluck('name')->all();

        $this->assertNotContains('Low Range', $names);
        $this->assertContains('Mid Range', $names);
    }

    public function testPriceRangeOverlap(): void
    {
        $this->createProject('Below Range', 50000, 100000);
        $this->createProject('Overlaps Low', 80000, 250000);
        $this->createProject('Within Range', 200000, 400000);
        $this->createProject('Overlaps High', 250000, 600000);
        $this->createProject('Above Range', 600000, 900000);

        $results = $this->repository->getProjects(
            ['min_price' => 150000, 'max_price' => 500000],
            ['paginate' => ['per_page' => 50]]
        );

        $names = $results->pluck('name')->all();

        $this->assertNotContains('Below Range', $names);
        $this->assertContains('Overlaps Low', $names);
        $this->assertContains('Within Range', $names);
        $this->assertContains('Overlaps High', $names);
        $this->assertNotContains('Above Range', $names);
    }

    public function testMaxPriceIncludesProjectsWithNullPriceFromWhenPriceToIsWithinRange(): void
    {
        $this->createProject('No Price From', null, 200000);
        $this->createProject('No Price From High', null, 500000);

        $results = $this->repository->getProjects(
            ['max_price' => 300000],
            ['paginate' => ['per_page' => 50]]
        );

        $names = $results->pluck('name')->all();

        $this->assertContains('No Price From', $names);
        $this->assertNotContains('No Price From High', $names);
    }

    public function testMinPriceIncludesProjectsWithNullPriceToWhenPriceFromIsAboveMin(): void
    {
        $this->createProject('No Price To High', 300000, null);
        $this->createProject('No Price To Low', 50000, null);

        $results = $this->repository->getProjects(
            ['min_price' => 200000],
            ['paginate' => ['per_page' => 50]]
        );

        $names = $results->pluck('name')->all();

        $this->assertContains('No Price To High', $names);
        $this->assertNotContains('No Price To Low', $names);
    }

    public function testBothPricesNullExcludedFromPriceFilter(): void
    {
        $this->createProject('No Prices', null, null);
        $this->createProject('Has Prices', 200000, 400000);

        $results = $this->repository->getProjects(
            ['min_price' => 100000, 'max_price' => 500000],
            ['paginate' => ['per_page' => 50]]
        );

        $names = $results->pluck('name')->all();

        $this->assertNotContains('No Prices', $names);
        $this->assertContains('Has Prices', $names);
    }

    public function testNoFiltersReturnsAllProjects(): void
    {
        $this->createProject('Project A', 100000, 200000);
        $this->createProject('Project B', 300000, 500000);

        $results = $this->repository->getProjects(
            [],
            ['paginate' => ['per_page' => 50]]
        );

        $this->assertCount(2, $results);
    }
}
