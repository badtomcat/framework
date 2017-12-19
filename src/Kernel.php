<?php

namespace Badtomcat\Framework;


use Badtomcat\Http\Response;
use Badtomcat\Routing\RequestContext;
use Exception;
use Badtomcat\Routing\Router;


abstract class Kernel
{
    /**
     * The priority-sorted list of middleware.
     *
     * Forces the listed middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [

    ];
    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [

        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [

    ];
    /**
     * The application implementation.
     *
     * @var Application
     */
    protected $app;

    /**
     * The router instance.
     *
     * @var Router
     */
    protected $router;

    /**
     * The bootstrap classes for the application.
     *
     * @var array
     */
    protected $bootstrappers = [

    ];



    /**
     * Create a new HTTP kernel instance.
     *
     * @param  Application  $app
     * @param  Router  $router
     * @return void
     */
    public function __construct(Application $app, Router $router)
    {
        $this->app = $app;
        $this->router = $router;

        $router->middlewarePriority = $this->middlewarePriority;
        $router->setMiddlewareGroups($this->middlewareGroups);
        $router->setGlobalMiddleware($this->routeMiddleware);
    }

    /**
     * Handle an incoming HTTP request.
     *
     * @param  RequestContext  $request
     * @return Response
     */
    public function handle($request)
    {
        try {
            $response = $this->sendRequestThroughRouter($request);
        } catch (Exception $e) {
            $this->reportException($e);
            $response = new Response($e->getMessage(),500);
        }

        return $response;
    }

    /**
     * Send the given request through the middleware / router.
     *
     * @param  RequestContext $request
     * @return Response
     * @throws Exception
     */
    protected function sendRequestThroughRouter($request)
    {
        $this->bootstrap();
        return $this->router->dispatch($request);
    }

    /**
     * Bootstrap the application for HTTP requests.
     *
     * @return void
     * @throws Exception
     */
    public function bootstrap()
    {
        if (! $this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }
    }

    /**
     * Get the bootstrap classes for the application.
     *
     * @return array
     */
    protected function bootstrappers()
    {
        return $this->bootstrappers;
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param  \Exception  $e
     * @return void
     */
    protected function reportException(Exception $e)
    {
        var_dump($e->getMessage());
        var_dump($e->getFile());
        var_dump($e->getLine());
        echo "Badtomcat\\Framework\\Kernel::reportException";
        exit;
//        $this->app["error.handler"]->report($e);
    }

    /**
     * Render the exception to a response.
     *
     * @param  RequestContext  $request
     * @param  \Exception  $e
     */
    protected function renderException($request, Exception $e)
    {
        //echo $e->getMessage();
        //return $this->app[ExceptionHandler::class]->render($request, $e);
    }

    /**
     * Get the Laravel application instance.
     *
     * @return Application
     */
    public function getApplication()
    {
        return $this->app;
    }
}
