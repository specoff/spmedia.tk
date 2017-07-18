<?php

namespace Baluhin\Controllers;

use Baluhin\Controller as Controller;

class Home extends Controller {

	/**
	 * @param array $params
	 *
	 * @return bool
	 */
	public function initialize(array $params = array()) {
		if (!empty($_REQUEST['q'])) {
			$this->redirect('/');
		}
		return true;
	}

	/**
	 * @return string
	 */
	public function run() {
		return $this->template('home', array(
			'title' => 'Главная страница',
			'pagetitle' => 'Третий курс обучения',
			'content' => 'Текст главной страницы',
		), $this);
	}

}