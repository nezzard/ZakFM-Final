<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'admin_zakfm');

/** Имя пользователя MySQL */
define('DB_USER', 'admin_zakfm');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', 'oIOAFUzcsy');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8mb4');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         ':EovfsQqZ;s^,yp<q&uW$C@x0R07P]Gw@.o;rjj)T`cmzTlb*oF{Lo^,(ab]XOSj');
define('SECURE_AUTH_KEY',  '0%]3t0dW_Pq>r/o8PU`eHE>X#R2)u1o?oHo2e8B(Ri!f1JhI}BD9otC*FY=|=lZy');
define('LOGGED_IN_KEY',    '(;2J]6|]L9fv-+-el 0.az|03,*{( dh|RD/vq nETH|}F9;dh3}V}BGuFt=8+]h');
define('NONCE_KEY',        'w0.O8P;8Aq]&XcfdV!N08m&MiEL6=bJ&DYM ,p*0H88*_0HzM^;lpv3TWi]v%%4>');
define('AUTH_SALT',        '0i _qYX#}PVM=(Af<*e5$S7Tl%zJ>=cC^S43C$,W|](]v-iek(%;T>:Clz,Zax$:');
define('SECURE_AUTH_SALT', 'e=FFa]i/~(n3U##u^t%:qFBg`)#7Za}*{?H <tilS7(dv1yhByQy`W2D[COo%^n2');
define('LOGGED_IN_SALT',   '}-}x%DI4D>|}9:q?{t14fz;w&bV~w;DcE[$Y{y*l8jt-<3X5~RE?wa9%DC>=cr-H');
define('NONCE_SALT',       '4@SFW2w3/:},2mC0W7o* @+FD;4A$Xm]DBDJyDJ@TuB+p*5fi .tFHy1@>X,xV0[');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 * 
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
