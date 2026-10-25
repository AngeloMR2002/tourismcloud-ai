@extends('layouts.app', ['title' => __('recommendations.title')])

@section('content')
<main class="tc-workspace mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <header class="tc-page-heading flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-primary-700">{{ __('recommendations.eyebrow') }}</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ __('recommendations.title') }}</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                {{ $tienePreferencias ? __('recommendations.personalized_intro') : __('recommendations.general_intro') }}
            </p>
        </div>
        @if($tienePreferencias)
            <a href="{{ route('preferencias.turista', auth()->id()) }}" class="tc-btn-outline justify-center">
                {{ __('recommendations.review_preferences') }}
            </a>
        @else
            <a href="{{ route('preferencias.create') }}" class="tc-btn-primary justify-center">
                {{ __('recommendations.set_preferences') }}
            </a>
        @endif
    </header>

    @if($tienePreferencias && $categoriasPreferidas->isNotEmpty())
        <section class="tc-card mt-6 p-5 sm:p-6" aria-labelledby="recommendation-interests-heading">
            <h2 id="recommendation-interests-heading" class="text-sm font-bold text-slate-900">
                {{ __('recommendations.your_interests') }}
            </h2>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($categoriasPreferidas as $categoria)
                    <span class="rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700">{{ $categoria }}</span>
                @endforeach
            </div>
            <p class="mt-3 text-sm leading-6 text-slate-500">{{ __('recommendations.interests_hint') }}</p>
        </section>
    @endif

    @if($recomendacionesAtractivos->isEmpty() && $recomendacionesActividades->isEmpty())
        <section class="tc-card mt-6 px-6 py-12 text-center">
            <h2 class="text-lg font-semibold text-slate-900">{{ __('recommendations.empty_title') }}</h2>
            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">{{ __('recommendations.empty_intro') }}</p>
            <div class="mt-5 flex flex-wrap justify-center gap-3">
                <a href="{{ route('catalogo.atractivos.index') }}" class="tc-btn-outline">{{ __('recommendations.view_attractions') }}</a>
                <a href="{{ route('catalogo.actividades.index') }}" class="tc-btn-outline">{{ __('recommendations.explore_activities') }}</a>
            </div>
        </section>
    @else
        @if($recomendacionesAtractivos->isNotEmpty())
            <section class="mt-7" aria-labelledby="recommendation-attractions-heading">
                <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 id="recommendation-attractions-heading" class="text-xl font-bold text-slate-900">{{ __('recommendations.attractions_heading') }}</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ trans_choice('recommendations.attraction_count', $recomendacionesAtractivos->count(), ['count' => $recomendacionesAtractivos->count()]) }}
                        </p>
                    </div>
                    <a href="{{ route('catalogo.atractivos.index') }}" class="text-sm font-semibold text-primary-700 hover:underline">
                        {{ __('recommendations.view_attractions') }} →
                    </a>
                </div>
                @include('recomendaciones::partials.cards', ['items' => $recomendacionesAtractivos, 'tipo' => 'atractivo'])
            </section>
        @endif

        @if($recomendacionesActividades->isNotEmpty())
            <section class="mt-8" aria-labelledby="recommendation-activities-heading">
                <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 id="recommendation-activities-heading" class="text-xl font-bold text-slate-900">{{ __('recommendations.activities_heading') }}</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ trans_choice('recommendations.activity_count', $recomendacionesActividades->count(), ['count' => $recomendacionesActividades->count()]) }}
                        </p>
                    </div>
                    <a href="{{ route('catalogo.actividades.index') }}" class="text-sm font-semibold text-primary-700 hover:underline">
                        {{ __('recommendations.view_all') }} →
                    </a>
                </div>
                @include('recomendaciones::partials.cards', ['items' => $recomendacionesActividades, 'tipo' => 'actividad'])
            </section>
        @endif
    @endif
</main>
@endsection
