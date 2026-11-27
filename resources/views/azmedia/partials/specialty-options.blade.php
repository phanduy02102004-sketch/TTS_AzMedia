@foreach(config('internships.fields') as $key => $field)
    <optgroup label="{{ $field['name'] }}">
        @if($includeFields ?? false)
            <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>Tất cả ngành {{ $field['name'] }}</option>
        @endif
        @foreach($field['specialties'] as $category => $label)
            <option value="{{ $category }}" {{ ($includeFields ?? false) && request('category') === $category ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </optgroup>
@endforeach
