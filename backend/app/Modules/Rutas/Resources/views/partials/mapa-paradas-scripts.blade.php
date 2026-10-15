<script>
    (function () {
        if (window.TourismCloudRouteStopsMap) {
            return;
        }

        let googleMapsPromise;

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
                script.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(apiKey) + '&v=weekly&language=es&region=PE';
                script.async = true;
                script.defer = true;
                script.onload = function () {
                    if (window.google && window.google.maps) {
                        resolve(window.google.maps);
                        return;
                    }

                    reject(new Error('Google Maps no se inicializó correctamente.'));
                };
                script.onerror = function () {
                    reject(new Error('No se pudo cargar Google Maps.'));
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
                        : 'Parada ' + (indice + 1),
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
                titulo.textContent = 'Aún no hay paradas seleccionadas';
                texto.textContent = 'Agrega al menos dos atractivos para preparar la vista del recorrido.';
                estado.textContent = 'Sin paradas';
            } else if (ubicadas.length === 0) {
                titulo.textContent = 'Las paradas aún no tienen coordenadas';
                texto.textContent = 'La ruta conserva sus paradas, pero el mapa aparecerá cuando los atractivos publiquen latitud y longitud.';
                estado.textContent = 'Sin coordenadas';
            } else if (faltantes > 0) {
                titulo.textContent = faltantes === 1
                    ? 'Hay 1 parada sin coordenadas'
                    : 'Hay ' + faltantes + ' paradas sin coordenadas';
                texto.textContent = 'No se dibuja el recorrido hasta que todas las paradas publiquen su ubicación. Así se evita conectar puntos y presentar una ruta incompleta.';
                estado.textContent = faltantes === 1 ? '1 parada sin coordenadas' : faltantes + ' paradas sin coordenadas';
            } else if (ubicadas.length === 1) {
                titulo.textContent = 'Falta una segunda parada con coordenadas';
                texto.textContent = 'Hay una ubicación disponible, pero se requieren al menos dos para dibujar el trayecto.';
                estado.textContent = '1 punto ubicado';
            } else if (razon === 'sin-clave') {
                titulo.textContent = 'Google Maps aún no está configurado';
                texto.textContent = 'Las paradas ya están listas. Al agregar la clave y habilitar Maps JavaScript API y Routes API, se verá el recorrido interactivo.';
                estado.textContent = ubicadas.length + ' puntos listos';
            } else {
                titulo.textContent = 'No se pudo cargar el mapa interactivo';
                texto.textContent = 'Puedes abrir el recorrido en Google Maps mientras se revisa la configuración de la clave.';
                estado.textContent = ubicadas.length + ' puntos disponibles';
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
                    ? 'Abrir puntos disponibles en Google Maps'
                    : ubicadas.length > 1
                        ? 'Abrir recorrido en Google Maps'
                        : 'Abrir ubicación en Google Maps';
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
                        strokeColor: '#00626A',
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

                    estadoElemento.textContent = 'Trazando recorrido vial…';
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
                            estadoElemento.textContent = paradas.length + ' paradas trazadas';
                        })
                        .catch(function () {
                            if (versionActual === versionRenderizado) {
                                estadoElemento.textContent = paradas.length + ' paradas ubicadas · línea referencial';
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
                        estadoElemento.textContent = 'Cargando mapa';

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
                            estadoElemento.textContent = paradasUbicadas.length + ' paradas ubicadas · línea referencial';
                            intentarTrazadoVial(maps, paradasUbicadas, versionActual);
                        }
                    } catch (error) {
                        if (versionActual !== versionRenderizado) {
                            return;
                        }

                        console.warn('No se pudo inicializar el mapa de la ruta.', error);
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
