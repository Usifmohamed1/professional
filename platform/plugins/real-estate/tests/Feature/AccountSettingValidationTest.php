<?php

namespace Botble\RealEstate\Tests\Feature;

use Botble\RealEstate\Http\Requests\Settings\AccountSettingRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class AccountSettingValidationTest extends TestCase
{
    protected function getValidationRules(): array
    {
        return (new AccountSettingRequest())->rules();
    }

    public function testPropertyExpiredAfterDaysAcceptsZero(): void
    {
        $rules = $this->getValidationRules();
        $data = ['property_expired_after_days' => 0];

        $validator = Validator::make($data, [
            'property_expired_after_days' => $rules['property_expired_after_days'],
        ]);

        $this->assertTrue($validator->passes(), 'Validation should pass when property_expired_after_days is 0');
    }

    public function testPropertyExpiredAfterDaysAcceptsPositiveInteger(): void
    {
        $rules = $this->getValidationRules();
        $data = ['property_expired_after_days' => 30];

        $validator = Validator::make($data, [
            'property_expired_after_days' => $rules['property_expired_after_days'],
        ]);

        $this->assertTrue($validator->passes(), 'Validation should pass when property_expired_after_days is positive');
    }

    public function testPropertyExpiredAfterDaysRejectsNegativeValue(): void
    {
        $rules = $this->getValidationRules();
        $data = ['property_expired_after_days' => -1];

        $validator = Validator::make($data, [
            'property_expired_after_days' => $rules['property_expired_after_days'],
        ]);

        $this->assertFalse($validator->passes(), 'Validation should fail when property_expired_after_days is negative');
    }

    public function testPropertyExpiredAfterDaysIsRequired(): void
    {
        $rules = $this->getValidationRules();
        $data = [];

        $validator = Validator::make($data, [
            'property_expired_after_days' => $rules['property_expired_after_days'],
        ]);

        $this->assertFalse($validator->passes(), 'Validation should fail when property_expired_after_days is missing');
    }

    public function testPropertyExpiredAfterDaysMustBeInteger(): void
    {
        $rules = $this->getValidationRules();
        $data = ['property_expired_after_days' => 'abc'];

        $validator = Validator::make($data, [
            'property_expired_after_days' => $rules['property_expired_after_days'],
        ]);

        $this->assertFalse($validator->passes(), 'Validation should fail when property_expired_after_days is not integer');
    }

    public function testPropertyExpiredAfterDaysAcceptsLargeValue(): void
    {
        $rules = $this->getValidationRules();
        $data = ['property_expired_after_days' => 365];

        $validator = Validator::make($data, [
            'property_expired_after_days' => $rules['property_expired_after_days'],
        ]);

        $this->assertTrue($validator->passes(), 'Validation should pass for large values like 365 days');
    }
}
