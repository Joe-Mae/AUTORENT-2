<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Lecture des réglages de l'entreprise (marque blanche).
 *
 * Tous les réglages sont chargés en une seule requête puis mis en
 * cache : le site public en fait plusieurs appels par page.
 */
class Settings
{
    /**
     * Retourne un réglage par sa clé, avec une valeur de repli.
     */
    public function get(string $key, ?string $default = null): ?string
    {
        return $this->all()[$key] ?? $default;
    }

    /**
     * Enregistre un réglage (création ou mise à jour).
     */
    public function set(string $key, ?string $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Enregistre plusieurs réglages en une transaction.
     *
     * @param  array<string, ?string>  $values
     */
    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Tous les réglages sous forme clé => valeur.
     *
     * @return array<string, string>
     */
    public function all(): array
    {
        return Cache::rememberForever(
            Setting::CACHE_KEY,
            fn () => Setting::query()->pluck('value', 'key')->all(),
        );
    }

    /**
     * Réglages de couleur du thème, prêts à être injectés en CSS.
     *
     * @return array<string, string>
     */
    public function themeColors(): array
    {
        return [
            'primary' => $this->get('color_primary', '#d95319'),
            'secondary' => $this->get('color_secondary', '#14213d'),
            'accent' => $this->get('color_accent', '#2a9d8f'),
        ];
    }
}
