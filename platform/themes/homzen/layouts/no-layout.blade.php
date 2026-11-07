@extends(Theme::getThemeNamespace('layouts.base'))

@section('content')
    <main id="main-content" tabindex="-1">
        {!! Theme::content() !!}
    </main>
@endsection
