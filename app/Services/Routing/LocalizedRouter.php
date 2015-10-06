<?php namespace App\Services\Routing;

use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\ControllerInspector;
use Illuminate\Routing\Router;

class LocalizedRouter extends Router
{
    /**
     * Create a new Router instance.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher $events
     * @param  \Illuminate\Container\Container         $container
     */
    public function __construct(Dispatcher $events, Container $container = null)
    {
        $this->events = $events;
        $this->routes = new LocalizedRouteCollection;
        $this->container = $container ?: new Container;
    }

    /**
     * Add a route to the underlying route collection.
     *
     * @param  array|string          $methods
     * @param  string                $uri
     * @param  \Closure|array|string $action
     *
     * @return \Illuminate\Routing\Route
     */
    protected function addRoute($methods, $uri, $action)
    {
        // Now we apply our Localization modifications.
        $uri = $this->localizeUris($uri);

        return parent::addRoute($methods, $uri, $action);
    }

    /**
     * Route a controller to a URI with wildcard routing.
     *
     * @param  string $uri
     * @param  string $controller
     * @param  array  $names
     *
     * @return void
     */
    public function controller($uri, $controller, $names = [])
    {
        $prepended = $controller;

        // First, we will check to see if a controller prefix has been registered in
        // the route group. If it has, we will need to prefix it before trying to
        // reflect into the class instance and pull out the method for routing.
        if (!empty($this->groupStack)) {
            $prepended = $this->prependGroupUses($controller);
        }

        $controllerInspector = new ControllerInspector;
        $routable = $controllerInspector->getRoutable($prepended, $uri);

        // Now we apply our Localization modifications.
        foreach ($routable as &$routes) {
            foreach ($routes as &$route) {
                $route['plain'] = $this->localizeUris($route['plain']);
                unset($route);
            }
            unset($routes);
        }

        // When a controller is routed using this method, we use Reflection to parse
        // out all of the routable methods for the controller, then register each
        // route explicitly for the developers, so reverse routing is possible.
        foreach ($routable as $method => $routes) {
            foreach ($routes as $route) {
                $this->registerInspected($route, $controller, $method, $names);
            }
        }

        $this->addFallthroughRoute($controller, $uri);
    }

    /**
     * Route a resource to a controller.
     *
     * @param  string $name
     * @param  string $controller
     * @param  array  $options
     *
     * @return void
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
     * Add a fallthrough route for a controller.
     *
     * @param  string $controller
     * @param  string $uri
     *
     * @return void
     */
    protected function addFallthroughRoute($controller, $uri)
    {
        $localizedUri = app('translator')->get('routes.' . $uri . '.');
        if (false !== strpos($localizedUri, '.')) {
            $localizedUri = $uri;
        }

        parent::addFallthroughRoute($controller, $localizedUri);
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    private function localizeUris($uri)
    {
        $uriExploded = explode('/', trim($uri, '/'));
        $localizedUriTranslationBitParts = [];
        while (list($level, $bitName) = each($uriExploded)) {
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
