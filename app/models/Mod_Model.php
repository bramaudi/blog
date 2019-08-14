<?php

class Mod_Model extends Database {

	public function count()
	{
		return $this->num_rows("SELECT COUNT(id) FROM tb_mod");
	}

	public function users($id)
	{
		return $this->num_rows("SELECT COUNT(id) FROM tb_users WHERE `mod` = $id");
	}

	public function duplicate($name)
	{
		$name = $this->quote($name);
		return $this->num_rows("SELECT COUNT(id) FROM tb_mod WHERE name = $name");
	}

	public function duplicateUpdate($name, $id)
	{
		$name = $this->quote($name);
		return $this->num_rows("SELECT COUNT(id) FROM tb_mod WHERE name = $name AND id != $id");
	}

	public function select($id)
	{
		$this->query("SELECT * FROM tb_mod WHERE id = $id");
		$this->execute();
		return $this->return('fetch', PDO::FETCH_ASSOC);
	}

	public function list()
	{
		$this->query("SELECT id FROM tb_mod ORDER BY id");
		$this->execute();
		return $this->return('fetchAll', PDO::FETCH_ASSOC);
	}

	public function edit($data, $id)
	{
		$this->query("UPDATE tb_mod
					SET `name` = ?,
						`description` = ?,
						`post_create` = ?,
						`post_edit` = ?,
						`post_delete` = ?,
						`comment_edit` = ?,
						`comment_delete` = ?,
						`comment_approve` = ?,
						`category_create` = ?,
						`category_edit` = ?,
						`category_delete` = ?,
						`file_create` = ?,
						`file_edit` = ?,
						`file_delete` = ?,
						`gallery_create` = ?,
						`gallery_edit` = ?,
						`gallery_delete` = ?,
						`user_create` = ?,
						`user_edit` = ?,
						`user_delete` = ?,
						`setting` = ?,
						`mod` = ?
						
					WHERE id = ?");
		$this->bind(1, $data->name);
		$this->bind(2, $data->description);
		$this->bind(3, $data->post_create);
		$this->bind(4, $data->post_edit);
		$this->bind(5, $data->post_delete);
		$this->bind(7, $data->comment_edit);
		$this->bind(8, $data->comment_delete);
		$this->bind(6, $data->comment_approve);
		$this->bind(9, $data->category_create);
		$this->bind(10, $data->category_edit);
		$this->bind(11, $data->category_delete);
		$this->bind(12, $data->file_create);
		$this->bind(13, $data->file_edit);
		$this->bind(14, $data->file_delete);
		$this->bind(15, $data->gallery_create);
		$this->bind(16, $data->gallery_edit);
		$this->bind(17, $data->gallery_delete);
		$this->bind(18, $data->user_create);
		$this->bind(19, $data->user_edit);
		$this->bind(20, $data->user_delete);
		$this->bind(21, $data->setting);
		$this->bind(22, $data->mod);
		$this->bind(23, $id);
		$this->execute();
	}
	
	public function create($data)
	{
		$this->query("INSERT INTO tb_mod
					SET `name` = ?,
						`description` = ?,
						`post_create` = ?,
						`post_edit` = ?,
						`post_delete` = ?,
						`comment_edit` = ?,
						`comment_delete` = ?,
						`comment_approve` = ?,
						`category_create` = ?,
						`category_edit` = ?,
						`category_delete` = ?,
						`file_create` = ?,
						`file_edit` = ?,
						`file_delete` = ?,
						`gallery_create` = ?,
						`gallery_edit` = ?,
						`gallery_delete` = ?,
						`user_create` = ?,
						`user_edit` = ?,
						`user_delete` = ?,
						`setting` = ?,
						`mod` = ?");
		$this->bind(1, $data->name);
		$this->bind(2, $data->description);
		$this->bind(3, $data->post_create);
		$this->bind(4, $data->post_edit);
		$this->bind(5, $data->post_delete);
		$this->bind(7, $data->comment_edit);
		$this->bind(8, $data->comment_delete);
		$this->bind(6, $data->comment_approve);
		$this->bind(9, $data->category_create);
		$this->bind(10, $data->category_edit);
		$this->bind(11, $data->category_delete);
		$this->bind(12, $data->file_create);
		$this->bind(13, $data->file_edit);
		$this->bind(14, $data->file_delete);
		$this->bind(15, $data->gallery_create);
		$this->bind(16, $data->gallery_edit);
		$this->bind(17, $data->gallery_delete);
		$this->bind(18, $data->user_create);
		$this->bind(19, $data->user_edit);
		$this->bind(20, $data->user_delete);
		$this->bind(21, $data->setting);
		$this->bind(22, $data->mod);
		$this->execute();
	}

	public function delete($id)
	{
		$this->query("DELETE FROM tb_mod WHERE id = ".$id);
		$this->execute();

		// Rearrange Autoincrement ID
		$this->query("SELECT id FROM tb_mod ORDER BY id");
		$this->execute();
		$loop = $this->return('fetchAll', PDO::FETCH_ASSOC);
		$no = 0;
		foreach ($loop as $x)
		{
			$no++;
			$this->query("UPDATE tb_mod SET id = ? WHERE id = ?");
			$this->bind(1, $no);
			$this->bind(2, $x['id']);
			$this->execute();
		}

		$this->query("ALTER TABLE tb_mod AUTO_INCREMENT = ?");
		$this->bind(1, $no);
		$this->execute();
	}

}