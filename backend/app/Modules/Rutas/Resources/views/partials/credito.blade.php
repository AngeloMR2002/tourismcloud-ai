@php($urlPortada = $ruta->portada_url)
@if($urlPortada && str_contains($urlPortada, '/actividades-rutas-demo/cusco-sacsayhuaman.jpg'))
    <p class="px-4 py-2 text-xs text-gray-500">Foto de contexto:
        <a href="https://commons.wikimedia.org/wiki/File:Sacsayhuaman_in_Cusco.jpg" target="_blank" rel="noopener noreferrer" class="underline">Filipe Fortes</a>,
        <a href="https://creativecommons.org/licenses/by-sa/2.0/" target="_blank" rel="noopener noreferrer" class="underline">CC BY-SA 2.0</a>.
    </p>
@elseif($urlPortada && str_contains($urlPortada, '/actividades-rutas-demo/cusco-plaza.jpg'))
    <p class="px-4 py-2 text-xs text-gray-500">Foto de contexto:
        <a href="https://commons.wikimedia.org/wiki/File:The_Plaza_de_Armas_of_Cusco.jpg" target="_blank" rel="noopener noreferrer" class="underline">Entropy1963</a>, dominio público.
    </p>
@endif
