<?php namespace KodiCMS\CMS\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{

	/**
	 * This namespace is applied to the controller routes in your routes file.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'KodiCMS\CMS\Http\Controllers';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @param  \Illuminate\Routing\Router $router
	 * @return void
	 */
	public function boot(Router $router)
	{
		parent::boot($router);

	}

	/**
	 * Define the routes for the application.
	 *
	 * @param  \Illuminate\Routing\Router $router
	 * @return void
	 */
	public function map(Router $router)
	{
		foreach ($this->app['module.loader']->getRegisteredModules() as $module) {
			$routesFile = $module->getPath(['Http', 'routes.php']);
			if (is_file($routesFile)) {
				$router->group(['namespace' => $module->getControllerNamespace()], function ($router) use ($routesFile) {
					require $routesFile;
				});
			}
		}
	}
}
