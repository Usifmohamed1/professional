<div class="form-group">
    <label for="select-bathroom" class="control-label">{{ trans('plugins/real-estate::filters.bathrooms') }}</label>
    <div class="select--arrow">
        <select name="bathroom" id="select-bathroom" class="form-control">
            <option value="">{{ trans('plugins/real-estate::filters.select') }}</option>
            @foreach([1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5] as $i)
                <option value="{{ $i }}" @if ((float) request()->input('bathroom') == $i) selected @endif>
                    {{ $i == (int) $i ? (int) $i : $i }} {{ $i == 1 ? trans('plugins/real-estate::filters.room') : trans('plugins/real-estate::filters.rooms') }}
                </option>
            @endforeach
            <option value="5" @if ((float) request()->input('bathroom') == 5) selected @endif>{{ trans('plugins/real-estate::filters.5_rooms') }}</option>
        </select>
        <i class="fas fa-angle-down"></i>
    </div>
</div>
