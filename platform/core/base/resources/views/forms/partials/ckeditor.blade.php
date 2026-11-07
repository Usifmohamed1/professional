@php
    Assets::addScriptsDirectly(config('core.base.general.editor.ckeditor.js'))->addScriptsDirectly(
        'vendor/core/core/base/js/editor.js',
    );

    if (BaseHelper::getRichEditor() == 'ckeditor' && App::getLocale() != 'en') {
        Assets::addScriptsDirectly(
            sprintf('https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/translations/%s.js', App::getLocale()),
        );
    }

    $attributes['class'] = Arr::get($attributes, 'class', '') . ' form-control editor-ckeditor ays-ignore';
    $attributes['id'] = Arr::get($attributes, 'id', $name);
    $attributes['rows'] = Arr::get($attributes, 'rows', 4);
@endphp

@once
    <script>
        document.addEventListener('core-editor-init', (event) => {
            const editorManagement = event.detail
            const registerInitialCallback = editorManagement.ckEditorInitialUsing.bind(editorManagement)

            editorManagement.ckEditorInitialUsing = function (editorOrCallback) {
                if (typeof editorOrCallback === 'function') {
                    return registerInitialCallback(editorOrCallback)
                }

                const editor = editorOrCallback
                editor.execute('heading', { value: 'heading5' })
                editor.execute('selectAll')
                editor.execute('fontSize', { value: 17 })
                editor.model.change((writer) => {
                    writer.setSelection(editor.model.document.getRoot(), 'end')
                })
                editor.execute('fontSize', { value: 17 })

                return editor
            }
        })
    </script>
@endonce

{!! Form::textarea($name, BaseHelper::cleanEditorContent($value), $attributes) !!}
