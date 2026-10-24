@extends('layouts.app', ['title' => __('favorites.title')])
@section('content')
<div class="tc-catalog-shell max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" data-favorites-list>
    <header class="tc-page-heading"><h1>{{ __('favorites.title') }}</h1><p>{{ __('favorites.intro') }}</p></header>
    <nav class="tc-favorites-tabs" aria-label="{{ __('favorites.title') }}">
        <a href="{{ route('favoritos.index') }}" @if(!request('tipo')) aria-current="page" @endif>{{ __('favorites.all') }}</a>
        @foreach(\App\Modules\Usuarios\Services\FavoritoCatalogo::TIPOS as $tipo)
            <a href="{{ route('favoritos.index', ['tipo' => $tipo]) }}" @if(request('tipo') === $tipo) aria-current="page" @endif>{{ __('favorites.'.$tipo) }}</a>
        @endforeach
    </nav>
    @if($favoritos->isEmpty())
        <div class="tc-empty-state text-center p-10"><h2 class="text-2xl font-bold">{{ __('favorites.empty') }}</h2><p class="mt-3">{{ __('favorites.empty_hint') }}</p><a class="tc-btn-primary mt-6" href="{{ route('catalogo.atractivos.index') }}">{{ __('favorites.explore') }}</a></div>
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($favoritos as $favorito)<x-catalog-card :type="$favorito->tipo" :item="$favorito->recurso" />@endforeach
        </div>
        <div class="mt-8">{{ $favoritos->links() }}</div>
    @endif
</div>
@endsection
