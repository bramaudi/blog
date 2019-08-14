<?php

class Category extends Controllers {

	public function main(Type $var = null)
	{
		$this->list();
	}

	public function list()
	{
		$db = $this->model('Category_Model');
		if ($db->count())
		{
			$json['empty'] = 0;
			foreach($db->list() as $x) {
				$x = (object)$x;
				$posts = $db->postsCount($x->id);
				$data[] = array(
					'id'	=> $x->id,
					'name'	=> $x->name,
					'description'	=> empty($x->description) ? '-' : $x->description,
					'posts'	=> (int)$posts['count']
				);
			}
			$json['data'] = $data;
		} else {
			$json['empty'] = 1;
			$json['data'] = [];
		}

		echo json_encode($json);
	}


	public function create()
	{
		$db = $this->model('Category_Model');
		$post = json_decode(file_get_contents('php://input'));

		$slug = empty($post->slug) ? $this->plugin->slugify($post->name) : $post->slug;

		$slugExists = $db->num_rows("SELECT COUNT(id) FROM tb_categories WHERE slug = '$slug'");

		$my = $this->model('Users_Model')->getCurrent();
		$mod = $this->model('Mod_Model')->select($my['mod']);

		if (!$post->cors) {
			$json['error'] = 1;
			$json['msg'] = 'Connection not allowed.';
		} elseif (!$mod['category_create']) {
			$json['error'] = 1;
			$json['msg'] = 'Access denied, you dont have permission.';
		} elseif (empty($post->name)) {
			$json['error'] = 1;
			$json['msg'] = 'Name cannot be empty!';
		} elseif (strlen($post->name) < 3) {
			$json['error'] = 1;
			$json['msg'] = 'Name minimum is 3.';
		} elseif (strlen($post->name) > 20) {
			$json['error'] = 1;
			$json['msg'] = 'Name maximum is 20.';
		} elseif (strlen($post->slug) > 20) {
			$json['error'] = 1;
			$json['msg'] = 'Slug maximum is 20.';
		} elseif ($slugExists) {
			$json['error'] = 1;
			$json['msg'] = 'Category already exists (duplicate slug)!.';
		} elseif (strlen($post->desc) > 255) {
			$json['error'] = 1;
			$json['msg'] = 'Description maximum is 255.';
		} else
		{
			$done = $db->create([
				'name' => $post->name,
				'desc' => $post->desc,
				'slug' => $slug
			]);

			if ($done)
			{
				$json['error'] = 0;
				$json['msg'] = 'Category was created!';
			}
			else {
				$json['error'] = 1;
				$json['msg'] = 'Error while connecting to Database!';
			}
		}

		echo json_encode($json);
	}

	public function edit()
	{
		$db = $this->model('Category_Model');
		$post = json_decode(file_get_contents('php://input'));

		$slug = empty($post->slug) ? $this->plugin->slugify($post->name) : $post->slug;

		$slugExists = $db->num_rows("SELECT COUNT(id) FROM tb_categories WHERE slug = '$slug' AND id != ".$post->id);

		$my = $this->model('Users_Model')->getCurrent();
		$mod = $this->model('Mod_Model')->select($my['mod']);

		if (!$post->cors) {
			$json['error'] = 1;
			$json['msg'] = 'Connection not allowed.';
		} elseif (!$mod['category_edit']) {
			$json['error'] = 1;
			$json['msg'] = 'Access denied, you dont have permission.';
		} elseif (empty($post->name)) {
			$json['error'] = 1;
			$json['msg'] = 'Name cannot be empty!';
		} elseif (strlen($post->name) < 3) {
			$json['error'] = 1;
			$json['msg'] = 'Name minimum is 3.';
		} elseif (strlen($post->name) > 20) {
			$json['error'] = 1;
			$json['msg'] = 'Name maximum is 20.';
		} elseif (strlen($post->slug) > 20) {
			$json['error'] = 1;
			$json['msg'] = 'Slug maximum is 20.';
		} elseif ($slugExists) {
			$json['error'] = 1;
			$json['msg'] = 'Category already exists (duplicate slug)!.';
		} elseif (strlen($post->desc) > 255) {
			$json['error'] = 1;
			$json['msg'] = 'Description maximum is 255.';
		} else
		{
			$done = $db->edit([
				'name' => $post->name,
				'desc' => $post->desc,
				'slug' => $slug
			], $post->id);

			if ($done)
			{
				$json['error'] = 0;
				$json['msg'] = 'Category was updated!';
			}
			else {
				$json['error'] = 1;
				$json['msg'] = 'Error while connecting to Database!';
			}
		}

		echo json_encode($json);
	}

	public function delete()
	{
		$post = json_decode(file_get_contents('php://input'));
		$db = $this->model('Category_Model');

		$my = $this->model('Users_Model')->getCurrent();
		$mod = $this->model('Mod_Model')->select($my['mod']);

		if (!$post->cors) {
			$json['error'] = 1;
			$json['msg'] = 'Connection not allowed.';
		} elseif (!$mod['category_delete']) {
			$json['error'] = 1;
			$json['msg'] = 'Access denied, you dont have permission.';
		} else {
			$db->delete($post->id);
			$json['error'] = 0;
			$json['msg'] = 'Category deleted successfuly.';
		}

		echo json_encode($json);
	}

	public function select()
	{
		$id = json_decode(file_get_contents('php://input'))->id;
		$data = $this->model('Category_Model')->select($id);
		$json['empty'] = empty($id) ? 1: 0;
		$json['data'] = empty($id) ? null : $data;
		echo json_encode($json);
	}

}