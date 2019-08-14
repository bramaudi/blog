<?php

class Controllers {

	private $tpl;
	private $tplx;
	public $plugin;
	public $blog;

	public function __construct()
	{
		$this->tpl = new tinyTemplate();
		$this->tplx = new tinyTemplate();
		$this->plugin = new Plugin();
		$this->blog = $this->model('Settings_Model')->get();
	}


	public function view($view, $data = [], $filter = true)
	{
		$path = './app/views/' .$view. '.html';
		if (file_exists($path)) {
			foreach ($data as $key => $value) {
				$this->tpl->set($key, $value, $filter);
			}
			echo $this->tpl->fetch($path);
			// $file = file_get_contents($path);
			// foreach ($data as $key => $value) {
			// 	$file = str_replace('{$'.$key.'}', $value, $file);
			// }
			// echo $file;
		}
		else {
			if (DEBUG_MODE) {
				// views % not found
				echo '<span style="background: #dfd; font-weight: bold">INFO</span>: views "<span style="background:#f0f0f0;"><code>'.$view.'</code></span>" not found.';
			}
			else {
				require_once './app/views/errors/404.html';
			}
		}
	}


	public function include($view, $data = [], $filter = true)
	{
		$path = './app/views/' .$view. '.html';
		if (file_exists($path)) {
			foreach ($data as $key => $value) {
				$this->tplx->set($key, $value, $filter);
			}
			return $this->tplx->fetch($path);
		}
		else {
			if (DEBUG_MODE) {
				// views % not found
				echo '<span style="background: #dfd; font-weight: bold">INFO</span>: cant include "<span style="background:#f0f0f0;"><code>'.$view.'</code></span>" is not found.';
			}
			else {
				require_once './app/views/errors/404.html';
			}
		}
	}


	public function model($model)
	{
		$path = './app/models/' .$model. '.php';
		if (file_exists($path)) {
			require_once $path;
			return new $model;
		}
		else {
			if (DEBUG_MODE) {
				// views % not found
				echo '<span style="background: #dfd; font-weight: bold">INFO</span>: models "<span style="background:#f0f0f0;"><code>'.$model.'</code></span>" not found.';
			}
			else {
				require_once './app/views/errors/404.html';
			}
		}
	}


	/** Extended method */
	public function logged()
	{
		$db = $this->model('Users_Model');
		if (isset($_COOKIE['logged'])) {
			if ($db->verify($_COOKIE['logged'])) {
				$db->updateOnlineActivity();
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function home_header($title = null, $desc = null)
	{
		$data['blog'] = $this->blog;
		$data['title'] = empty($title) ? $data['blog']['name'] : $title;
		$data['logged'] = $this->logged();
		$data['search'] = @$_COOKIE['search'];
		$data['meta_description'] = empty($desc) ? $data['blog']['description'] : htmlentities($desc);

		$this->view('home/header', $data);
	}

	public function home_footer()
	{
		$data['blog'] = $this->blog;
		$this->view('home/footer', $data);
	}

	public function pagination($pagin, $link = '/home/page/')
	{
		$html = '';
		if ($pagin['get'] > 1) {
			$html .= '<a href="'.$link.'1">First</a>';
		}

		if ($pagin['get'] > 2) {
			$start = $pagin['get'] - 2;
		} else {
			$start = 1;
		}

		if ($pagin['get'] == $pagin['page']) {
			$end = $pagin['get'];
		} elseif ($pagin['get'] < $pagin['page']) {
			$end = $pagin['get']+1;
		} else {
			$end = $pagin['get']+2;
		}
		for ($i = $start;$i <= $end;$i++) {
			if ($i != $pagin['get']) {
				$html .= '<a href="'.$link.$i.'">'.$i.'</a>';
			} else {
				$html .= '<span>'.$i.'</span>';
			}
		}

		if ($pagin['get'] > 0 && $pagin['get'] < $pagin['page']) {
			$html .= '<a href="'.$link.$pagin['page'].'">'.$pagin['page'].'</a>';
		}

		return $html;
	}
	
	
}