<?php

namespace Tests\Feature;

use App\Modules\Actividades\Http\Requests\ActividadRequest;
use App\Modules\Rutas\Http\Requests\RutaRequest;
use App\Modules\Valoraciones\Http\Requests\ValoracionRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class InterfaceLocaleTest extends TestCase
{
    public function test_spanish_is_the_default_on_the_login_page(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('<html lang="es"', false)
            ->assertSee('Iniciar sesión');
    }

    public function test_language_selection_persists_across_pages_and_can_be_changed_back(): void
    {
        $this->postJson(route('interface.locale'), ['locale' => 'en'])
            ->assertOk()
            ->assertJsonPath('locale', 'en')
            ->assertCookie('tc_locale', 'en');

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('<html lang="en"', false)
            ->assertSee('Sign in')
            ->assertDontSee('Continúa con tus planes de viaje.');

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Register as a traveler');

        $this->postJson(route('interface.locale'), ['locale' => 'es'])
            ->assertOk()
            ->assertJsonPath('locale', 'es');

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('<html lang="es"', false)
            ->assertSee('Continúa con tus planes de viaje.');
    }

    public function test_unsupported_language_is_rejected_without_changing_session(): void
    {
        $this->postJson(route('interface.locale'), ['locale' => 'en'])->assertOk();
        $this->postJson(route('interface.locale'), ['locale' => 'fr'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('locale');

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('<html lang="en"', false);
    }

    public function test_language_cookie_restores_preference_after_a_new_session(): void
    {
        $this->withCookie('tc_locale', 'en')
            ->get(route('login'))
            ->assertOk()
            ->assertSee('<html lang="en"', false)
            ->assertSee('Sign in');
    }

    public function test_all_application_translation_keys_exist_in_both_languages(): void
    {
        foreach (glob(lang_path('en/*.php')) as $englishFile) {
            $name = basename($englishFile);
            $spanishFile = lang_path('es/'.$name);

            $this->assertFileExists($spanishFile);
            $english = require $englishFile;
            $spanish = require $spanishFile;

            $this->assertSame([], array_diff(array_keys($english), array_keys($spanish)), $name.' has English-only keys');
            $this->assertSame([], array_diff(array_keys($spanish), array_keys($english)), $name.' has Spanish-only keys');
        }
    }

    public function test_operator_form_validation_messages_follow_selected_language(): void
    {
        app()->setLocale('en');
        $this->assertSame('Select a destination.', (new ActividadRequest)->messages()['destino_id.required']);
        $this->assertSame('Add at least two stops.', (new RutaRequest)->messages()['paradas.required']);
        $this->assertSame('Select a rating from 1 to 5 stars.', (new ValoracionRequest)->messages()['puntuacion.required']);

        app()->setLocale('es');
        $this->assertSame('Debes seleccionar un destino.', (new ActividadRequest)->messages()['destino_id.required']);
        $this->assertSame('Debes agregar al menos dos paradas.', (new RutaRequest)->messages()['paradas.required']);
        $this->assertSame('Debes seleccionar una puntuación de 1 a 5 estrellas.', (new ValoracionRequest)->messages()['puntuacion.required']);
    }

    public function test_nested_translations_preserve_keys_and_replacement_parameters(): void
    {
        $flatten = function (array $messages, string $prefix = '') use (&$flatten): array {
            $result = [];
            foreach ($messages as $key => $value) {
                $path = $prefix.$key;
                if (is_array($value)) {
                    $result += $flatten($value, $path.'.');
                } else {
                    $result[$path] = $value;
                }
            }

            return $result;
        };

        foreach (glob(lang_path('en/*.php')) as $englishFile) {
            $name = basename($englishFile);
            $english = $flatten(require $englishFile);
            $spanish = $flatten(require lang_path('es/'.$name));
            $englishKeys = array_keys($english);
            $spanishKeys = array_keys($spanish);
            sort($englishKeys);
            sort($spanishKeys);

            $this->assertSame($englishKeys, $spanishKeys, $name.' has mismatched nested keys');
            foreach ($english as $key => $message) {
                preg_match_all('/(?<![[:alnum:]]):[a-zA-Z_][a-zA-Z0-9_]*/', $message, $englishParameters);
                preg_match_all('/(?<![[:alnum:]]):[a-zA-Z_][a-zA-Z0-9_]*/', $spanish[$key], $spanishParameters);
                $expected = array_values(array_unique($englishParameters[0]));
                $actual = array_values(array_unique($spanishParameters[0]));
                sort($expected);
                sort($actual);

                $this->assertSame($expected, $actual, $name.':'.$key.' has mismatched replacement parameters');
            }
        }
    }

    public function test_generic_validation_messages_follow_selected_language(): void
    {
        app()->setLocale('es');
        $this->assertSame(
            'El campo correo electrónico debe ser un correo electrónico válido.',
            Validator::make(['email' => 'invalid'], ['email' => 'email'])->errors()->first('email'),
        );

        app()->setLocale('en');
        $this->assertSame(
            'The email field must be a valid email address.',
            Validator::make(['email' => 'invalid'], ['email' => 'email'])->errors()->first('email'),
        );
    }

    public function test_operator_activity_and_route_forms_render_in_english(): void
    {
        app()->setLocale('en');

        $activity = view('actividades::operador.create', ['destinos' => new Collection, 'errors' => new ViewErrorBag])->render();
        $this->assertStringContainsString('Experience information', $activity);
        $this->assertStringContainsString('Search for a place or meeting point', $activity);
        $this->assertStringContainsString('Main image', $activity);

        $route = view('rutas::operador.create', [
            'destinos' => new Collection,
            'destinoSeleccionado' => null,
            'atractivosPorDestino' => [],
            'errors' => new ViewErrorBag,
        ])->render();
        $this->assertStringContainsString('Available attractions', $route);
        $this->assertStringContainsString('Route through your stops', $route);
        $this->assertStringContainsString('Create route', $route);
    }

    public function test_destination_form_renders_in_both_languages(): void
    {
        $data = ['operadores' => new Collection, 'errors' => new ViewErrorBag];

        app()->setLocale('en');
        $english = view('destinos.create', $data)->render();
        $this->assertStringContainsString('Register a new destination', $english);
        $this->assertStringContainsString('Search destination (city / region)', $english);

        app()->setLocale('es');
        $spanish = view('destinos.create', $data)->render();
        $this->assertStringContainsString('Registrar nuevo destino', $spanish);
        $this->assertStringContainsString('Buscar destino (ciudad / región)', $spanish);
    }

    public function test_provider_creation_form_translates_labels_days_and_map_hint(): void
    {
        $data = ['destinos' => new Collection, 'categorias' => new Collection, 'errors' => new ViewErrorBag];

        app()->setLocale('en');
        $english = view('proveedor.establecimientos.create', $data)->render();
        $this->assertStringContainsString('Register a new place', $english);
        $this->assertStringContainsString('Monday', $english);
        $this->assertStringContainsString('Drag to adjust the location', $english);
        $this->assertStringNotContainsString('Arrastra para ajustar', $english);

        app()->setLocale('es');
        $spanish = view('proveedor.establecimientos.create', $data)->render();
        $this->assertStringContainsString('Lunes', $spanish);
        $this->assertStringContainsString('Arrastra para ajustar', $spanish);
    }
}
