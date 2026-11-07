<ul{!! BaseHelper::clean($options) !!}>
    @foreach ($menu_nodes as $key => $row)
        @php
            $menuUrl = (string) $row->url;
            $normalizedTitle = \Illuminate\Support\Str::lower((string) $row->title);
            $isHiddenLandMenuItem = \Illuminate\Support\Str::contains($normalizedTitle, [
                'land',
                'lands',
                'أراضي',
                'اراضي',
                'أراض',
                'اراض',
                'ارض',
            ]);
            $propertyType = null;

            if ($isHiddenLandMenuItem) {
                continue;
            }

            if (\Illuminate\Support\Str::contains($normalizedTitle, ['للبيع', 'بيع', 'for sale', 'sale'])) {
                $propertyType = \Botble\RealEstate\Enums\PropertyTypeEnum::SALE;
            } elseif (\Illuminate\Support\Str::contains($normalizedTitle, ['للإيجار', 'للايجار', 'ايجار', 'إيجار', 'for rent', 'rent'])) {
                $propertyType = \Botble\RealEstate\Enums\PropertyTypeEnum::RENT;
            }

            if ($propertyType && str_contains($menuUrl, 'category_id=')) {
                $separator = str_contains($menuUrl, '?') ? '&' : '?';

                if (str_contains($menuUrl, 'type=')) {
                    $menuUrl = preg_replace('/([?&])type=[^&]*/', '$1type=' . $propertyType, $menuUrl);
                } else {
                    $menuUrl .= $separator . 'type=' . $propertyType;
                }
            }
        @endphp

        <li @class(['dropdown2' => $row->has_child, 'current' => $row->active, $row->css_class])>
            <a href="{{ $menuUrl }}" target="{{ $row->target }}">
                {!! BaseHelper::clean($row->icon_html) !!}
                {{ $row->title }}
            </a>

            @if ($row->has_child)
                {!! Menu::generateMenu([
                    'menu' => $menu,
                    'menu_nodes' => $row->child,
                    'view' => 'main-menu',
                ]) !!}
            @endif
        </li>
    @endforeach
</ul>
