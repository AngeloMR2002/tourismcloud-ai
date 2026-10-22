@props(['type', 'item', 'inline' => false])
@php($saved = isset($favoriteKeys[$type.':'.$item->getKey()]))
<div {{ $attributes->class(['tc-favorite-wrap', 'tc-favorite-wrap--inline' => $inline]) }}>
    @auth
        <form method="POST" action="{{ route('favoritos.store', [$type, $item->getKey()]) }}" data-favorite-form data-key="{{ $type.':'.$item->getKey() }}" data-save-label="{{ __('favorites.save') }}" data-remove-label="{{ __('favorites.remove') }}" data-error="{{ __('favorites.error') }}">
            @csrf
            <input type="hidden" name="_method" value="{{ $saved ? 'DELETE' : 'POST' }}">
            <button type="submit" class="tc-heart" aria-pressed="{{ $saved ? 'true' : 'false' }}" aria-label="{{ $saved ? __('favorites.remove') : __('favorites.save') }}" title="{{ $saved ? __('favorites.remove') : __('favorites.save') }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"/></svg>
            </button>
            <span role="status" class="tc-favorite-message" hidden></span>
        </form>
    @else
        <a href="{{ route('favoritos.confirmar', [$type, $item->getKey()]) }}" class="tc-heart" aria-label="{{ __('favorites.login') }}" title="{{ __('favorites.login') }}">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"/></svg>
        </a>
    @endauth
</div>
