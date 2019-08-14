<?php

class Posts extends Controllers {

	public function main()
	{
		$this->list();
	}

	public function select()
	{
		$post = json_decode(file_get_contents('php://input'));
		
		$json['error'] = 0;
		$json['data'] = $this->model('Posts_Model')->select($post->id);

		echo json_encode($json);
	}

	public function list($get = 1)
	{
		$limit = 10;
		$pagination = array(
			'get'	=> $get,
			'limit'	=> $limit,
			'start'	=> $limit * $get - $limit
		);

		$posts = $this->model('Posts_Model');
		$postsList = $posts->list($pagination);
		$data = [];
		foreach ($postsList['posts'] as $x) {
			$x = (object)$x;
			$data[] = array(
				'id'	=> $x->id,
				'title' => $x->title,
				'slug'	=> $x->slug,
				'created_time' => $this->plugin->formatTime($x->created_time, 'd F Y  (H:i:s)'),
				'updated_time' => $x->updated_time,
				'text'	=> $x->text,
				'category_id' => $x->category_id,
				'tags' 	=> $x->tags,
				'status' => $x->status
			);
		}
		if ($posts->count()) {
			$json['empty'] = 0;
			$json['pagination'] = $postsList['pagination'];
			$json['data'] = $data;
		} else {
			$json['empty'] = 1;
			$json['data'] = null;
		}

		echo json_encode($json);
	}

	public function create()
	{
		$db = $this->model('Posts_Model');
		$post = json_decode(file_get_contents('php://input'));
		$slug = empty($post->slug) ? $this->plugin->slugify($post->title) : $post->slug;
		$slugExists = $db->exists($slug);

		$myCurrentID = $this->model('Users_Model')->getCurrent()['id'];
		$mod_allowed = $this->model('Mod_Model')->select($myCurrentID)['post_create'];

		if (!$mod_allowed) {
			$json['error'] = 1;
			$json['msg'] = 'Access denied, you dont have permission.';
		} elseif (empty($post->title)) {
			$json['error'] = 1;
			$json['msg'] = 'Title cannot be empty.';
		} elseif (strlen($post->title) > 160) {
			$json['error'] = 1;
			$json['msg'] = 'Title maximal is 160.';
		} elseif (strlen($post->slug) > 160) {
			$json['error'] = 1;
			$json['msg'] = 'Slug maximal is 160.';
		} elseif ($slugExists) {
			$json['error'] = 1;
			$json['msg'] = 'Post already exists (slug duplicate).';
		} elseif (empty($post->text)) {
			$json['error'] = 1;
			$json['msg'] = 'Text content cannot be empty.';
		} elseif (strlen($post->tags) > 255) {
			$json['error'] = 1;
			$json['msg'] = 'Tags maximal is 255.';
		}
		else {
			
			// Uncategorized ID is 1
			$category = empty($post->category) ? 1: $post->category;

			$ok = $db->create([
				'title' => $post->title,
				'slug'	=> $slug,
				'text'	=> $post->text,
				'category' => $category,
				'tags'	=> $post->tags,
				'status' => $post->status	
			]);

			if ($ok) {
				$json['error'] = 0;
				$json['msg'] = 'Post was created.';
			} else {
				$json['error'] = 1;
				$json['msg'] = 'Failed while processing Database query.';
			}

		}

		echo json_encode($json);
	}


	public function edit()
	{
		$db = $this->model('Posts_Model');
		$post = json_decode(file_get_contents('php://input'));
		$slug = empty($post->slug) ? $this->plugin->slugify($post->title) : $post->slug;
		$slugExists = $db->duplicate($slug, (int)$post->id);

		$myCurrentID = $this->model('Users_Model')->getCurrent()['id'];
		$mod_allowed = $this->model('Mod_Model')->select($myCurrentID)['post_edit'];

		if (!$mod_allowed) {
			$json['error'] = 1;
			$json['msg'] = 'Access denied, you dont have permission.';
		} elseif (empty($post->title)) {
			$json['error'] = 1;
			$json['msg'] = 'Title cannot be empty.';
		} elseif (strlen($post->title) > 160) {
			$json['error'] = 1;
			$json['msg'] = 'Title maximal is 160.';
		} elseif (strlen($post->slug) > 160) {
			$json['error'] = 1;
			$json['msg'] = 'Slug maximal is 160.';
		} elseif ($slugExists) {
			$json['error'] = 1;
			$json['msg'] = 'Post already exists (slug duplicate).';
		} elseif (empty($post->text)) {
			$json['error'] = 1;
			$json['msg'] = 'Text content cannot be empty.';
		} elseif (strlen($post->tags) > 255) {
			$json['error'] = 1;
			$json['msg'] = 'Tags maximal is 255.';
		}
		else {
			
			// Uncategorized ID is 1
			$category = empty($post->category) ? 1: $post->category;

			$ok = $db->edit([
				'title' => $post->title,
				'slug'	=> $slug,
				'text'	=> $post->text,
				'category' => $category,
				'tags'	=> $post->tags,
				'status' => $post->status	
			], (int)$post->id);
			

			if ($ok) {
				$json['error'] = 0;
				$json['msg'] = 'Post was saved.';
			} else {
				$json['error'] = 1;
				$json['msg'] = 'Failed while processing Database query.';
			}

		}

		echo json_encode($json);
	}

	public function delete()
	{
		$id = json_decode(file_get_contents('php://input'))->id;
		
		$myCurrentID = $this->model('Users_Model')->getCurrent()['id'];
		$mod_allowed = $this->model('Mod_Model')->select($myCurrentID)['post_delete'];
		
		if (!$mod_allowed) {
			$json['error'] = 1;
			$json['msg'] = 'Access denied, you dont have permission.';
		} else {
			$this->model('Posts_Model')->delete($id);
			$json['error'] = 0;
			$json['msg'] = 'Post deleted successfuly';
		}

		echo json_encode($json);
	}
}