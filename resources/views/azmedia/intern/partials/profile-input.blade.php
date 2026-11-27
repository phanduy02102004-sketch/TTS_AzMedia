<div class="{{ $field['span'] ?? '' }}">
    <label for="{{ $field['name'] }}" class="block text-xs font-bold text-slate-700 mb-2">
        {{ $field['label'] }} @if(!empty($field['required']))<span class="text-rose-500">*</span>@endif
    </label>
    <input id="{{ $field['name'] }}" name="{{ $field['name'] }}" type="{{ $field['type'] ?? 'text' }}"
           value="{{ old($field['name'], $candidate->{$field['name']}) }}"
           @if(isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
           @foreach(['step', 'min', 'max'] as $attribute) @if(isset($field[$attribute])) {{ $attribute }}="{{ $field[$attribute] }}" @endif @endforeach
           @if(!empty($field['required'])) required @endif
           class="w-full rounded-xl border {{ $errors->has($field['name']) ? 'border-rose-400 bg-rose-50' : 'border-slate-300' }} px-3.5 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-brand-500 focus:ring-brand-500">
    @error($field['name'])<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
</div>
