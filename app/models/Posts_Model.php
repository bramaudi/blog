<?php

class Posts_Model extends Database {

	public function count($public = 0)
	{
		if ($public) {
			return $this->num_rows("SELECT COUNT(id) FROM tb_posts WHERE status = 1");
		} else {
			return $this->num_rows("SELECT COUNT(id) FROM tb_posts");
		}
	}

	public function create($data)
	{
		$this->query("INSERT INTO tb_posts
			SET title = ?, slug = ?, `text` = ?, category_id = ?, tags = ?, `status` = ?");
		$this->bind(1, $data['title']);
		$this->bind(2, $data['slug']);
		$this->bind(3, $data['text']);
		$this->bind(4, $data['category']);
		$this->bind(5, $data['tags']);
		$this->bind(6, $data['status']);
		$this->execute();

		return $this->exists($data['slug']);
	}

	public function edit($data, $id)
	{
		$this->query("UPDATE tb_posts
			SET title = ?, slug = ?, `text` = ?, category_id = ?, tags = ?, `status` = ?, updated_time = CURRENT_TIMESTAMP() WHERE id = ".$id);
		$this->bind(1, $data['title']);
		$this->bind(2, $data['slug']);
		$this->bind(3, $data['text']);
		$this->bind(4, $data['category']);
		$this->bind(5, $data['tags']);
		$this->bind(6, $data['status']);
		$this->execute();

		return $this->exists($data['slug']);
	}

	public function list($pagination, $public = 0)
	{
		$public = $public == 1 ? 'WHERE status = 1':'';
		$num = $this->num_rows("SELECT COUNT(*) FROM tb_posts ".$public);

		$this->query("SELECT * FROM tb_posts ".$public." ORDER BY created_time DESC LIMIT ".$pagination['start'].",".$pagination['limit']);
		$this->execute();
		
		$data['pagination'] = array_merge($pagination, array(
			'num' => $num,
			'page' => ceil($num/$pagination['limit'])
		));
		$data['posts'] = $this->return('fetchAll', PDO::FETCH_ASSOC);
		return $data;
	}

	public function search($search, $public = 0)
	{
		$public = $public ? ' AND status = 1 ': '';
		$search = $this->quote('%'.$search.'%');
		$query = "SELECT * FROM tb_posts WHERE title LIKE ".$search." ".$public." OR text LIKE ".$search.$public;
		$this->query($query);
		$this->execute();
		$data['count'] = $this->rowCount();
		$data['data'] = $this->return('fetchAll', PDO::FETCH_ASSOC);
		return $data;
	}

	public function exists($slug, $public = 0)
	{
		if ($public) {
			return $this->num_rows("SELECT COUNT(id) FROM tb_posts WHERE slug = '".$slug."' AND status = 1");
		} else {
			return $this->num_rows("SELECT COUNT(id) FROM tb_posts WHERE slug = '".$slug."'");
		}
	}

	public function duplicate($slug, $id)
	{
		return $this->num_rows("SELECT COUNT(id) FROM tb_posts WHERE slug = '".$slug."' AND id !== ".$id);
	}

	// for panel (private)
	public function select($id)
	{
		$this->query("SELECT * FROM tb_posts WHERE id = ".$id);
		$this->execute();
		return $this->return('fetch', PDO::FETCH_ASSOC);
	}

	// for public
	public function selectSlug($slug)
	{
		$this->query("SELECT * FROM tb_posts WHERE slug = '".$slug."' AND status = 1");
		$this->execute();
		return $this->return('fetch', PDO::FETCH_ASSOC);
	}

	public function category($categoryID, $pagination, $public = 0)
	{
		$public = $public ? ' AND status = 1': '';
		
		
		$data['count'] = $this->num_rows("SELECT COUNT(id) FROM tb_posts WHERE category_id LIKE '%".$categoryID."%'" . $public);
		
		$data['pagination'] = array_merge($pagination, array(
			'num' => $data['count'],
			'page' => ceil($data['count']/$pagination['limit'])
		));

		$this->query("SELECT * FROM tb_posts WHERE category_id LIKE '%".$categoryID."%'" . $public . " ORDER BY created_time DESC LIMIT ".$pagination['start'].",".$pagination['limit']);
		$this->execute();
		$data['data'] = $this->return('fetchAll', PDO::FETCH_ASSOC);
		return $data;
	}

	public function delete($id)
	{
		$this->query("DELETE FROM tb_posts WHERE id = ".$id);
		$this->execute();

		return $this->num_rows("SELECT COUNT(id) FROM tb_posts WHERE id = ".$id) == 0 ? 1 : 0;
	}

}