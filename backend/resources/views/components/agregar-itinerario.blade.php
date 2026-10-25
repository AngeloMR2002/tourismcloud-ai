@props(['tipo', 'item'])
@if(auth()->user()?->isTurista())
    @php($disponibles = $itinerariosUsuario->where('destino_id', $item->destino_id))
    <section class="tc-card p-5">
        <p class="text-xs font-bold uppercase tracking-[0.13em] text-primary-700">{{ __('modules.add_to_itinerary') }}</p>
        @if($disponibles->isEmpty())
            <p class="mt-2 text-sm text-gray-500">{{ __('modules.no_itineraries_for_destination') }}</p>
            <a href="{{ route('itinerarios.create', ['destino_id' => $item->destino_id]) }}" class="tc-btn-outline mt-3 w-full justify-center">{{ __('modules.create_itinerary') }}</a>
        @else
            <form method="POST" action="{{ route('itinerarios.items.store', $disponibles->first()) }}" class="mt-3 space-y-3">
                @csrf
                <input type="hidden" name="entidad_tipo" value="{{ $tipo }}">
                <input type="hidden" name="entidad_id" value="{{ $item->id }}">
                <select name="itinerario_dia_id" required class="tc-select" aria-label="{{ __('modules.travel_day_label') }}" onchange="this.form.action = this.selectedOptions[0].dataset.action">
                    @foreach($disponibles as $itinerario)
                        <optgroup label="{{ $itinerario->nombre }}">
                            @foreach($itinerario->diasItinerario as $dia)
                                <option value="{{ $dia->id }}" data-action="{{ route('itinerarios.items.store', $itinerario) }}">{{ __('modules.day_number', ['number' => $dia->numero_dia]) }}{{ $dia->fecha ? ' · '.$dia->fecha->format('d/m/Y') : '' }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <div class="grid grid-cols-2 gap-2">
                    <input type="time" name="hora_inicio" required class="tc-input" aria-label="{{ __('modules.start_hour') }}">
                    <input type="time" name="hora_fin" required class="tc-input" aria-label="{{ __('modules.end_hour') }}">
                </div>
                @if($tipo === 'establecimiento')
                    <input type="number" name="costo_estimado" min="0" step="0.01" data-tc-money-input class="tc-input" placeholder="{{ __('modules.item_cost_pen') }}">
                @endif
                @if($errors->any())<p class="text-xs text-red-600">{{ $errors->first() }}</p>@endif
                <button type="submit" class="tc-btn-primary w-full justify-center">{{ __('modules.add_to_itinerary') }}</button>
            </form>
        @endif
    </section>
@endif