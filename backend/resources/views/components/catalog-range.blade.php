@props(['name', 'label', 'values' => [], 'money' => false, 'ceiling' => 14400])
<fieldset class="tc-range" data-catalog-range data-money="{{ $money ? 'true' : 'false' }}" data-error="{{ __('favorites.range_error') }}" data-unlimited="{{ __('favorites.unlimited') }}">
    <legend @if($money) data-tc-money-label @endif>{{ $label }}</legend>
    <input type="hidden" data-range-ceiling @if($money) data-tc-money-input @endif value="{{ $ceiling }}" disabled>
    <div class="tc-range__labels" data-tc-currency-ignore><output data-range-summary-min></output><output data-range-summary-max></output></div>
    <div class="tc-range__track">
        <div class="tc-range__fill"></div>
        <input type="range" min="0" max="1000" value="0" step="1" data-range-handle="min" aria-label="{{ $label }} — {{ __('favorites.minimum') }}">
        <input type="range" min="0" max="1000" value="1000" step="1" data-range-handle="max" aria-label="{{ $label }} — {{ __('favorites.maximum') }}">
    </div>
    <input type="hidden" name="{{ $name.'_min' }}" value="{{ $values[$name.'_min'] ?? '' }}" data-range-value="min" @if($money) data-tc-money-input @endif>
    <input type="hidden" name="{{ $name.'_max' }}" value="{{ $values[$name.'_max'] ?? '' }}" data-range-value="max" @if($money) data-tc-money-input @endif>
</fieldset>
