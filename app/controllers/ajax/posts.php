<?php

class Posts extends Controllers
{
	public function main()
	{
		$post = json_decode(file_get_contents('php://input'));
		$public = (int)$post->public;
		$db = (object)$this->model('Posts_Model')->search($post->keywords, $public);
		
		$json['count'] = $db->count;

		foreach ($db->data as $x) {
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

		$json['data'] = $data;
		echo json_encode($json);
	}

	public function read()
	{
		$db = $this->model('Posts_Model');
		$cats = $this->model('Category_Model');
		$public = 1;
		$slug = json_decode(file_get_contents('php://input'))->slug;

		
		$json['count'] = $db->exists($slug, $public);
		if ($json['count']) {
			
			$x = $db->selectSlug($slug);

			foreach ($cats->select($x->category_id) as $catz) {
				$category[] = '<a href="/category/'.$catz['slug'].'">'.$catz['name'].'</a>';
			}
			
			$json['data'] = array(
				'title'		=> $x->title,
				'slug'		=> $x->slug,
				'text'		=> nl2br($x->text),
				'date'		=> $this->plugin->formatTime($x->created_time, 'F d, Y'),
				'category'		=> implode(', ', $category)
			);
		} else {
			$json['data'] = null;
		}

		echo json_encode($json);
	}

}