<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Empêche le navigateur de garder une page HTML en cache.
 *
 * Les assets Vite changent de nom à chaque build : une page en cache
 * référence des fichiers qui n'existent plus, et le site paraît vide.
 * Les assets eux-mêmes gardent des noms versionnés et restent cachables.
 */
class PreventPageCaching
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (str_contains($response->headers->get('Content-Type', ''), 'text/html')) {
            $response->headers->set('Cache-Control', 'no-cache, must-revalidate');
        }

        return $response;
    }
}
