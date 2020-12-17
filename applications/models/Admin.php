<?php

namespace applications\models;

use applications\core\Model;
use Imagick;

class Admin extends Model {

	public $error;

	public function loginValidate($post) {
		$cfg = require 'applications/config/admin.php';
		if (($cfg['login'] != $post['login']) or ($cfg['password'] != $post['password'])) {
			$this->error = 'Неправильно введён логин или пароль.';
			return false;
		}
	return true;
	}

	public function postValidate($post, $type) {
		$titlelen = iconv_strlen($post['title']);
		//$descriptionlen = iconv_strlen($post['description']);
		$textlen = iconv_strlen($post['text']);
		if ($titlelen <= 1 or $titlelen > 100) {
			$this->error = 'Длинна заголовка должна быть от 2 до 100 символов.';
			return false;
		/*} elseif ($descriptionlen < 3 or $descriptionlen > 100) {
			$this->error = 'Длинна описания должна быть от 3 до 100 символов.';
			return false;*/
		} elseif ($textlen < 3 or $textlen > 255) {
			$this->error = 'Длинна сообщения должна быть от 3 до 255 символов.';
			return false;
		}
		if (empty($_FILES['img']['tmp_name']) and $type == 'add') {
				$this->error = 'Не выбран файл.';
				return false;
		}
		return true;
	}

	public function postAdd($post) {
		$params = [
			'id' => '',
			'title' => $post['title'],
			'text' => $post['text'],
		];
		$this->db->query('INSERT INTO news (`id`, `title`, `text`) VALUES (:id, :title, :text) ', $params);
		return $this->db->lastInsertId();
	}

	public function postEdit($post, $id) {
		$params = [
			'id' => $id,
			'title' => $post['title'],
			'text' => $post['text'],
		];
		$this->db->query('UPDATE news SET title = :title, text = :text WHERE id = :id ', $params);
	}

	public function postUploadImage($path, $id) {
		$img = new Imagick($path);
		$img->cropThumbnailImage(600, 600);
		$img->setImageCompressionQuality(80);
		$img->writeImage('public/materials/'.$id.'.jpg');
	}

	public function isPostExists($id) {
		$params = [
			'id' => $id,
		];
		return $this->db->column('SELECT id FROM news WHERE id = :id', $params);
	}

	public function postDelete($id) {
		$params = [
			'id' => $id,
		];
		$this->db->query('DELETE FROM news WHERE id = :id', $params);
		unlink('public/materials/'.$id.'.jpg');
	}

	public function postData($id) {
		$params = [
			'id' => $id,
		];
		return $this->db->row('SELECT * FROM news WHERE id = :id', $params);
	}
}