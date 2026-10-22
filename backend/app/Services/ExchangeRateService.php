<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class ExchangeRateService
{
    private const CURRENT_CACHE_KEY = 'exchange_rate.bcrp.usd_pen.current';

    private const LAST_CACHE_KEY = 'exchange_rate.bcrp.usd_pen.last';

    private const SERIES_CODE = 'PD04640PD';

    /**
     * The BCRP publishes the SBS bank selling rate as soles per US dollar.
     * This is a reference conversion, not a quoted payment or settlement rate.
     *
     * @return array{rate: float, as_of: string, stale: bool}|null
     */
    public function latest(): ?array
    {
        $today = CarbonImmutable::now('America/Lima')->startOfDay();
        $cached = Cache::get(self::CURRENT_CACHE_KEY);

        if ($this->isRecent($cached, $today)) {
            return $cached + ['stale' => false];
        }

        try {
            $url = sprintf(
                'https://estadisticas.bcrp.gob.pe/estadisticas/series/api/%s/json/%s/%s',
                self::SERIES_CODE,
                $today->subDays(45)->toDateString(),
                $today->toDateString(),
            );
            $response = Http::acceptJson()->timeout(6)->get($url);

            if ($response->successful()) {
                $quote = $this->latestValidQuote($response->json('periods'), $today);

                if ($quote !== null) {
                    Cache::put(self::CURRENT_CACHE_KEY, $quote, now()->addHours(6));
                    Cache::put(self::LAST_CACHE_KEY, $quote, now()->addDays(10));

                    return $quote + ['stale' => false];
                }
            }
        } catch (Throwable $exception) {
            report($exception);
        }

        $last = Cache::get(self::LAST_CACHE_KEY);

        return $this->isRecent($last, $today) ? $last + ['stale' => true] : null;
    }

    /**
     * @return array{rate: float, as_of: string}|null
     */
    private function latestValidQuote(mixed $periods, CarbonImmutable $today): ?array
    {
        if (! is_array($periods)) {
            return null;
        }

        $latest = null;

        foreach ($periods as $period) {
            if (! is_array($period)) {
                continue;
            }

            $date = $this->parseBcrpDate($period['name'] ?? null);
            $value = $period['values'][0] ?? null;

            if ($date === null || ! is_numeric($value)) {
                continue;
            }

            $rate = (float) $value;

            if ($rate <= 0 || $rate >= 20 || $date->greaterThan($today)) {
                continue;
            }

            $quote = ['rate' => $rate, 'as_of' => $date->toDateString()];

            if ($this->isRecent($quote, $today) && ($latest === null || $quote['as_of'] > $latest['as_of'])) {
                $latest = $quote;
            }
        }

        return $latest;
    }

    private function parseBcrpDate(mixed $name): ?CarbonImmutable
    {
        if (! is_string($name) || ! preg_match('/^(\d{1,2})\.([[:alpha:]]{3})\.(\d{2})$/u', $name, $parts)) {
            return null;
        }

        $months = [
            'ene' => 1, 'jan' => 1, 'feb' => 2, 'mar' => 3,
            'abr' => 4, 'apr' => 4, 'may' => 5, 'jun' => 6,
            'jul' => 7, 'ago' => 8, 'aug' => 8, 'set' => 9,
            'sep' => 9, 'oct' => 10, 'nov' => 11, 'dic' => 12, 'dec' => 12,
        ];
        $month = $months[mb_strtolower($parts[2])] ?? null;
        $year = 2000 + (int) $parts[3];
        $day = (int) $parts[1];

        if ($month === null || ! checkdate($month, $day, $year)) {
            return null;
        }

        return CarbonImmutable::create($year, $month, $day, 0, 0, 0, 'America/Lima');
    }

    private function isRecent(mixed $quote, CarbonImmutable $today): bool
    {
        if (! is_array($quote) || ! isset($quote['rate'], $quote['as_of'])) {
            return false;
        }

        try {
            $date = CarbonImmutable::createFromFormat('!Y-m-d', (string) $quote['as_of'], 'America/Lima');
        } catch (Throwable) {
            return false;
        }

        return is_numeric($quote['rate'])
            && (float) $quote['rate'] > 0
            && (float) $quote['rate'] < 20
            && $date !== false
            && $date->betweenIncluded($today->subDays(7), $today);
    }
}
