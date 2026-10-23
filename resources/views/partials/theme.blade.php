@php
    /**
     * Variables CSS du thème, pilotées par les réglages de l'entreprise.
     * Placée après @vite : elle écrase les valeurs de repli de _theme.scss.
     */
    $colors = app(App\Support\Settings::class)->themeColors();
@endphp
<style>
    :root {
        --color-primary: {{ $colors['primary'] }};
        --color-secondary: {{ $colors['secondary'] }};
        --color-accent: {{ $colors['accent'] }};
    }
</style>
