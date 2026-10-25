<div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
    @foreach($items as $recomendacion)
        <div class="min-w-0">
            @if($tienePreferencias)
                <div class="mb-2 flex min-h-7 flex-wrap gap-1.5">
                    @foreach($recomendacion['intereses'] as $interes)
                        <span class="rounded-full bg-primary-50 px-2.5 py-1 text-[11px] font-semibold text-primary-700">
                            {{ __('recommendations.matches_interest', ['interest' => $interes]) }}
                        </span>
                    @endforeach
                    @if($recomendacion['dentroPresupuesto'])
                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">{{ __('recommendations.within_budget') }}</span>
                    @endif
                </div>
            @else
                <div class="mb-2 flex min-h-7 items-center">
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">
                        {{ $tipo === 'atractivo' ? __('recommendations.general_attraction') : __('recommendations.general_pick') }}
                    </span>
                </div>
            @endif
            <x-catalog-card :item="$recomendacion['item']" :type="$tipo" />
        </div>
    @endforeach
</div>
