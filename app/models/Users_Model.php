<?php

class Users_Model extends Database {

	public function verify($cookie)
	{
		return $this->num_rows("SELECT COUNT(id) FROM tb_users WHERE ban = 0 AND logged = " . (int)$cookie);
	}


	public function count()
	{
		$data['total'] = $this->num_rows("SELECT COUNT(id) FROM tb_users");
		$data['week'] = $this->num_rows("SELECT COUNT(id) FROM tb_users WHERE UNIX_TIMESTAMP(register_at) >= UNIX_TIMESTAMP(CURRENT_TIMESTAMP()) - (60*60*24*7)");
		return $data;
	}


	public function register($data)
	{
		$usersEmpty = $this->num_rows("SELECT COUNT(id) FROM tb_users");
		$mod = $usersEmpty > 0 ? null: 1;
		$this->query("INSERT INTO tb_users
					SET `name` = ?,
						`username` = ?,
						`password` = ?,
						`logged` = ?,
						`online` = ?,
						`mod` = ?,
						`register_at` = ?");
		$this->bind(1, $data['name']);
		$this->bind(2, $data['username']);
		$this->bind(3, $data['password']);
		$this->bind(4, rand(0000,9999)+rand(0000,1111));
		$this->bind(5, time());
		$this->bind(6, $mod);
		$this->bind(7, date('Y-m-d H:i:s', time()));
		$this->execute();
	}


	public function edit($data, $id)
	{
		$this->query("UPDATE tb_users SET name = ? WHERE id = ?");
		$this->bind(1, $data['name']);
		$this->bind(2, $id);
		$this->execute();
	}

	public function editMod($mod, $target)
	{
		$this->query("UPDATE tb_users SET `mod` = ? WHERE id = ?");
		$this->bind(1, $mod);
		$this->bind(2, $target);
		$this->execute();
	}


	public function getCurrent()
	{
		$key = $this->quote($_COOKIE['logged']);
		$this->query("SELECT * FROM tb_users WHERE logged = $key");
		$this->execute();
		return $this->return('fetch', PDO::FETCH_ASSOC);
	}


	public function get($id)
	{
		$this->query("SELECT * FROM tb_users WHERE id = $id");
		$this->execute();
		return $this->return('fetch', PDO::FETCH_ASSOC);
	}


	public function updateOnlineActivity()
	{
		$this->query("UPDATE tb_users SET online = ? WHERE logged = ?");
		$this->bind(1, time());
		$this->bind(2, $_COOKIE['logged']);
		$this->execute();
	}


	public function list($pagination)
	{
		$num = $this->num_rows("SELECT COUNT(id) FROM tb_users");

		$this->query("SELECT * FROM tb_users ORDER BY id LIMIT ".$pagination['start'].",".$pagination['limit']);
		$this->execute();
		
		$data['pagination'] = array_merge($pagination, array(
			'num' => $num,
			'page' => ceil($num/$pagination['limit'])
		));
		$data['data'] = $this->return('fetchAll', PDO::FETCH_ASSOC);
		return $data;
	}


	public function ban($data)
	{
		$ban = $data->ban ? 0: 1;

		$this->query("UPDATE tb_users SET ban = ? WHERE id = ?");
		$this->bind(1, $ban);
		$this->bind(2, $data->id);
		$this->execute();
	}

}