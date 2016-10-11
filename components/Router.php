<?php

/**
* Клас Router
* Компонент для роботи з маршрутами
*/
class Router
{

      /**
      * Властивість для зберігання масиву роутів
      * @var Array
      */
    private $routes;

    public function __construct()
    {
        $routesPath = ROOT . '/config/routes.php';
        $this->routes = include($routesPath);
    }

    /**
     * Метод отримання URI
     * @return string
     */
    private function getURI()
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI'], '/');
        }
    }


    /**
    * Метод для обробки запиту
    */
    public function run()
    {

        $uri = $this->getURI();


        /** Перевіряємо наявність такого запиту в масиві маршрутів (routes.php) */
        foreach ($this->routes as $uriPattern => $path) {

            /**  Порівнюємо $ uriPattern і $uri */
            if (preg_match("~$uriPattern~", $uri)) {

                /** Отримаєм внутрішній шлях із зовнішнього згідно з правилом. */
                $internalRoute = preg_replace("~$uriPattern~", $path, $uri);


                /** Визначити контролер, action, параметри @var  $segments */
                $segments = explode('/', $internalRoute);

                $controllerName = array_shift($segments) . 'Controller';
                $controllerName = ucfirst($controllerName);



                $actionName = 'action' . ucfirst(array_shift($segments));

                $parameters = $segments;

                /** Підключити файл класу-контролера */
                $controllerFile = ROOT . '/controllers/' .
                        $controllerName . '.php';

                if (file_exists($controllerFile)) {
                    include_once($controllerFile);
                }


                $controllerObject = new $controllerName;


                /**
                 * Перевіряємо наявність метода, якщо немає то видаємо 404
                 */
                if(method_exists($controllerObject,$actionName))
                {

                    /** Викликаємо необхідний метод ($ actionName) у певного
                    * Класу ($ controllerObject) з заданими ($ parameters) параметрами
                    */
                    $result = call_user_func_array(array($controllerObject, $actionName), $parameters);
                }
                else
                {
                    $con = new ErrorController();
                    $result = $con->action404();
                }

                /** Якщо метод контролера успішно викликаний, завершуємо роботу роутера */
                if ($result != null) {
                    break;
                }
            }
        }



    }

}
