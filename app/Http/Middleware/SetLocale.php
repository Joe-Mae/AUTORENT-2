<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Url;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fixe la locale de l'application depuis le préfixe de l'URL.
 * Seul « fr » est livré en v1 ; ajouter une locale ici et dans
 * $locales l'expose automatiquement dans les routes.
 */
class SetLocale
{
    /** Locales acceptées par le site. */
    public const LOCALES = ['fr'];

    /** Locale utilisée quand l'URL n'en porte pas (back-office, fichiers). */
    public const DEFAULT = 'fr';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route()?->parameter('locale');

        if (! in_array($locale, self::LOCALES, true)) {
            $locale = self::DEFAULT;
        }

        app()->setLocale($locale);

        // Les routes publiques prennent {locale} en paramètre : en fournir
        // une par défaut évite de la repasser à chaque appel de route().
        Url::defaults(['locale' => $locale]);
        $request->route()?->forgetParameter('locale');

        return $next($request);
    }
}
