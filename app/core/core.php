<?php
/**
 * URL Scheme
 * host/{controller}/{method}/{params @array}
 */

class Core extends Controllers {

	protected $controller = 'home';
	protected $method = 'main';
	protected $params = [];

	protected $debug = 0;

	public function __construct($debug = 0)
	{
		$url = explode('/', rtrim($_SERVER['REQUEST_URI'], '/'));

		if (count($url) > 3)
		{
			for ($i = 3; $i < count($url); $i++) {
				array_push($this->params, $url[$i]);
			}
		}

		$this->controller = isset($url[1]) ? $url[1] : $this->controller;
		$this->controller = str_replace('.','/', $this->controller);
		$this->method = isset($url[2]) ? $url[2] : $this->method;

		$path = './app/controllers/'.$this->controller.'.php';

		if (!file_exists($path)) {
			$debug ? $this->notice('controller','"'.$this->controller.'" not found.') : '';
			$this->controller = 'home';
			$this->params = (array)$url[1];
		}

		require './app/controllers/'.$this->controller.'.php';
		$controllerName = explode('/',$this->controller);
		$c = new $controllerName[count($controllerName)-1];

		if (!method_exists($c, $this->method)) {
			$debug ? $this->notice('method','"'.$this->method.'" not found in "'.$this->controller.'" controller.') : '';
			$this->method = 'main';
			$this->params = (array)$url[2];
			$method = 0;
		} else {
			$method = new ReflectionMethod($c, $this->method);
			$requiredParams = $method->getNumberOfRequiredParameters();
			$numParams = count($this->params) > 0 ? count($this->params) : 0;
		}

		if ($method) {
			if ($numParams < $requiredParams)
			{
				$debug ? $this->notice('params', 'required "'.$requiredParams.'".') : '';
			}
		}
		
		error_reporting(-0);
		call_user_func_array(array($c, $this->method), $this->params);

	}


	public function notice($type, $content)
	{
		echo '<code style="display:block;padding:10px;margin:10px;margin:10px;background:#f0f0f0"><b>'.$type.'</b>: '.$content.'</code>';
	}


	public function debug()
	{
		echo '<hr/><pre style="padding: 10px; background: #f0f0f0; border: 1px solid #333; display: inline-block">';
		print_r($this);
		echo '</pre>';
	}
}