@extends('layouts.app', ['title' => __('operator.new_activity')])

@section('content')
    <div class="tc-workspace max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        <div class="tc-page-heading">
            <div>
                <a href="{{ route('operador.actividades.index') }}" class="text-sm font-semibold hover:underline" style="color: var(--color-primary-600);">← {{ __('operator.my_activities') }}</a>
                <h1>{{ __('operator.new_activity') }}</h1>
                <p>{{ __('operator.new_activity_intro') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('operador.actividades.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @include('actividades::operador._form', ['actividad' => null, 'destinos' => $destinos, 'submitLabel' => __('operator.create_activity')])
        </form>
    </div>
@endsection
