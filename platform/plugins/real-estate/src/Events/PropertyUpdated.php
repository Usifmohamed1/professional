<?php

namespace Botble\RealEstate\Events;

use Botble\RealEstate\Models\Property;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PropertyUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Property $property,
        public array $originalImages = []
    ) {
    }
}
