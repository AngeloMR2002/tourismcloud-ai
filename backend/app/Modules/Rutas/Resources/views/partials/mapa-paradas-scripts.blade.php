<script>
    (function () {
        if (window.TourismCloudRouteStopsMap) {
            return;
        }

        let googleMapsPromise;
        const copy = @json(trans('maps'));
        const locale = @json(app()->getLocale());
        const withCount = (message, count) => message.replace(':count', String(count));

        function cargarGoogleMaps(apiKey) {
            if (!apiKey) {
                return Promise.resolve(null);
            }

            if (window.google && window.google.maps) {
                return Promise.resolve(window.google.maps);
            }

            if (googleMapsPromise) {
                return googleMapsPromise;
            }

            googleMapsPromise = new Promise(function (resolve, reject) {
                const script = document.createElement('script');
                script.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(apiKey) + '&v=weekly&language=' + encodeURIComponent(locale) + '&region=PE';
                script.async = true;
                script.defer = true;
                script.onload = function () {
                    if (window.google && window.google.maps) {
                        resolve(window.google.maps);
                        return;
                    }

                    reject(new Error(copy.maps_init_failed));
                };
                script.onerror = function () {
                    reject(new Error(copy.maps_load_failed));
                };
                document.head.appendChild(script);
            });

            return googleMapsPromise;
        }

        function esCoordenadaValida(latitud, longitud) {
            return Number.isFinite(latitud)
                && Number.isFinite(longitud)
                && latitud >= -90
                && latitud <= 90
                && longitud >= -180
                && longitud <= 180;
        }

        function convertirCoordenada(valor) {
            if (valor === null || typeof valor === 'undefined') {
                return Number.NaN;
            }

            if (typeof valor === 'string' && valor.trim() === '') {
                return Number.NaN;
            }

            return Number(valor);
        }

        function normalizarParadas(paradas) {
            return (Array.isArray(paradas) ? paradas : []).map(function (parada, indice) {
                const latitud = convertirCoordenada(parada.latitud);
                const longitud = convertirCoordenada(parada.longitud);

                return {
                    orden: Number(parada.orden) || indice + 1,
                    nombre: typeof parada.nombre === 'string' && parada.nombre.trim() !== ''
                        ? parada.nombre.trim()
                        : copy.stop_number.replace(':number', String(indice + 1)),
                    latitud: latitud,
                    longitud: longitud,
                    tieneCoordenadas: esCoordenadaValida(latitud, longitud),
                };
            });
        }

        function crearUrlGoogleMaps(paradas) {
            const ubicadas = paradas.filter(function (parada) {
                return parada.tieneCoordenadas;
            });

            if (ubicadas.length === 0) {
                return null;
            }

            if (ubicadas.length === 1) {
                return 'https://maps.google.com/?q=' + encodeURIComponent(ubicadas[0].latitud + ',' + ubicadas[0].longitud);
            }

            const origen = ubicadas[0].latitud + ',' + ubicadas[0].longitud;
            const destino = ubicadas[ubicadas.length - 1].latitud + ',' + ubicadas[ubicadas.length - 1].longitud;
            const url = new URL('https://www.google.com/maps/dir/');
            url.searchParams.set('api', '1');
            url.searchParams.set('origin', origen);
            url.searchParams.set('destination', destino);

            if (ubicadas.length > 2) {
                url.searchParams.set('waypoints', ubicadas.slice(1, -1).map(function (parada) {
                    return parada.latitud + ',' + parada.longitud;
                }).join('|'));
            }

            return url.toString();
        }

        function vaciar(elemento) {
            while (elemento.firstChild) {
                elemento.removeChild(elemento.firstChild);
            }
        }

        function mostrarRespaldo(elemento, estado, paradas, razon) {
            vaciar(elemento);

            const ubicadas = paradas.filter(function (parada) {
                return parada.tieneCoordenadas;
            });
            const faltantes = paradas.length - ubicadas.length;
            const contenido = document.createElement('div');
            contenido.className = 'max-w-md text-center';
            const icono = document.createElement('div');
            icono.className = 'mx-auto grid h-10 w-10 place-items-center rounded-full bg-white text-lg font-extrabold text-primary-700 shadow-sm';
            icono.textContent = String(ubicadas.length);
            const titulo = document.createElement('p');
            titulo.className = 'mt-3 font-semibold text-slate-800';
            const texto = document.createElement('p');
            texto.className = 'mt-1 text-sm leading-6 text-slate-500';

            if (paradas.length === 0) {
                titulo.textContent = copy.no_stops_selected;
                texto.textContent = copy.add_two_attractions;
                estado.textContent = copy.no_stops;
            } else if (ubicadas.length === 0) {
                titulo.textContent = copy.stops_without_coordinates;
                texto.textContent = copy.stops_without_coordinates_help;
                estado.textContent = copy.no_coordinates;
            } else if (faltantes > 0) {
                titulo.textContent = faltantes === 1
                    ? copy.missing_coordinate_one
                    : withCount(copy.missing_coordinate_many, faltantes);
                texto.textContent = copy.incomplete_route_note;
                estado.textContent = faltantes === 1 ? copy.missing_coordinate_status_one : withCount(copy.missing_coordinate_status_many, faltantes);
            } else if (ubicadas.length === 1) {
                titulo.textContent = copy.second_stop_needed;
                texto.textContent = copy.second_stop_help;
                estado.textContent = copy.one_point;
            } else if (razon === 'sin-clave') {
                titulo.textContent = copy.maps_not_configured;
                texto.textContent = copy.maps_not_configured_help;
                estado.textContent = withCount(copy.points_ready, ubicadas.length);
            } else {
                titulo.textContent = copy.interactive_map_failed;
                texto.textContent = copy.open_route_help;
                estado.textContent = withCount(copy.points_available, ubicadas.length);
            }

            contenido.append(icono, titulo, texto);
            const url = crearUrlGoogleMaps(paradas);
            if (url) {
                const enlace = document.createElement('a');
                enlace.href = url;
                enlace.target = '_blank';
                enlace.rel = 'noopener noreferrer';
                enlace.className = 'mt-4 inline-flex items-center gap-2 rounded-lg border border-primary-200 bg-white px-3 py-2 text-sm font-bold text-primary-700 transition hover:bg-primary-50';
                enlace.textContent = faltantes > 0
                    ? copy.open_available_points
                    : ubicadas.length > 1
                        ? copy.open_route
                        : copy.open_location;
                contenido.appendChild(enlace);
            }

            elemento.appendChild(contenido);
        }

        window.TourismCloudRouteStopsMap = {
            create: function (opciones) {
                const mapaElemento = document.getElementById(opciones.mapId);
                const respaldoElemento = document.getElementById(opciones.mapId + '-fallback');
                const estadoElemento = document.getElementById(opciones.mapId + '-status');
                const apiKey = typeof opciones.apiKey === 'string' ? opciones.apiKey.trim() : '';
                let mapa = null;
                let marcadores = [];
                let polilineas = [];
                let paradasActuales = [];
                let versionRenderizado = 0;

                function limpiarMapa() {
                    marcadores.forEach(function (marcador) {
                        marcador.setMap(null);
                    });
                    polilineas.forEach(function (polilinea) {
                        polilinea.setMap(null);
                    });
                    marcadores = [];
                    polilineas = [];
                }

                function dibujarLineaDirecta(maps, paradas) {
                    const linea = new maps.Polyline({
                        path: paradas.map(function (parada) {
                            return { lat: parada.latitud, lng: parada.longitud };
                        }),
                        geodesic: true,
                        strokeColor: '#16734f',
                        strokeOpacity: 0.8,
                        strokeWeight: 4,
                    });
                    linea.setMap(mapa);
                    polilineas.push(linea);
                }

                function intentarTrazadoVial(maps, paradas, versionActual) {
                    if (paradas.length < 2 || paradas.length > 27 || typeof maps.importLibrary !== 'function') {
                        return;
                    }

                    estadoElemento.textContent = copy.tracing_road;
                    maps.importLibrary('routes')
                        .then(function (biblioteca) {
                            return biblioteca.Route.computeRoutes({
                                origin: { lat: paradas[0].latitud, lng: paradas[0].longitud },
                                destination: { lat: paradas[paradas.length - 1].latitud, lng: paradas[paradas.length - 1].longitud },
                                intermediates: paradas.slice(1, -1).map(function (parada) {
                                    return {
                                        location: { lat: parada.latitud, lng: parada.longitud },
                                    };
                                }),
                                travelMode: 'DRIVING',
                                fields: ['path'],
                            });
                        })
                        .then(function (resultado) {
                            if (versionActual !== versionRenderizado || !resultado.routes || !resultado.routes[0]) {
                                return;
                            }

                            const nuevasPolilineas = resultado.routes[0].createPolylines();
                            if (!nuevasPolilineas || nuevasPolilineas.length === 0) {
                                return;
                            }

                            polilineas.forEach(function (polilinea) {
                                polilinea.setMap(null);
                            });
                            polilineas = nuevasPolilineas;
                            polilineas.forEach(function (polilinea) {
                                polilinea.setMap(mapa);
                            });
                            estadoElemento.textContent = withCount(copy.stops_traced, paradas.length);
                        })
                        .catch(function () {
                            if (versionActual === versionRenderizado) {
                                estadoElemento.textContent = withCount(copy.stops_positioned, paradas.length);
                            }
                        });
                }

                async function actualizar(paradas) {
                    paradasActuales = normalizarParadas(paradas);
                    const paradasUbicadas = paradasActuales.filter(function (parada) {
                        return parada.tieneCoordenadas;
                    });
                    const hayParadasSinCoordenadas = paradasUbicadas.length !== paradasActuales.length;
                    const versionActual = ++versionRenderizado;

                    if (!mapaElemento || !respaldoElemento || !estadoElemento) {
                        return;
                    }

                    if (hayParadasSinCoordenadas || paradasUbicadas.length < 2 || !apiKey) {
                        mapaElemento.classList.add('hidden');
                        respaldoElemento.classList.remove('hidden');
                        mostrarRespaldo(respaldoElemento, estadoElemento, paradasActuales, apiKey ? 'sin-coordenadas' : 'sin-clave');
                        return;
                    }

                    try {
                        const maps = await cargarGoogleMaps(apiKey);
                        if (versionActual !== versionRenderizado || !maps) {
                            return;
                        }

                        respaldoElemento.classList.add('hidden');
                        mapaElemento.classList.remove('hidden');
                        estadoElemento.textContent = copy.loading_map;

                        if (!mapa) {
                            mapa = new maps.Map(mapaElemento, {
                                center: { lat: paradasUbicadas[0].latitud, lng: paradasUbicadas[0].longitud },
                                zoom: 13,
                                mapTypeControl: false,
                                streetViewControl: false,
                                fullscreenControl: false,
                            });
                        }

                        limpiarMapa();
                        const limites = new maps.LatLngBounds();
                        paradasUbicadas.forEach(function (parada) {
                            const posicion = { lat: parada.latitud, lng: parada.longitud };
                            limites.extend(posicion);
                            const marcador = new maps.Marker({
                                map: mapa,
                                position: posicion,
                                label: String(parada.orden),
                                title: parada.orden + '. ' + parada.nombre,
                            });
                            marcadores.push(marcador);
                        });

                        if (paradasUbicadas.length === 1) {
                            mapa.setCenter({ lat: paradasUbicadas[0].latitud, lng: paradasUbicadas[0].longitud });
                            mapa.setZoom(15);
                        } else {
                            mapa.fitBounds(limites, 48);
                            dibujarLineaDirecta(maps, paradasUbicadas);
                            estadoElemento.textContent = withCount(copy.stops_positioned, paradasUbicadas.length);
                            intentarTrazadoVial(maps, paradasUbicadas, versionActual);
                        }
                    } catch (error) {
                        if (versionActual !== versionRenderizado) {
                            return;
                        }

                        console.warn(copy.route_map_init_failed, error);
                        mapaElemento.classList.add('hidden');
                        respaldoElemento.classList.remove('hidden');
                        mostrarRespaldo(respaldoElemento, estadoElemento, paradasActuales, 'error');
                    }
                }

                return {
                    actualizar: actualizar,
                };
            },
        };
    })();
</script>
