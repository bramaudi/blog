<?php

class Settings_Model extends Database
{
	public function get()
	{
		$this->query("SELECT * FROM tb_settings");
		$this->execute();
		$data = $this->return('fetch', PDO::FETCH_ASSOC);
		return array(
			'name'				 => $data['blog_name'],
			'description'		 => $data['description'],
			'keywords'			 => $data['keywords'],
			'per_page'			 => $data['per_page'],
			'comment_moderation' => $data['comment_moderation'],
			'open_register'		 => $data['open_register'],
			'open_comment'		 => $data['open_comment']
		);
	}

	public function update($data)
	{
		$x = (object)$data;
		$this->query("UPDATE tb_settings SET
			blog_name = ?,
			keywords = ?,
			`description` = ?,
			per_page = ?,
			comment_moderation = ?,
			open_register = ?,
			open_comment = ?");
		$this->bind(1, $x->blog_name);
		$this->bind(2, $x->keywords);
		$this->bind(3, $x->description);
		$this->bind(4, $x->per_page);
		$this->bind(5, $x->comment_moderation);
		$this->bind(6, $x->open_register);
		$this->bind(7, $x->open_comment);
		return $this->execute();

	}
}