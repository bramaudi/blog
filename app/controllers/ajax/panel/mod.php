<?php

class Mod extends Controllers
{

	public function select()
	{
		$post = json_decode(file_get_contents('php://input'));
		if (!$post->cors) {
			$json['error'] = 1;
			$json['msg'] = 'Connection not allowed.';
		} else {
			$json['error'] = 0;
			$json['data'] = $this->model('Mod_Model')->select($post->id);
		}
		echo json_encode($json);
	}

	public function list()
	{
		$db = $this->model('Mod_Model');
		$json['count'] = $db->count();
		foreach ($db->list() as $item) {
			$data = $db->select($item['id']);
			$data = array_merge($data, array('users' => $db->users($item['id'])));
			$json['data'][] = $data;
		}
		echo json_encode($json);
	}

	public function create()
	{
		$db = $this->model('Mod_Model');
		$post = json_decode(file_get_contents('php://input'));
		$duplicate = $db->duplicate($post->name);
		
		$my = $this->model('Users_Model')->getCurrent();
		$mod = $db->select($my['mod']);

		if (!$post->cors) {
			$json['error'] = 1;
			$json['msg'] = 'Connection not allowed.';
		} elseif (!$mod['mod']) {
			$json['error'] = 1;
			$json['msg'] = 'Access denied, you dont have permission.';
		} elseif (empty($post->name)) {
			$json['error'] = 1;
			$json['msg'] = 'Name cannot be empty.';
		} elseif (strlen($post->name) > 10) {
			$json['error'] = 1;
			$json['msg'] = 'Name maximum is 10.';
		} elseif ($duplicate) {
			$json['error'] = 1;
			$json['msg'] = 'Name already exists.';
		} else {
			
			$db->create($post);

			$json['error'] = 0;
			$json['msg'] = 'Permission profile was created.';
		}

		echo json_encode($json);
	}

	public function edit()
	{
		$db = $this->model('Mod_Model');
		$post = json_decode(file_get_contents('php://input'));
		$duplicate = $db->duplicateUpdate($post->name, $post->id);

		$my = $this->model('Users_Model')->getCurrent();
		$mod = $db->select($my['mod']);

		if (!$post->cors) {
			$json['error'] = 1;
			$json['msg'] = 'Connection not allowed.';
		} elseif (!$mod['mod']) {
			$json['error'] = 1;
			$json['msg'] = 'Access denied, you dont have permission.';
		} elseif (empty($post->name)) {
			$json['error'] = 1;
			$json['msg'] = 'Name cannot be empty.';
		} elseif (strlen($post->name) > 10) {
			$json['error'] = 1;
			$json['msg'] = 'Name maximum is 10.';
		} elseif ($duplicate) {
			$json['error'] = 1;
			$json['msg'] = 'Name already exists.';
		} else {
			
			$db->edit($post, $post->id);

			$json['error'] = 0;
			$json['msg'] = 'Settings was successfuly updated.';
		}

		echo json_encode($json);
	}

	public function delete()
	{
		$post = json_decode(file_get_contents('php://input'));

		$my = $this->model('Users_Model')->getCurrent();
		$mod = $this->model('Mod_Model')->select($my['mod']);

		if (!$post->cors) {
			$json['error'] = 1;
			$json['msg'] = 'Connection not allowed.';
		} elseif (!$mod['mod']) {
			$json['error'] = 1;
			$json['msg'] = 'Access denied, you dont have permission.';
		} else {
			$this->model('Mod_Model')->delete($post->id);
			$json['error'] = 0;
			$json['msg'] = 'Role was deleted.';
		}

		echo json_encode($json);
	}
}