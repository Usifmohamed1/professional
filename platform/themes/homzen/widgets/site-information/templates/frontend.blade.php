<div class="col-lg-4 col-md-6">
    <div class="footer-cl-1">
        <p class="text-variant-2">{!! BaseHelper::clean($config['about']) !!}</p>
        @if($items->isNotEmpty())
            <ul class="mt-12">
                @foreach($items as $item)
                    @php
                        $text = trim((string) $item['text']);
                        $plainText = trim(strip_tags($text));
                        $normalizedPhone = preg_replace('/[^0-9+]/', '', $plainText);
                        $whatsappNumber = preg_replace('/[^0-9]/', '', $plainText);
                        $icon = (string) $item['icon'];
                        $href = null;

                        if (filter_var($plainText, FILTER_VALIDATE_EMAIL)) {
                            $href = 'mailto:' . $plainText;
                        } elseif (str_contains($icon, 'whatsapp') && $whatsappNumber) {
                            if (str_starts_with($whatsappNumber, '0')) {
                                $whatsappNumber = '20' . substr($whatsappNumber, 1);
                            } elseif (! str_starts_with($whatsappNumber, '2')) {
                                $whatsappNumber = '2' . $whatsappNumber;
                            }
                            $href = 'https://wa.me/+' . $whatsappNumber;
                        } elseif (str_contains($icon, 'phone') && $normalizedPhone) {
                            if (str_starts_with($normalizedPhone, '0')) {
                                $normalizedPhone = '20' . substr($normalizedPhone, 1);
                            }
                            $href = 'tel:+' . $normalizedPhone;
                        }
                    @endphp

                    <li class="mt-12 d-flex align-items-center gap-8">
                        <x-core::icon :name="$item['icon']" class="text-variant-2" style="width: 1.25rem; height: 1.25rem; flex-shrink: 0;" />
                        @if($href)
                            <a href="{{ $href }}" class="text-white" target="_blank" rel="noopener noreferrer">
                                {!! BaseHelper::clean(nl2br($item['text'])) !!}
                            </a>
                        @else
                            <p class="text-white">{!! BaseHelper::clean(nl2br($item['text'])) !!}</p>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
