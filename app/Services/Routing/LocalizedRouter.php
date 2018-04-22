<?php namespace App\Services\Routing;

use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\Router;

class LocalizedRouter extends Router
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Dispatcher $events, Container $container = null)
    {
        parent::__construct($events, $container);

        $this->routes = new LocalizedRouteCollection;
    }

    /**
     * {@inheritdoc}
     */
    protected function addRoute($methods, $uri, $action)
    {
        if (isset($action['locale'])) {
            // Now we apply our Localization modifications.
            $uri = $this->localizeUris($uri, $action['locale']);
        }

        return parent::addRoute($methods, $uri, $action);
    }

    /**
     * {@inheritdoc}
     */
    public function resource($name, $controller, array $options = [])
    {
        if ($this->container && $this->container->bound(LocalizedResourceRegistrar::class)) {
            $registrar = $this->container->make(LocalizedResourceRegistrar::class);
        } else {
            $registrar = new LocalizedResourceRegistrar($this);
        }

        $registrar->register($name, $controller, $options);
    }

    /**
     * Translates URIs
     *
     * @param string $uri
     * @param string $locale
     *
     * @return string
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    private function localizeUris($uri, $locale)
    {
        $uriExploded = explode('/', trim($uri, '/'));
        $localizedUriTranslationBitParts = [];
        foreach ($uriExploded as $level => $bitName) {
            if ($level == 0) {
                $localizedUriTranslationBitParts[$level] = 'routes.' . $bitName . '.';
            } else {
                $localizedUriTranslationBitParts[$level] = trim($localizedUriTranslationBitParts[$level - 1], '.') . '.' . $bitName;
            }
        }
        foreach ($localizedUriTranslationBitParts as $level => &$translationBitPart) {
            $phraseToGetTranslationFor = $translationBitPart;
            if (preg_match('#(?<!routes)\.\{[^\}]+\}\.#', $translationBitPart)) { // For lower-level paths, in order not to hit 'routes.' index.
                $phraseToGetTranslationFor = preg_replace('#\{[^\}]+\}\.?#', '', $translationBitPart);
            }
            app('translator')->setLocale($locale);
            $translatedPhrase = app('translator')->get($phraseToGetTranslationFor);
            if (false !== strpos($translatedPhrase, '.')) {
                $translationBitPart = $uriExploded[$level];
            } else {
                $translationBitPart = $translatedPhrase;
            }
            unset($translationBitPart); // Delete the reference (won't delete the original).
        }

        return implode('/', $localizedUriTranslationBitParts);
    }
}
