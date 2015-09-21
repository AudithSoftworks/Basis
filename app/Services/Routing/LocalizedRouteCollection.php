<?php namespace App\Services\Routing;

use Illuminate\Routing\RouteCollection;

class LocalizedRouteCollection extends RouteCollection
{
    /**
     * Add the route to any look-up tables if necessary.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return void
     */
    protected function addLookups($route)
    {
        $locale = app('translator')->getLocale();

        // If the route has a name, we will add it to the name look-up table so that we
        // will quickly be able to find any route associate with a name and not have
        // to iterate through every route every time we need to perform a look-up.
        $action = $route->getAction();

        if (isset($action['as'])) {
            $this->nameList[$action['as']][$locale] = $route;
        }

        // When the route is routing to a controller we will also store the action that
        // is used by the route. This will let us reverse route to controllers while
        // processing a request and easily generate URLs to the given controllers.
        if (isset($action['controller'])) {
            $this->addToActionList($action, $route);
        }
    }

    /**
     * Add a route to the controller action dictionary.
     *
     * @param  array                     $action
     * @param  \Illuminate\Routing\Route $route
     *
     * @return void
     */
    protected function addToActionList($action, $route)
    {
        $locale = app('translator')->getLocale();
        $this->actionList[trim($action['controller'], '\\')][$locale] = $route;
    }

    /**
     * Get a route instance by its controller action.
     *
     * @param  string $action
     *
     * @return \Illuminate\Routing\Route|null
     */
    public function getByAction($action)
    {
        $locale = app('translator')->getLocale();

        return isset($this->actionList[$action][$locale]) ? $this->actionList[$action][$locale] : null;
    }

    /**
     * Get a route instance by its name.
     *
     * @param  string  $name
     * @return \Illuminate\Routing\Route|null
     */
    public function getByName($name)
    {
        $locale = app('translator')->getLocale();

        if (isset($this->nameList[$name])) {
            return $this->nameList[$name];
        }

        return isset($this->nameList[$locale . '.' . $name]) ? $this->nameList[$locale . '.' . $name] : null;
    }
}
