<?php

class Gallery extends Controllers
{
	public function list()
	{
		$db = $this->model('Gallery_Model');
		$post = json_decode(file_get_contents('php://input'));
		if (!$post->cors) {
			$json['error'] = 1;
			$json['msg'] = 'Not allowed connection.';
		} else {
			$json['error'] = 0;
			$json['count'] = $db->count();
			$json['data'] = $db->list();
		}
		echo json_encode($json);
	}
}