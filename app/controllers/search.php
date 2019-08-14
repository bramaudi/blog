<?php

class Search extends Controllers
{
	public function main($query = null)
	{
		$query = rawurldecode($query);
		setcookie('search', $query, time()+3600, '/');
		$this->home_header(empty($query) ? 'Search': 'Search: "'.$query.'"');
		$cats = $this->model('Category_Model');
		$post_model = $this->model('Posts_Model');
		$post_search = (object)$post_model->search($query, 1);

		$data['count'] = $post_search->count;
		$data['empty'] = $data['count'] ? 0: 1;
		$data['keyword'] = strip_tags($query);

			foreach ($post_search->data as $x) {
				$x = (object)$x;
				$category = array();

				foreach ($cats->select($x->category_id) as $catz) {

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

		$this->view('home/search', $data, 0);
		$this->home_footer();
	}


}