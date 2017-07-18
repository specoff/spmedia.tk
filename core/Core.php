<?php

namespace Baluhin;

use \Fenom as Fenom;
use \Exception as Exception;

class Core {

	public $config = array();
	/** @var Fenom $fenom */
	public $fenom;


	/**
	 * Конструктор класса
	 *
	 * @param string $config Имя файла с конфигом
	 */
	function __construct($config = 'config') {
		if (is_string($config)) {
			$config = dirname(__FILE__) . "/Config/{$config}.inc.php";
			if (file_exists($config)) {
				require_once $config;
				/** @var string $database_dsn */
				/** @var string $database_user */
				/** @var string $database_password */
				/** @var array $database_options */
				try {
					$this->xpdo = new xPDO($database_dsn, $database_user, $database_password, $database_options);
					$this->xpdo->setPackage(PROJECT_NAME, PROJECT_MODEL_PATH);
					$this->xpdo->startTime = microtime(true);
				}
				catch (Exception $e) {
					exit($e->getMessage());
				}
			}
			else {
				exit('Не могу загрузить файл конфигурации');
			}
		}
		else {
			exit('Неправильное имя файла конфигурации');
		}
	}


	/**
	 * Обработка входящего запроса
	 *
	 * @param $uri
	 */
	public function handleRequest($uri) {
		// Определяем страницу для вывода
		$request = explode('/', $uri);
		$className = '\Baluhin\Controllers\\' . ucfirst(array_shift($request));
		/** @var Controller $controller */
			if (!class_exists($className)) {
				$controller = new Controllers\Home($this);
		}
		else {
			$controller = new $className($this);
		}
		
		$initialize = $controller->initialize($request);
		if ($initialize === true) {
			$response = $controller->run();
		}
		elseif (is_string($initialize)) {
			$response = $initialize;
		}
		else {
			$response = 'Возникла неведомая ошибка при загрузке страницы';
		}

		echo $response;
	}


	/**
	 * Получение экземпляра класса Fenom
	 *
	 * @return bool|Fenom
	 */
	public function getFenom() {
		if (!$this->fenom) {
			try {
				if (!file_exists($this->config['cachePath'])) {
					mkdir($this->config['cachePath']);
				}
				$this->fenom = Fenom::factory($this->config['templatesPath'], $this->config['cachePath'], $this->config['fenomOptions']);
			}
			catch (Exception $e) {
				$this->log($e->getMessage());
				return false;
			}
		}

		return $this->fenom;
	}


	/**
	 * Метод удаления директории с кэшем
	 *
	 */
	public function clearCache() {
		Core::rmDir($this->config['cachePath']);
		mkdir($this->config['cachePath']);
	}


	/**
	 * Рекурсивное удаление директорий
	 *
	 * @param $dir
	 */
	public function rmDir($dir) {
		$dir = rtrim($dir, '/');
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != '.' && $object != '..') {
					if (is_dir($dir . '/' . $object)) {
						$this->rmDir($dir . '/' . $object);
					}
					else {
						unlink($dir . '/' . $object);
					}
				}
			}
			rmdir($dir);
		}
	}


	/**
	 * Логирование. Пока просто выводит ошибку на экран.
	 *
	 * @param $message
	 * @param $level
	 */
	public function log($message, $level = E_USER_ERROR) {
		if (!is_scalar($message)) {
			$message = print_r($message, true);
			}
		trigger_error($message, $level);
	}

	/**
	 * Удаление ненужных файлов в пакетах, установленных через Composer
	 *
	 * @param mixed $base
	 */
	public static function cleanPackages($base = '') {
		// Composer при вызове метода передаёт внутрь свой объект, но нам это не нужно
		// Значит, если передана не строка, то это первый запуск и мы стартуем от директории вендоров
		if (!is_string($base)) {
			$base = dirname(dirname(__FILE__)) . '/vendor/';
		}
		// Получаем все директории и
		if ($dirs = @scandir($base)) {
			// Проходим по ним в цикле
			foreach ($dirs as $dir) {
				// Символы выхода из директории нас не интересуют
				if (in_array($dir, array('.', '..'))) {
					continue;
				}
				$path = $base . $dir;
				// Если это директория, а не файл
				if (is_dir($path)) {
					// И она в следующем списке
					if (in_array($dir, array('tests', 'test', 'docs', 'gui', 'sandbox', 'examples', '.git'))) {
						// Удаляем её, вместе с поддиректориями
						Core::rmDir($path);
					}
					// А если не в списке - рекурсивно проверяем её дальше, этим же методом
					else {
						// Просто передавая в него нужный путь
						Core::cleanPackages($path . '/');
					}
				}
				// А если это файл, то удаляем все, кроме php
				elseif (pathinfo($path, PATHINFO_EXTENSION) != 'php') {
					unlink($path);
				}
			}
		}
	}
}