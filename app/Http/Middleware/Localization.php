<?php namespace App\Http\Middleware;

use Illuminate\Http\Request;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        $locale = $request->segment(1);
        if (!array_key_exists($locale, \Config::get('app.locales'))) {
            $locale = $this->negotiateLanguage($request);
            if ($locale !== \Config::get('app.locale')) {
                if (count($request->segments()) <= 1) {
                    // We have one or no segments, it's safer to assume no locale value exists in there, so let's prepend it to existing segments.
                    $segments = array_merge([$locale], $request->segments());
                } else {
                    // We have more than one segments, it's safer to assume that locale value exists in there and is wrong. Let's fix that.
                    $segments = $request->segments();
                    $segments[0] = $locale;
                }

                return redirect(implode('/', $segments));
            }
        }
        $request->session()->set('locale', $locale);
        \Lang::setLocale($locale);

        return $next($request);
    }

    /**
     * Negotiates language with the user's browser through the Accept-Language
     * HTTP header or the user's hostname.
     *
     * Language codes are generally in the form "ll" for a language spoken in only one country, or "ll-CC" for a
     * language spoken in a particular country.  For example, U.S. English is "en-US", while British English is "en-UK".
     * Portuguese as spoken in Portugal is "pt-PT", while Brazilian Portuguese is "pt-BR".
     *
     * This function is based on negotiateLanguage from Pear HTTP2 http://pear.php.net/package/HTTP2/
     *
     * Quality factors in the Accept-Language: header are supported, e.g. Accept-Language: en-UK;q=0.7, en-US;q=0.6, no, dk;q=0.8
     *
     * @param Request $request
     *
     * @return string
     */
    private function negotiateLanguage(Request $request)
    {
        # Check Accept-Language request header.
        $allLocales = \Config::get('app.locales');
        $acceptedLanguages = $this->getAcceptedLanguages($request);
        foreach ($acceptedLanguages as $language => $qualityFactor) {
            if (array_key_exists($language, $allLocales)) {
                return $language;
            }
        }

        # No success yet? Ok, let's see if client's hostname can give us any clues.
        if ($request->server('REMOTE_HOST')) {
            $remoteHost = explode('.', $request->server('REMOTE_HOST'));
            $language = strtolower(end($remoteHost));
            if (array_key_exists($language, $allLocales)) {
                return $language;
            }
        }

        # Still here? Fall back to the default language.
        return \Config::get('app.fallback_locale');
    }

    /**
     * Matches from the header field Accept-Languages
     *
     * @param Request $request
     *
     * @return array
     */
    private function getAcceptedLanguages(Request $request)
    {
        $matches = [];
        if ($acceptLanguages = $request->header('Accept-Language')) {
            $acceptLanguages = explode(',', $acceptLanguages);
            $genericMatches = [];
            foreach ($acceptLanguages as $option) {
                $option = array_map('trim', explode(';', $option));
                $language = $option[0];
                if (isset($option[1])) {
                    $q = (float)str_replace('q=', '', $option[1]);
                } else {
                    $q = null;
                    # Assign default low weight for generic values
                    if ($language == '*/*') {
                        $q = 0.01;
                    } elseif (substr($language, -1) == '*') {
                        $q = 0.02;
                    }
                }

                # Unweighted values, get high weight by their position in the list
                $q = isset($q) ? $q : 1000 - count($matches);
                $matches[$language] = $q;

                # If for some reason the Accept-Language header only sends language with country,
                # we should make the language without country an accepted option with a value less than it's parent.
                $languageOptions = explode('-', $language);
                array_pop($languageOptions);
                while (!empty($languageOptions)) {
                    //The new generic option needs to be slightly less important than it's base
                    $q -= 0.001;
                    $op = implode('-', $languageOptions);
                    if (empty($genericMatches[$op]) || $genericMatches[$op] > $q) {
                        $genericMatches[$op] = $q;
                    }
                    array_pop($languageOptions);
                }
            }
            $matches = array_merge($genericMatches, $matches);
            arsort($matches, SORT_NUMERIC);
        }

        return $matches;
    }
}
