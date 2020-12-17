<?php

namespace applications\core;

use applications\core\View;

abstract class Controller {

	public $route;
	public $view;
	public $acl;

	public function __construct($route) {
		$this->route = $route;
		if(!$this->checkAcl()) {
			View::errorCode(403);
		};
		$this->view = new View($route);
		$this->model = $this->loadModel($route['controller']);
	}

	public function loadModel($name) {
		$path = 'applications\models\\'.ucfirst($name);
		if (class_exists($path)) {
			return new $path;
		} else {
			echo 'Класс не найден '.$path;
		}
	}

	public function checkAcl() {
		$this->acl = require 'applications/acl/'.$this->route['controller'].'.php';
		if ($this->isAcl('all')) {
			return true;
		}
		elseif (isset($_SESSION['authorize']['id']) and $this->isAcl('aut')) {
			return true;
		}
		elseif (!isset($_SESSION['authorize']['id']) and $this->isAcl('gst')) {
			return true;
		}
		elseif (isset($_SESSION['administrator']) and $this->isAcl('adm')) {
			return true;
		}
		return false;
	}

	public function isAcl($key) {
		return in_array($this->route['action'], $this->acl[$key]);
	}
}