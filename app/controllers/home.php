<?php

class Home extends Controllers {

	public function main($slug = null)
	{
		$limit = $this->blog['per_page'];
		$pagination = array(
			'get'	=> 1,
			'limit'	=> $limit,
			'start'	=> $limit * 1 - $limit
		);

		$cats = $this->model('Category_Model');
		$post_model = $this->model('Posts_Model');
		$checkSlug = "SELECT COUNT(id) FROM tb_posts WHERE slug = '$slug'";
		$postList = $post_model->list($pagination, 1);

		$data['slug'] = $slug;
		$data['post'] = null;
		
		if (empty($slug))
		{
			$data['empty'] = $post_model->count(1) ? 0 : 1;
			
			foreach ($postList['posts'] as $x) {
				$x = (object)$x;
				$category = array();

				foreach ($cats->select($x->category_id) as $catz) {

					$catz = (object)$catz;
					array_push($category, '<a href="/category/'.$catz->slug.'">'.$catz->name.'</a>');
				}
	
				$postsData['post'][] = array(
					'title'		=> $x->title,
					'slug'		=> $x->slug,
					'summary'	=> $this->plugin->summarize($x->text, 200),
					'date'		=> $this->plugin->formatTime($x->created_time, 'F d, Y'),
					'category'		=> implode(' ', $category)
				);

				$data['posts'] = $this->include('home/list', $postsData, 0);
			
			}

			$data['pagination'] = $this->pagination($postList['pagination']);

			$this->home_header();
			$this->view('home/index', $data, 0);
		} else {

			$data['empty'] = $post_model->exists($slug, 1) ? 0 : 1;

			$x = (object)$post_model->selectSlug($slug);

			foreach ($cats->select($x->category_id) as $catz) {
				$catz = (object)$catz;
				$category[] = '<a href="/category/'.$catz->slug.'">'.$catz->name.'</a>';
			}
			
			$data['post'] = array(
				'title'		=> $x->title,
				'slug'		=> $x->slug,
				'text'		=> nl2br($x->text),
				'date'		=> $this->plugin->formatTime($x->created_time, 'F d, Y'),
				'category'		=> implode(', ', $category)
			);

			$desc = $this->plugin->summarize($x->text, 160);
			$this->home_header($x->title, $desc);
			$this->view('home/read', $data, 0);
		}

		$this->home_footer();
	}

	public function page($get)
	{
		$limit = $this->blog['per_page'];
		$pagination = array(
			'get'	=> $get,
			'limit'	=> $limit,
			'start'	=> $limit * $get - $limit
		);

		$cats = $this->model('Category_Model');
		$post_model = $this->model('Posts_Model');
		$postList = $post_model->list($pagination, 1);

		$data['empty'] = $post_model->count(1) ? 0 : 1;
			
		foreach ($postList['posts'] as $x) {
			$x = (object)$x;
			$category = array();

			foreach ($cats->select($x->category_id) as $catz) {

				$catz = (object)$catz;
				array_push($category, '<a href="/category/'.$catz->slug.'">'.$catz->name.'</a>');
			}

			$postsData['post'][] = array(
				'title'		=> $x->title,
				'slug'		=> $x->slug,
				'summary'	=> $this->plugin->summarize($x->text, 200),
				'date'		=> $this->plugin->formatTime($x->created_time, 'F d, Y'),
				'category'		=> implode(' ', $category)
			);

			$data['posts'] = $this->include('home/list', $postsData, 0);
		
		}

		$data['pagination'] = $this->pagination($postList['pagination']);

		$this->home_header();
		$this->view('home/index', $data, 0);
		$this->home_footer();
	}
	
}