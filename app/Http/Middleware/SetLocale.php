<?php

namespace App\Http\Middleware;

use App\Helpers\LocaleHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $availableLocales = LocaleHelper::codes();

        // Handle language switch from URL
        if ($request->has('switch_locale')) {
            $locale = $request->get('switch_locale');
            if (in_array($locale, $availableLocales)) {
                session(['locale' => $locale]);
                return redirect()->to($request->url());
            }
        }

        // Get locale from session or use default
        $locale = session('locale', LocaleHelper::default());

        // Validate locale
        if (in_array($locale, $availableLocales)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
