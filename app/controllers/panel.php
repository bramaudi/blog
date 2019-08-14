<?php

class Panel extends Controllers {
	
	public function main()
	{
		$this->index();
	}
	

	public function header($title)
	{
		$data['title'] = empty($title) ? 'Panel': $title;
		$data['logged'] = $this->logged();
		$data['logged2'] = $this->logged();

		$data['categories'] = $this->model('Category_Model')->count();
		$data['gallery'] = $this->model('Gallery_Model')->count();
		$data['posts'] = $this->model('Posts_Model')->count();
		$data['mod'] = $this->model('Mod_Model')->count();
		$data['users'] = $this->model('Users_Model')->count();

		$data['blog'] = $this->blog;
		$this->view('panel/header', $data);
	}

	public function index()
	{
		if ($this->logged())
		{
			$this->header('Panel');
			$this->view('panel/index');
		
		} else {
			$this->login();
		}
	}

	public function login()
	{
		if (!$this->logged())
		{
			$this->header('Login');
			$this->view('panel/login');
		} else {
			$this->index();
		}
	}

	public function register()
	{
		$this->header('Register');
		$this->view('panel/register');
	}


	public function category()
	{
		if ($this->logged())
		{
			$this->header('Panel - Category');
			$this->view('panel/category');
		} else {
			$this->login();
		}
	}

	public function posts()
	{
		if ($this->logged())
		{
			$this->header('Panel - Posts');
			$this->view('panel/posts');
		} else {
			$this->login();
		}
	}


	public function mod()
	{
		if ($this->logged())
		{
			$this->header('Panel - Role');
			$this->view('panel/role');
		} else {
			$this->login();
		}
	}


	public function profile($act = null, $param = null)
	{
		if ($this->logged())
		{
			$user = $this->model('Users_Model');
			$x = (object)$user->getCurrent();

			$this->header('Panel - User');
			
			switch ($act) {

				case 'edit':
					$data['name'] = $x->name;
					$this->view('panel/profile/edit', $data);
					break;

				default:
					$online = $this->plugin->online($x->online) ? 'Online': 'Offline';
					$data = array(
						'id'			=> $x->id,
						'name'			=> $x->name,
						'username'		=> $x->username,
						'online'		=> $online,
						'mod'			=> $this->model('Mod_Model')->select($x->mod)['name'],
						'register_at'	=> $this->plugin->formatTime($x->register_at)
					);

					$this->view('panel/profile/info', $data);
					break;
			}
		} else {
			$this->login();
		}
	}


	public function users($act = null, $param = null)
	{
		if ($this->logged())
		{
			$this->header('Panel - Users');
			$this->view('panel/users');
		} else {
			$this->login();
		}
	}


	public function gallery()
	{
		if ($this->logged())
		{
			$this->header('Panel - Gallery');
			$this->view('panel/gallery');
		} else {
			$this->login();
		}
	}


	public function setting()
	{
		if ($this->logged())
		{
			$data = $this->model('Settings_Model');
			$this->header('Panel - Setting');	
			$this->view('panel/setting', $data->get());
		}
		else {
			$this->login();
		}
	}



	public function logout()
	{
		setcookie('logged','', 1, '/');
		$this->plugin->redir('/panel');
	}
	
}