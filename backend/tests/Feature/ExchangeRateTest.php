<?php

namespace Tests\Feature;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExchangeRateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cache.default', 'array');
        Cache::flush();
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-20 12:00:00', 'America/Lima'));
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_it_uses_the_most_recent_valid_bcrp_rate_and_caches_it(): void
    {
        Http::fake([
            'estadisticas.bcrp.gob.pe/*' => Http::response([
                'periods' => [
                    ['name' => '16.Oct.26', 'values' => ['3.431']],
                    ['name' => '19.Oct.26', 'values' => ['3.447']],
                    ['name' => '20.Oct.26', 'values' => ['n.d.']],
                ],
            ]),
        ]);

        $this->getJson(route('exchange-rate.pen-usd'))
            ->assertOk()
            ->assertJsonPath('base', 'USD')
            ->assertJsonPath('quote', 'PEN')
            ->assertJsonPath('rate', 3.447)
            ->assertJsonPath('as_of', '2026-10-19')
            ->assertJsonPath('stale', false);

        $this->getJson(route('exchange-rate.pen-usd'))->assertOk();
        Http::assertSentCount(1);
    }

    public function test_it_uses_a_recent_previous_quote_when_bcrp_is_temporarily_unavailable(): void
    {
        Http::fakeSequence()
            ->push(['periods' => [['name' => '19.Oct.26', 'values' => ['3.447']]]])
            ->pushStatus(503);

        $this->getJson(route('exchange-rate.pen-usd'))->assertOk();
        Cache::forget('exchange_rate.bcrp.usd_pen.current');

        $this->getJson(route('exchange-rate.pen-usd'))
            ->assertOk()
            ->assertJsonPath('rate', 3.447)
            ->assertJsonPath('stale', true);
    }

    public function test_it_refuses_to_invent_or_reuse_an_old_rate(): void
    {
        Http::fake([
            'estadisticas.bcrp.gob.pe/*' => Http::response([
                'periods' => [['name' => '01.Oct.26', 'values' => ['3.400']]],
            ]),
        ]);

        $this->getJson(route('exchange-rate.pen-usd'))
            ->assertStatus(503)
            ->assertJsonMissingPath('rate');
    }
}
