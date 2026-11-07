<?php

namespace Botble\RealEstate\Tests\Feature;

use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\Setting\Facades\Setting;
use Botble\Setting\Supports\SettingStore;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyExpirationSettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2025, 1, 1, 12, 0, 0));

        app(SettingStore::class)->forgetAll();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function testPropertyExpiredDaysReturnsDefaultWhenNotSet(): void
    {
        $days = RealEstateHelper::propertyExpiredDays();

        $this->assertEquals(45, $days);
    }

    public function testPropertyExpiredDaysReturnsZeroWhenSetToZero(): void
    {
        Setting::set('property_expired_after_days', 0);
        Setting::save();

        $days = RealEstateHelper::propertyExpiredDays();

        $this->assertEquals(0, $days);
    }

    public function testPropertyExpiredDaysReturnsConfiguredValue(): void
    {
        Setting::set('property_expired_after_days', 30);
        Setting::save();

        $days = RealEstateHelper::propertyExpiredDays();

        $this->assertEquals(30, $days);
    }

    public function testPropertyExpiredDaysReturnsDefaultWhenEmptyString(): void
    {
        Setting::set('property_expired_after_days', '');
        Setting::save();

        $days = RealEstateHelper::propertyExpiredDays();

        $this->assertEquals(45, $days);
    }

    public function testCalculatePropertyExpireDateReturnsNullWhenDaysIsZero(): void
    {
        Setting::set('property_expired_after_days', 0);
        Setting::save();

        $expireDate = RealEstateHelper::calculatePropertyExpireDate();

        $this->assertNull($expireDate);
    }

    public function testCalculatePropertyExpireDateReturnsDateWhenDaysIsPositive(): void
    {
        Setting::set('property_expired_after_days', 30);
        Setting::save();

        $expireDate = RealEstateHelper::calculatePropertyExpireDate();

        $this->assertInstanceOf(Carbon::class, $expireDate);
        $this->assertEquals(Carbon::now()->addDays(30)->toDateString(), $expireDate->toDateString());
    }

    public function testCalculatePropertyExpireDateUsesBaseDateWhenProvided(): void
    {
        Setting::set('property_expired_after_days', 30);
        Setting::save();

        $baseDate = Carbon::create(2025, 6, 15);
        $expireDate = RealEstateHelper::calculatePropertyExpireDate($baseDate);

        $this->assertInstanceOf(Carbon::class, $expireDate);
        $this->assertEquals('2025-07-15', $expireDate->toDateString());
    }

    public function testCalculatePropertyExpireDateReturnsNullWithBaseDateWhenDaysIsZero(): void
    {
        Setting::set('property_expired_after_days', 0);
        Setting::save();

        $baseDate = Carbon::create(2025, 6, 15);
        $expireDate = RealEstateHelper::calculatePropertyExpireDate($baseDate);

        $this->assertNull($expireDate);
    }

    public function testCalculatePropertyExpireDateUsesDefaultDaysWhenNotSet(): void
    {
        $expireDate = RealEstateHelper::calculatePropertyExpireDate();

        $this->assertInstanceOf(Carbon::class, $expireDate);
        $this->assertEquals(Carbon::now()->addDays(45)->toDateString(), $expireDate->toDateString());
    }

    public function testIsPropertyExpirationEnabledReturnsTrueWhenDaysIsPositive(): void
    {
        Setting::set('property_expired_after_days', 30);
        Setting::save();

        $this->assertTrue(RealEstateHelper::isPropertyExpirationEnabled());
    }

    public function testIsPropertyExpirationEnabledReturnsFalseWhenDaysIsZero(): void
    {
        Setting::set('property_expired_after_days', 0);
        Setting::save();

        $this->assertFalse(RealEstateHelper::isPropertyExpirationEnabled());
    }

    public function testIsPropertyExpirationEnabledReturnsTrueByDefault(): void
    {
        $this->assertTrue(RealEstateHelper::isPropertyExpirationEnabled());
    }
}
