<?php

namespace Tado\Lyrc\Services;

use Illuminate\Filesystem\Filesystem;

/**
* Given a array parsed from yaml, tries to build laravel routes
*/
class YamlRoutesToLaravel
{
	protected $files;
	protected $routeStub;

	public function __construct(Filesystem $files)
	{
		$this->files = $files;
		$this->routeStub = $this->getRouteStub();
		$this->middlewareStub = $this->files->get(__DIR__.'/stubs/middleware.stub');
	}

    /**
     * Passes parsed yaml array and configurations
     * and builds the Laravel routes definitions
     *
     * @param array $config | Configuration on lyrc file
     * @param array $routes | Simple parsed yml routes file
     * @return string | Complete laravel routes definition
     */
    public function getLaravelRoutes(array $config, array $routes)
	{
		$laravelRoutes = '';

		$routes = $this->wrapInMiddleware($routes);

		foreach ($routes as $middleware => $r) {
			$laravelRoutes .= $this->createMiddlewareByStub($middleware, $r, $config);
		}

		return $laravelRoutes;
	}

    /**
     * Organizes all routes by middleware combinations
     *
     * @param $routes
     * @return array
     */
    protected function wrapInMiddleware($routes)
	{
		$routesByMiddleware = [];

		foreach ($routes as $key => $value) {
			if (isset($value['middleware'])) {
				$middlewareKeys = implode('|', $value['middleware']);

				if (!isset($routesByMiddleware[$middlewareKeys])) {
					$routesByMiddleware[$middlewareKeys] = [];
				}

				$routesByMiddleware[$middlewareKeys][$key] = $value;
			} else {
				$routesByMiddleware['no-middleware'][$key] = $value;
			}
		}

		return $routesByMiddleware;
	}

    /**
     * Replaces all definitions by the correspondent stubs
     *
     * @param $middleware
     * @param $routes
     * @param $config
     * @return mixed|string
     */
    protected function createMiddlewareByStub($middleware, $routes)
	{
		$middlewaresText = '';
		$routesText = '';

		if ('no-middleware' == $middleware) {
			$middlewareStub = '{{routes}}';
		} else {
			$middlewares = array_map('trim', explode('|', $middleware));

			// We have to count the middlewares in order to define them in a array or just not
			if (count($middlewares) == 1) {
				$middlewaresText = "'$middlewares[0]'";
			} else {
				$middlewaresText .= "['" . implode("', '", $middlewares) . "']";
			}

			$middlewareStub = $this->replaceMiddlewareNames($this->middlewareStub, $middlewaresText);
		}

		foreach ($routes as $routeName => $route) {
			$routesText .= $this->getRoutesText($routeName, $route);
		}

		// replace routes definition by the stub
        return $this->replaceRoutes($middlewareStub, $routesText);
	}

    /**
     * Gets route stub definition
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
	protected function getRouteStub()
	{
		return $this->files->get(__DIR__.'/stubs/route.stub');
	}

    /**
     * For each parsed route, gets its correspondent laravel route definition
     *
     * @param $routeName | The name of the route defined and route key in the yml file
     * @param $route | Array of arguments useful for the laravel route
     * @return string | String in form all routes laravel style
     */
	protected function getRoutesText($routeName, $route)
	{
		$routeText = '';

		foreach ((array) $route['url'] as $countries => $uri) {

			foreach ($route['methods'] as $method) {
				$routeText .= $this->createRoute($method, $countries, $uri, $route['action'], $routeName);
			}
		}

		return $routeText;
	}

    /**
     * Creates a laravel
     *
     * @param $method
     * @param $countries
     * @param $uri
     * @param $action
     * @param $name
     * @return string
     */
	protected function createRoute($method, $countries, $uri, $action, $name)
	{
		$routeStub = '';
		$countries = $countries ? array_map('trim', explode(',', $countries)) : [''];

		foreach ($countries as $country) {
			$localizedName = $name;

		    if (!empty($country)) {
				$localizedName = $country . "_" . $name;
            }

			$routeStub .= $this->createRouteByStub($country, $method, $uri, $action, $localizedName);
		}

		return $routeStub;
	}

    /**
     * Replace text in stub by the correct definitions in yml file
     *
     * @param $country
     * @param $method
     * @param $uri
     * @param $action
     * @param $name
     * @return mixed
     */
	protected function createRouteByStub($country, $method, $uri, $action, $name)
	{
		// We are not taking care of the language
		if (is_array($uri)) {
			$uri = reset($uri);
		}

		$uri = $country ? "/" . $country . "/" . $uri : "/" . $uri;

		$stub = str_replace(
            ['{{method}}', '{{uri}}', '{{action}}', '{{name}}'],
            [$method, $uri, $action, $name],
            $this->routeStub
        );

        return $stub;
    }

    /**
     * Replaces {{middleware}} by the middleware laravel definition in stub
     *
     * @param $stub
     * @param $name
     * @return mixed
     */
	protected function replaceMiddlewareNames($stub, $name)
    {
        $stub = str_replace('{{middleware}}', $name, $stub);

        return $stub;
    }

    /**
     * Replaces {{routes}} by the routes laravel definition in stub
     *
     * @param $stub
     * @param $routes
     * @return mixed
     */
    protected function replaceRoutes($stub, $routes)
    {
        $stub = str_replace('{{routes}}', $routes, $stub);

        return $stub;
    }

    private function getLocalizedRouteName ($routeName)
    {
    }
}
