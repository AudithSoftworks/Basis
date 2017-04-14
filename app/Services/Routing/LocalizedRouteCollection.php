<?php namespace App\Services\Routing;

use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;

class LocalizedRouteCollection extends RouteCollection
{
    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getByName($name)
    {
        $locale = app('translator')->getLocale();
        if (is_array($this->nameList[$name])) { // array with localized routes
            return $this->nameList[$name][$locale];
        } elseif ($this->nameList[$name] instanceof Route) { // no localization available, so it'll be a route object
            return $this->nameList[$name];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getByAction($action)
    {
        $locale = app('translator')->getLocale();
        if (is_array($this->actionList[$action])) { // array with localized routes
            return $this->actionList[$action][$locale];
        } elseif ($this->actionList[$action] instanceof Route) { // no localization available, so it'll be a route object
            return $this->actionList[$action];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshNameLookups()
    {
        $this->nameList = [];

        /** @var \Illuminate\Routing\Route $route */
        foreach ($this->allRoutes as $route) {
            if ($route->getName()) {
                if (isset($route->getAction()['locale'])) {
                    $this->nameList[$route->getName()][$route->getAction()['locale']] = $route;
                } else {
                    $this->nameList[$route->getName()] = $route;
                }
            }
        }
    }
}
