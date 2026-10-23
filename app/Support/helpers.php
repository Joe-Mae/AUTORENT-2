<?php

use App\Support\Settings;

if (! function_exists('setting')) {
    /**
     * Lit un réglage de l'entreprise, ou tous les réglages si aucune clé n'est donnée.
     */
    function setting(?string $key = null, ?string $default = null): mixed
    {
        $settings = app(Settings::class);

        if ($key === null) {
            return $settings->all();
        }

        return $settings->get($key, $default);
    }
}

if (! function_exists('format_price')) {
    /**
     * Formate un montant dans la devise de l'entreprise (ex. « 7 000 FCFA »).
     */
    function format_price(float|int|null $amount): string
    {
        return trim(number_format((float) $amount, 0, ',', ' ').' '.setting('currency', 'FCFA'));
    }
}
