<?php

class Setting extends Controllers
{
	public function main()
	{
		$post = json_decode(file_get_contents('php://input'));

		if (!$post->cors) {
			$json['error'] = 1;
			$json['msg'] = 'Connection not allowed.';
		} elseif (empty($post->blog_name)) {
			$json['error'] = 1;
			$json['msg'] = 'Blog name cannot be empty.';
		} elseif (strlen($post->blog_name) > 20) {
			$json['error'] = 1;
			$json['msg'] = 'Blog name maximum is 20.';
		} elseif (empty($post->blog_keywords)) {
			$json['error'] = 1;
			$json['msg'] = 'Keywords cannot be empty.';
		} elseif (strlen($post->blog_keywords) > 255) {
			$json['error'] = 1;
			$json['msg'] = 'Keywords maximum is 255.';
		} elseif (empty($post->blog_desc)) {
			$json['error'] = 1;
			$json['msg'] = 'Description cannot be empty.';
		} elseif (strlen($post->blog_desc) > 255) {
			$json['error'] = 1;
			$json['msg'] = 'Description maximum is 255.';
		} else {
			
			$this->model('Settings_Model')->update(array(
				'blog_name'		=> $post->blog_name,
				'keywords'		=> $post->blog_keywords,
				'description'	=> $post->blog_desc,
				'per_page'		=> $post->per_page,
				'open_comment'	=> $post->open_comment,
				'comment_moderation' => $post->comment_moderation,
				'open_register' => $post->open_register
			));

			$json['error'] = 0;
			$json['msg'] = 'Setting was saved.';

		}

		echo json_encode($json);
	}
}