@switch($shortcode->display_type)
    @case('list')
        <div class="tf-faq">
            <ul class="box-faq box-faq--minimal" id="wrapper-faq">
                @foreach($faqs as $faq)
                    <li class="faq-item">
                        <a href="#accordion-faq-{{ $faq->getKey() }}" @class(['faq-header', 'collapsed' => ! ($loop->first && $expandFirst)]) data-bs-toggle="collapse" aria-expanded="{{ $loop->first && $expandFirst ? 'true' : 'false' }}" aria-controls="accordion-faq-{{ $faq->getKey() }}">
                            {!! BaseHelper::clean($faq->question) !!}
                        </a>
                        <div id="accordion-faq-{{ $faq->getKey() }}" @class(['collapse', 'show' => $loop->first && $expandFirst]) data-bs-parent="#wrapper-faq">
                            <div class="faq-body">
                                {!! BaseHelper::clean($faq->answer) !!}
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        @break

    @default
        @foreach($categories as $category)
            <div class="tf-faq">
                <h5>{{ $category->name }}</h5>
                <ul class="box-faq box-faq--minimal" id="wrapper-faq-{{ $categorySlug = Str::slug($category->name) }}">
                    @foreach($category->faqs as $faq)
                        <li class="faq-item">
                            <a href="#{{ $categorySlug }}-faq-{{ $faq->getKey() }}" @class(['faq-header', 'collapsed' => ! ($loop->parent->first && $loop->first && $expandFirst)]) data-bs-toggle="collapse" aria-expanded="{{ $loop->parent->first && $loop->first && $expandFirst ? 'true' : 'false' }}" aria-controls="{{ $categorySlug }}-faq-{{ $faq->getKey() }}">
                                {!! BaseHelper::clean($faq->question) !!}
                            </a>
                            <div id="{{ $categorySlug }}-faq-{{ $faq->getKey() }}" @class(['collapse', 'show' => $loop->parent->first && $loop->first && $expandFirst]) data-bs-parent="#wrapper-faq-{{ Str::slug($category->name) }}">
                                <div class="faq-body">
                                    {!! BaseHelper::clean($faq->answer) !!}
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
@endswitch
