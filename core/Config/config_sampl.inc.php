<?php
// Строка соединения с БД. Тип, хост, имя БД и кодировка
$database_dsn = 'mysql:host=127.0.0.1;dbname=s1889;charset=utf8';
// БД юзер
$database_user = 's1889';
// Пароль юзера для БД
$database_password = 'VqSBKfZRf19m';
// Настройки xPDO: кэширование и загрузка свойств и связанных объектов
// Тут лучше ничего не трогать, оставить по умолчанию
$database_options = array(
	\xPDO\xPDO::OPT_CACHE_PATH => PROJECT_CACHE_PATH,
	\xPDO\xPDO::OPT_HYDRATE_FIELDS => true,
	\xPDO\xPDO::OPT_HYDRATE_RELATED_OBJECTS => true,
	\xPDO\xPDO::OPT_HYDRATE_ADHOC_FIELDS => true,
);

if (!defined('PROJECT_BASE_PATH')) {
	define('PROJECT_BASE_PATH', strtr(realpath(dirname(dirname(dirname(__FILE__)))), '\\', '/') . '/');
}

if (!defined('PROJECT_CORE_PATH')) {
	define('PROJECT_CORE_PATH', PROJECT_BASE_PATH . 'core/');
}

if (!defined('PROJECT_TEMPLATES_PATH')) {
	define('PROJECT_TEMPLATES_PATH', PROJECT_CORE_PATH . 'Templates/');
}

if (!defined('PROJECT_CACHE_PATH')) {
	define('PROJECT_CACHE_PATH', PROJECT_CORE_PATH . 'Cache/');
}

if (!defined('PROJECT_FENOM_OPTIONS')) {
	define('PROJECT_FENOM_OPTIONS', Fenom::AUTO_RELOAD | Fenom::FORCE_VERIFY);
}