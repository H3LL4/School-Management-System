@props(['name', 'label', 'options', 'selected' => null, 'placeholder' => 'Select…'])
<div {{ $attributes->only('class')->merge(['class' => 'mb-3']) }}>
    <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    <select id="{{ $name }}" name="{{ $name }}" {{ $attributes->except('class') }}
            class="form-select @error($name) is-invalid @enderror">
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as $value => $text)
            <option value="{{ $value }}" @selected((string) old($name, $selected) === (string) $value)>{{ $text }}</option>
        @endforeach
    </select>
    @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
