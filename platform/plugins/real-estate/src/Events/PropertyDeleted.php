<?php

namespace Botble\RealEstate\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PropertyDeleted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public array $propertyData)
    {
    }
}
