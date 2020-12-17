<?php

namespace applications\models;

use applications\core\Model;

class Main extends Model {

	public $error;
	
	public function contactValidate($post)
	{	
		$namelen = iconv_strlen($post['name']);
		$textlen = iconv_strlen($post['text']);
		if ($namelen <= 1 or $namelen > 20)
		{
			$this->error = 'Длинна имени должна быть от 2 до 20 символов.';
			return false;
		} elseif (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)){
			$this->error = 'Адрес электронной почны указан неверно.';
			return false;
		} elseif ($textlen < 3 or $textlen > 255) {
			$this->error = 'Длинна сообщения должна быть от 3 до 255 символов.';
			return false;
		}
		return true;
	}

	public function getNews() {
		$result = $this->db->row('SELECT * FROM news');
		return $result;
	}

	public function postsCount() {
		return $this->db->column('SELECT COUNT(id) FROM news');
	}

	public function newsList($route) {
		$max = 10;
		$params = [
			'max' => $max,
			'start' => (($route['page'] ?? 1) - 1 ) * $max,
		];
		return $this->db->row('SELECT * FROM news ORDER BY pubdate DESC LIMIT :start, :max', $params);
	}
}