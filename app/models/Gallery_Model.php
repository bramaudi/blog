<?php

class Gallery_Model extends Database {

	public function count()
	{
		return $this->num_rows("SELECT COUNT(id) FROM tb_gallery");
	}

	public function album($album)
	{
		$this->quote($album);
		$this->query("SELECT * FROM tb_gallery WHERE album_id = $album");
		$this->execute();
		return $this->return('fetchAll', PDO::FETCH_ASSOC);
	}

	public function postsCount($id, $public = 0)
	{
		$id = $this->quote('%'.$id.'%');
		$public = $public ? 'status  1 AND': '';
		return $this->num_rows("SELECT COUNT(id) FROM tb_posts WHERE ".$public." category_id LIKE ".$id);
	}

	public function list()
	{
		$this->query("SELECT * FROM tb_gallery ORDER BY created_at");
		$this->execute();
		return $this->return('fetchAll', PDO::FETCH_ASSOC);
	}

	public function create($data)
	{
		$this->query("INSERT INTO tb_categories SET `name` = ?, `description` = ?, `slug` = ?, status = 1");
		$this->bind(1, $data['name']);
		$this->bind(2, $data['desc']);
		$this->bind(3, $data['slug']);
		$this->execute();

		return $this->num_rows("SELECT COUNT(id) FROM tb_categories WHERE slug = '".$data['slug']."'");
	}

	public function edit($data, $id)
	{
		$this->query("UPDATE tb_categories SET `name` = ?, `description` = ?, `slug` = ?, status = 1 WHERE id = ".$id);
		$this->bind(1, $data['name']);
		$this->bind(2, $data['desc']);
		$this->bind(3, $data['slug']);
		$this->execute();

		return $this->num_rows("SELECT COUNT(id) FROM tb_categories WHERE slug = '".$data['slug']."'");
	}

	public function delete($id)
	{
		$this->query("DELETE FROM tb_categories WHERE id = ".$id);
		$this->execute();

		// Rearrange Autoincrement ID
		$this->query("SELECT id FROM tb_categories ORDER BY id");
		$this->execute();
		$loop = $this->return('fetchAll', PDO::FETCH_ASSOC);
		$no = 0;
		foreach ($loop as $x)
		{
			$no++;
			$this->query("UPDATE tb_categories SET id = ? WHERE id = ?");
			$this->bind(1, $no);
			$this->bind(2, $x['id']);
			$this->execute();
		}

		$this->query("ALTER TABLE tb_categories AUTO_INCREMENT = ?");
		$this->bind(1, $no);
		$this->execute();
		
		return $this->num_rows("SELECT COUNT(id) FROM tb_categories WHERE id = ".$id) == 1 ? 0 : 1;
	}

	public function select($id)
	{
		$id = explode(',', $id);
		$data = array();

		if (is_array($id)) {
			foreach ($id as $target) {
				$this->query("SELECT * FROM tb_categories WHERE id = ". $target);
				$this->execute();
				$data[] = $this->return('fetch', PDO::FETCH_ASSOC);
			}
		} else {
			$this->query("SELECT * FROM tb_categories WHERE id = ". $target);
			$this->execute();
			$data[] = $this->return('fetch', PDO::FETCH_ASSOC);
		}

		return $data;
	}

	public function findid($slug)
	{
		return (int)$this->num_rows("SELECT id FROM tb_categories WHERE slug = '$slug'");
	}

}