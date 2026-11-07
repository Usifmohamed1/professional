<?php

namespace Botble\RealEstate\Events;

use Botble\RealEstate\Models\Property;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PropertyCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Property $property)
    {
    }
}
