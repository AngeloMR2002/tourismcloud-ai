<?php

namespace App\Modules\Recomendaciones\Contracts;

class ContratoComprensionRecomendacion
{
    /**
     * JSON Schema utilizado para las salidas estructuradas
     * de Anthropic.
     */
    public static function schema(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,

            'required' => [
                'estado',
                'criterios',
                'solicitudes_adicionales',
                'observaciones',
            ],

            'properties' => [
                'estado' => [
                    'type' => 'string',
                    'enum' => [
                        'ok',
                        'requiere_validacion',
                        'invalido',
                    ],
                ],

                'criterios' => [
                    'type' => 'object',
                    'additionalProperties' => false,

                    'required' => [
                        'dias',
                        'presupuesto_min',
                        'presupuesto_max',
                        'ritmo',
                        'hora_inicio',
                        'hora_fin',
                        'categorias',
                        'costo_max_actividad',
                        'costo_min_actividad',
                        'duracion_min_actividad',
                        'duracion_max_actividad',
                        'preferir_menor_distancia',
                    ],

                    'properties' => [
                        'dias' => self::criterioEntero(),

                        'presupuesto_min' =>
                            self::criterioDecimal(),

                        'presupuesto_max' =>
                            self::criterioDecimal(),

                        'ritmo' => [
                            'type' => 'object',
                            'additionalProperties' => false,

                            'required' => [
                                'valor',
                                'mencionado',
                            ],

                            'properties' => [
                                'valor' => [
                                    'type' => [
                                        'string',
                                        'null',
                                    ],

                                    'enum' => [
                                        'relajado',
                                        'moderado',
                                        'intenso',
                                        null,
                                    ],
                                ],

                                'mencionado' => [
                                    'type' => 'boolean',
                                ],
                            ],
                        ],

                        'hora_inicio' =>
                            self::criterioHora(),

                        'hora_fin' =>
                            self::criterioHora(),

                        'categorias' => [
                            'type' => 'object',
                            'additionalProperties' => false,

                            'required' => [
                                'valor',
                                'mencionado',
                                'operacion',
                            ],

                            'properties' => [
                                'valor' => [
                                    'type' => 'array',

                                    'items' => [
                                        'type' => 'string',
                                        'minLength' => 1,
                                    ],
                                ],

                                'mencionado' => [
                                    'type' => 'boolean',
                                ],

                                'operacion' => [
                                    'type' => 'string',

                                    'enum' => [
                                        'reemplazar',
                                        'agregar',
                                        'eliminar',
                                    ],
                                ],
                            ],
                        ],

                        'costo_max_actividad' =>
                            self::criterioDecimal(),

                        'costo_min_actividad' =>
                            self::criterioDecimal(),

                        'duracion_min_actividad' =>
                            self::criterioEntero(),

                        'duracion_max_actividad' =>
                            self::criterioEntero(),

                        'preferir_menor_distancia' => [
                            'type' => 'object',
                            'additionalProperties' => false,

                            'required' => [
                                'valor',
                                'mencionado',
                            ],

                            'properties' => [
                                'valor' => [
                                    'type' => 'boolean',
                                ],

                                'mencionado' => [
                                    'type' => 'boolean',
                                ],
                            ],
                        ],
                    ],
                ],

                'solicitudes_adicionales' => [
                    'type' => 'array',

                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,

                        'required' => [
                            'criterio',
                            'valor',
                        ],

                        'properties' => [
                            'criterio' => [
                                'type' => 'string',
                                'minLength' => 1,
                                'maxLength' => 100,
                            ],

                            'valor' => [
                                'type' => [
                                    'string',
                                    'number',
                                    'boolean',
                                    'array',
                                    'object',
                                    'null',
                                ],
                            ],
                        ],
                    ],
                ],

                'observaciones' => [
                    'type' => 'array',

                    'items' => [
                        'type' => 'string',
                        'minLength' => 1,
                    ],
                ],
            ],
        ];
    }

    /**
     * Criterio entero.
     */
    private static function criterioEntero(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,

            'required' => [
                'valor',
                'mencionado',
            ],

            'properties' => [
                'valor' => [
                    'type' => [
                        'integer',
                        'null',
                    ],

                    'minimum' => 1,
                ],

                'mencionado' => [
                    'type' => 'boolean',
                ],
            ],
        ];
    }

    /**
     * Criterio decimal.
     */
    private static function criterioDecimal(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,

            'required' => [
                'valor',
                'mencionado',
            ],

            'properties' => [
                'valor' => [
                    'type' => [
                        'number',
                        'null',
                    ],

                    'minimum' => 0,
                ],

                'mencionado' => [
                    'type' => 'boolean',
                ],
            ],
        ];
    }

    /**
     * Criterio de hora en formato HH:mm.
     */
    private static function criterioHora(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,

            'required' => [
                'valor',
                'mencionado',
            ],

            'properties' => [
                'valor' => [
                    'type' => [
                        'string',
                        'null',
                    ],

                    'pattern' =>
                        '^([01][0-9]|2[0-3]):[0-5][0-9]$',
                ],

                'mencionado' => [
                    'type' => 'boolean',
                ],
            ],
        ];
    }
}
