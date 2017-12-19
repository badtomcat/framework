<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/11
 * Time: 17:02
 * =====================================
 *
 *      注册基本路径
 *      注册LOG
 *      注册ROUTER
 *      初始化别名数据
 */
namespace Badtomcat\Framework;

use Badtomcat\Container;
use Badtomcat\Routing\RouteCollection;
use Badtomcat\Routing\Router;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use function Symfony\Component\Debug\Tests\testHeader;

class Application extends Container
{
    /**
     * The current globally available container (if any).
     *
     * @var static
     */
    protected static $instance;

    protected $providers = [];


    /**
     * 类别名
     * @var array
     */
    protected $alias = [

    ];

    protected $aliases = [];
    protected $abstractAliases = [];

    protected $base_path;
    protected $hasBeenBootstrapped;

    /**
     * Application constructor.
     * @param string $base
     */
    public function __construct($base)
    {
        $this->base_path = $base;
        $this->bindPathsInContainer();
        $this->registerBaseBindings();
        $this->registerBaseServiceProviders();
        $this->registerCoreContainerAliases();
    }

    /**
     * Boot the application's service providers.
     *
     * @return void
     */
    public function boot()
    {
        foreach ($this->providers as $provider)
        {
            $inst = $this->make($provider);
            if (method_exists($inst,"boot"))
            {
                $inst->boot();
            }
        }
        $this->hasBeenBootstrapped = true;
    }

    /**
     * Register the basic bindings into the container.
     *
     * @return void
     */
    protected function registerBaseBindings()
    {
        self::setInstance($this);

        $this->instance('app', $this);

        $this->instance(Container::class, $this);
    }

    /**
     * Bind all of the application paths in the container.
     *
     * @return void
     */
    protected function bindPathsInContainer()
    {
        $this->instance('path', $this->base_path);
        $this->instance('path.base', $this->base_path);
        $this->instance('path.config', $this->base_path . DIRECTORY_SEPARATOR . 'config');
        $this->instance('path.public', $this->base_path . DIRECTORY_SEPARATOR . 'web');
        $this->instance('path.storage', $this->base_path . DIRECTORY_SEPARATOR . 'storage');
        $this->instance('path.resources', $this->base_path . DIRECTORY_SEPARATOR . 'resources');
    }

    /**
     * @return string
     */
    protected function getSystemLogPath()
    {
        return $this->make('path.storage') . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . date("Y-m-d") . '.log';
    }

    /**
     * Register all of the base service providers.
     *
     * @return void
     */
    protected function registerBaseServiceProviders()
    {
        $logger = new Logger('system');
        $logger->pushHandler(new StreamHandler($this->getSystemLogPath(), Logger::DEBUG));
        $this->instance(Logger::class,$logger);
        $this->instance('log',$logger);
        $router = new Router($this,new RouteCollection());
        $this->instance(Router::class,$router);
        $this->instance('router',$router);
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->make("log");
    }

    /**
     * Register the core class aliases in the container.
     *
     * @return void
     */
    public function registerCoreContainerAliases()
    {
        foreach ($this->alias as $key => $aliases) {
            foreach ($aliases as $alias) {
                $this->alias($key, $alias);
            }
        }
    }
    /**
     * Alias a type to a different name.
     *
     * @param  string  $abstract
     * @param  string  $alias
     * @return void
     */
    public function alias($abstract, $alias)
    {
        $this->aliases[$alias] = $abstract;
        $this->abstractAliases[$abstract][] = $alias;
    }

    /**
     * Register all of the configured providers.
     *
     * @return void
     */
    public function registerConfiguredProviders()
    {
        foreach ($this->providers as $provider)
        {
            $inst = new $provider($this);
            $this->instance($provider,$inst);
            if (method_exists($inst,"register"))
            {
                $inst->register();
            }
        }
    }


    /**
     * @param string $name
     * @param bool $force
     * @return mixed
     */
    public function make($name, $force = false)
    {

        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }
        elseif (array_key_exists($name,$this->abstractAliases))
        {
            foreach ($this->abstractAliases[$name] as $alias)
            {
                if (isset($this->instances[$alias]))
                {
                    return $this->instances[$alias];
                }
            }
        }

        try {
            return parent::make($name, $force);
        } catch (\Exception $e) {
            try {
                $log = parent::make("log");
                $log->error($e->getMessage());
            } catch (\Exception $e) {
                echo "<-- {$e->getMessage()} -->";
            }
        }
        return null;
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->base_path;
    }


    /**
     * Run the given array of bootstrap classes.
     *
     * @param  array $bootstrappers
     * @return void
     * @throws \Exception
     */
    public function bootstrapWith(array $bootstrappers)
    {
        $this->hasBeenBootstrapped = true;

        foreach ($bootstrappers as $bootstrapper) {
            $this->make($bootstrapper)->bootstrap($this);
        }
    }

    /**
     * Determine if the application has been bootstrapped before.
     *
     * @return bool
     */
    public function hasBeenBootstrapped()
    {
        return $this->hasBeenBootstrapped;
    }

    /**
     * Get or check the current application environment.
     *
     * @return string|bool
     */
    public function environment()
    {
//        if (func_num_args() > 0) {
//            $patterns = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();
//
//            foreach ($patterns as $pattern) {
//                if (preg_match($pattern, $this['env'])) {
//                    return true;
//                }
//            }
//
//            return false;
//        }

        return true;
    }


    /**
     * Set the shared instance of the container.
     *
     * @param  Container|null  $container
     */
    public static function setInstance(Container $container = null)
    {
        static::$instance = $container;
    }
}