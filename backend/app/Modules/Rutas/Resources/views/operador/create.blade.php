@extends('layouts.app', ['title' => __('operator_routes.new_route')])

@section('content')
<div class="tc-workspace mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="tc-page-heading">
        <div>
            <a href="{{ route('operador.rutas.index') }}" class="text-sm font-semibold text-primary-700 hover:text-primary-900">← {{ __('operator_routes.back_to_routes') }}</a>
            <h1>{{ __('operator_routes.create_tourist_route') }}</h1>
            <p>{{ __('operator_routes.create_intro') }}</p>
        </div>
    </div>

    @include('rutas::operador._form')
</div>
@endsection
