<?php

class User extends Controllers {

	public function main(Type $var = null){}


	public function login()
	{
		$db = $this->model('Users_Model');
		$post = json_decode(file_get_contents('php://input'));
		
		$remember = empty($post->remember) ? 0 : 1;
		
		$check = $db->num_rows("SELECT COUNT(id) FROM tb_users WHERE username = '$post->user' AND `password` = '".md5(md5(md5($post->pass)))."'");
		
		if (empty($post->user) OR empty($post->pass)) {
			$json['error'] = 1;
			$json['msg'] = 'Username or password is empty!';
		} elseif (!$check) {
			$json['error'] = 1;
			$json['msg'] = 'Invalid username or password!';
		} else {
			
			$user_id = $db->num_rows("SELECT id FROM tb_users WHERE username = '$post->user'");

			$user = (object)$db->get($user_id);

			if ($user->ban)
			{
				$json['error'] = 1;
				$json['msg'] = 'This account is banned.';
			}
			else {
				$time = $remember == 1 ? time()+(3600*720) : time()+3600;
				setcookie('logged', $user->logged, $time, '/');
				
				$json['error'] = 0;
				$json['msg'] = 'click <a href="/panel">here</a> if not redirect automatically.';
			}

		}

		echo json_encode($json);
	}




	public function register()
	{
		$db = $this->model('Users_Model');
		$post = json_decode(file_get_contents('php://input'));
		
		$remember = empty($post->remember) ? 0 : 1;
		
		$check = $db->num_rows("SELECT COUNT(id) FROM tb_users WHERE username = '$post->user'");

		$open_register = $this->model('Settings_Model')->get()['open_register'];
		
		if (!$open_register) {
			$json['error'] = 1;
			$json['msg'] = 'Registration is closed by Administrator.';
		} elseif (empty($post->user) OR empty($post->pass)) {
			$json['error'] = 1;
			$json['msg'] = 'Username or password is empty!';
		} elseif (empty($post->passver)) {
			$json['error'] = 1;
			$json['msg'] = 'Please verify password!';
		} elseif ($post->passver !== $post->pass) {
			$json['error'] = 1;
			$json['msg'] = 'Password doesnt match!';
		} elseif ($check) {
			$json['error'] = 1;
			$json['msg'] = 'Username already used!';
		} else {

			$array = array(
				'name'	   => ucwords($this->plugin->slugify($post->user)),
				'username' => $post->user,
				'password' => md5(md5(md5($post->pass)))
			);
			$this->model('Users_Model')->register($array);

			$json['error'] = 0;
			$json['msg'] = 'click <a href="/panel/login">here</a> if not redirect automatically.';
		}

		echo json_encode($json);
	}


	public function edit()
	{
		$user = $this->model('Users_Model');
		$post = json_decode(file_get_contents('php://input'));
	
		if (empty($post->name)) {
			$json['error'] = 1;
			$json['msg'] = 'Name cannot be empty.';
		} elseif (strlen($post->name) > 20) {
			$json['error'] = 1;
			$json['msg'] = 'Name maximum is 20.';
		} elseif (strlen($post->name) < 3) {
			$json['error'] = 1;
			$json['msg'] = 'Name minimum is 3.';
		} else {

			$data = array(
				'name'	=> $post->name
			);
			$currentID = $user->getCurrent()['id'];
			$this->model('Users_Model')->edit($data, $currentID);

			$json['error'] = 0;
			$json['msg'] = 'Profile was successfuly updated.';
		}

		echo json_encode($json);
	}


	public function list($get = 1)
	{
		$limit = 20;
		$pagination = array(
			'get'	=> $get,
			'limit'	=> $limit,
			'start'	=> $limit * $get - $limit
		);

		$post = json_decode(file_get_contents('php://input'));
		$db = $this->model('Users_Model')->list($pagination);

		if (!$post->cors) {
			$json['error'] = 1;
			$json['msg'] = 'Connection is not secure!';
		} else {

			foreach ($db['data'] as $x) {
				$x = (object)$x;
				$custom[] = array(
					'register_at'	=> $this->plugin->formatTime($x->register_at, 'F d, Y - H:i'),
					'username'		=> $x->username,
					'online'		=> $this->plugin->online($x->online) ? 'On':'Off',
					'mod'			=> $this->model('Mod_Model')->select($x->mod)['name'],
					'ban'			=> $x->ban,
					'id'			=> $x->id
				);
			}


			$json['error'] = 0;
			$json['data'] = $custom;
			$json['pagination'] = $db['pagination'];

		}

		echo json_encode($json);
	}


	public function edit_mod()
	{
		$users = $this->model('Users_Model');
		$post = json_decode(file_get_contents('php://input'));

		$myMod = $this->model('Users_Model')->getCurrent()['mod'];
		$mod = $this->model('Mod_Model')->select($myMod);

		if (!$mod['mod']) {
			$json['error'] = 1;
			$json['msg'] = 'Access denied, you dont have permission.';
		} else {
			$users->editMod($post->mod, $post->target);
			$json['error'] = 0;
			$json['msg'] = 'Mod for this user was updated.';
		}

		echo json_encode($json);
	}


	public function ban()
	{
		$users = $this->model('Users_Model');
		$post = json_decode(file_get_contents('php://input'));
		
		$myMod = $this->model('Users_Model')->getCurrent()['mod'];
		$mod = $this->model('Mod_Model')->select($myMod);

		if (!$mod['mod']) {
			$json['error'] = 1;
			$json['msg'] = 'Access denied, you dont have permission.';
		} else {
			if ($users->getCurrent()['id'] !== $post->id) {
				$users->ban($post);
				$json['error'] = 0;
				$json['msg'] = 'User was successfuly banned.';
			} else {
				$json['error'] = 1;
				$json['msg'] = 'Cannot ban yourself.';
			}
		}

		echo json_encode($json);
	}

}