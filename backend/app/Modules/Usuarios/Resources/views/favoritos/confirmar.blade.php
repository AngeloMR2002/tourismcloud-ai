@extends('layouts.app', ['title' => __('favorites.confirm')])
@section('content')
<div class="max-w-xl mx-auto px-5 py-10">
    <header class="tc-page-heading mb-6"><h1>{{ __('favorites.confirm') }}</h1><p>{{ __('favorites.confirm_intro') }}</p></header>
    <x-catalog-card :type="$tipo" :item="$recurso" />
    <form method="POST" action="{{ route('favoritos.store', [$tipo, $recurso->id]) }}" class="mt-6">
        @csrf
        <button class="tc-btn-primary w-full justify-center">{{ __('favorites.save') }}</button>
    </form>
    <a class="block mt-4 text-center underline" href="{{ route('favoritos.index') }}">{{ __('favorites.title') }}</a>
</div>
@endsection
