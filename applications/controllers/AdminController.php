<?php

namespace applications\controllers;

use applications\core\Controller;
use applications\lib\Pagination;
use applications\models\Main;

class AdminController extends Controller{

	public function __construct($route) {
		parent::__construct($route); //Вызывает конструктор в Controller, чтобы подключить View
		$this->view->layout = 'admin'; //Изменяет свойство layout во View с default на admin
	}

	public function loginAction() {
		if (isset($_SESSION['administrator'])) {
			$this->view->redirect('admin/add');
		}
		if (!empty($_POST)){
			if (!$this->model->loginValidate($_POST)) {
				$this->view->message('error', $this->model->error);
			}
			$_SESSION['administrator'] = true;
			$this->view->redirect_j('admin/add');
		}
		$this->view->render('Вход');
	}

	public function addAction() {
		if (!empty($_POST)){
			if (!$this->model->postValidate($_POST, 'add')) {
				$this->view->message('error', $this->model->error);
			}
			$id = $this->model->postAdd($_POST);
			if(!$id){
				$this->view->message('error', 'Ошибка при добавлении файла.');
			}
			$this->model->postUploadImage($_FILES['img']['tmp_name'], $id);
			$this->view->message('success', 'Добавление выполнено.');
		}
		$this->view->render('Добавление');
	}

	public function editAction() {
		if (!$this->model->isPostExists($this->route['id'])){
			$this->view->errorCode(404);
		}
		if (!empty($_POST)){
			if (!$this->model->postValidate($_POST, 'edit')) {
				$this->view->message('error', $this->model->error);
			}
			$this->model->postEdit($_POST, $this->route['id']);
			if($_FILES['img']['tmp_name']) {
				$this->model->postUploadImage($_FILES['img']['tmp_name'], $this->route['id']);
				$this->view->message('success', 'Загружен новый файл');
			}
			$this->view->message('success', $this->model->error);
		}
		$vars = [
			'data' => $this->model->postData($this->route['id'])[0],
		];
		$this->view->render('Редактирование', $vars);
	}

	public function logoutAction() {
		unset($_SESSION['administrator']);
		$this->view->redirect('admin/login');
	}

	public function deleteAction() {
		if (!$this->model->isPostExists($this->route['id'])){
			$this->view->errorCode(404);
		}
		$this->model->postDelete($this->route['id']);
		$this->view->redirect('admin/posts');
	}

	public function postsAction() {
		$mainModel = new Main;
		$pagination = new Pagination($this->route, $mainModel->postsCount());
		$vars = [
			'pagination' => $pagination->get(),
			'list' => $mainModel->newsList($this->route),
		];
		$this->view->render('Список постов', $vars);
	}
}