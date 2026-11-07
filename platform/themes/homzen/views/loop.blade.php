<section class="flat-section editorial-journal">
    {!! apply_filters('ads_render', null, 'blog_list_before') !!}

    <div class="row">
        <div class="col-lg-9 editorial-journal-main">
            <div class="flat-blog-list editorial-journal-grid">
                @forelse($posts as $post)
                    <article class="flat-blog-item editorial-story">
                        @if ($post->image)
                            <a class="img-style editorial-story-image" href="{{ $post->url }}">
                                {{ RvMedia::image($post->image, $post->name) }}
                                <span class="date-post">{{ Theme::formatDate($post->created_at) }}</span>
                            </a>
                        @endif
                        <div class="content-box">
                            <div class="post-author">
                                @if (theme_option('blog_show_author_name', 'yes') == 'yes' && class_exists($post->author_type) && ($author = $post->author ?? null) && trim($author->name))
                                    <span class="text-black fw-7">{{ $author->name }}</span>
                                @endif

                                @if($category = $post->firstCategory)
                                    <span>
                                        <a href="{{ $category->url }}">{{ $category->name }}</a>
                                    </span>
                                @endif

                                @if (! $post->image)
                                    <span>{{ Theme::formatDate($post->created_at) }}</span>
                                @endif
                            </div>
                            <h3 class="title">
                                <a href="{{ $post->url }}">
                                    {!! BaseHelper::clean($post->name) !!}
                                </a>
                            </h3>
                            @if($post->description)
                                <p class="description body-1">{!! BaseHelper::clean(Str::limit($post->description)) !!}</p>
                            @endif
                            <a href="{{ $post->url }}" class="btn-read-more">{{ __('Read the story') }} <span aria-hidden="true">↗</span></a>
                        </div>
                    </article>
                @empty
                    <div class="editorial-empty" role="status">
                        <p class="editorial-eyebrow">{{ __('The journal') }}</p>
                        <h2>{{ __('No stories to show yet.') }}</h2>
                        <p>{{ __('Please explore another category or visit us again soon.') }}</p>
                    </div>
                @endforelse

                {{ $posts->onEachSide(1)->links(Theme::getThemeNamespace('partials.pagination')) }}
            </div>
        </div>
        <div class="col-lg-3">
            <aside class="sidebar-blog editorial-journal-sidebar">
                {!! dynamic_sidebar('blog_sidebar') !!}
            </aside>
        </div>
    </div>

    {!! apply_filters('ads_render', null, 'blog_list_after') !!}
</section>
