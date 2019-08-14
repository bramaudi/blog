<?php

class Category extends Controllers
{
	public function main($slug = null, $get = 1)
	{
		$limit = $this->blog['per_page'];
		$pagination = array(
			'get'	=> $get,
			'limit'	=> $limit,
			'start'	=> $limit * $get - $limit
		);

		$category_model = $this->model('Category_Model');
		$post_model = $this->model('Posts_Model');

		$category_id = $category_model->findid($slug);
		$post_category = (object)$post_model->category($category_id, $pagination, 1);
		$category_data = (object)$category_model->select($category_id)[0];

		if (!$category_id)
		{
			$this->home_header('Category Not Found');
			$data['exists'] = 0;
			$data['empty'] = 1;
			$data['post'] = null;
		}

		else {

			$this->home_header($category_data->name);
			$data['category'] = array(
				'name'	=> $category_data->name,
				'info'	=> $category_data->description,
				'posts'	=> $post_category->count
			);
			$data['exists'] = 1;
			$data['empty'] = $post_category->count ? 0 : 1;

			foreach ($post_category->data as $x) {
				$x = (object)$x;
				$category = array();
			
				foreach ($category_model->select($x->category_id) as $catz) {
					$catz = (object)$catz;
					array_push($category, '<a href="/category/'.$catz->slug.'">'.$catz->name.'</a>');
				}
	
				$postData['post'][] = array(
					'title'		=> $x->title,
					'slug'		=> $x->slug,
					'summary'	=> $this->plugin->summarize($x->text, 200),
					'date'		=> $this->plugin->formatTime($x->created_time, 'F d, Y'),
					'category'		=> implode(' ', $category)
				);

				$data['posts'] = $this->include('home/list', $postData, 0);
			}

			$data['pagination'] = $this->pagination($post_category->pagination, '/category/page/'.$slug.'/');

		}

		$this->view('home/category', $data, 0);
		$this->home_footer();
		
	}



	public function page($slug, $get = 1)
	{
		$this->main($slug, $get);
	}

}