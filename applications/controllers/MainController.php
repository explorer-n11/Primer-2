<?php

namespace applications\controllers;

use applications\core\Controller;
use applications\lib\Pagination;
use applications\models\Admin;

class MainController extends Controller{

	public function indexAction() {
		$pagination = new Pagination($this->route, $this->model->postsCount());
		$vars = [
			'pagination' => $pagination->get(),
			'list' => $this->model->newsList($this->route),
		];
		$this->view->render('Главная страница', $vars);
	}

	public function aboutAction() {
		$this->view->render('Обо мне');
	}

	public function contactAction() {
		if (!empty($_POST)){
			if (!$this->model->contactValidate($_POST)) {
				$this->view->message('error', $this->model->error);
			}
			mail('explorer.n11@yandex.ru', 'Сообщение из блога от пользователя: ',$_POST['name'].' - '.$_POST['email'].' | '.$_POST['text']);
			$this->view->message('success', 'Сообщение отправлено администратору.');
		}
		$this->view->render('Контакты');
	}

	public function postAction() {
		$adminModel = new Admin;
		if (!$adminModel->isPostExists($this->route['id'])) {
			$this->view->errorCode(404);
		}
		$vars = [
			'data' => $adminModel->postData($this->route['id'])[0],
		];
		$this->view->render('Пост', $vars);
	}
}