<?php
date_default_timezone_set('America/Caracas');
define("APP_NAME", "DSG");

// Default user configs (estas constantes pueden sobrescribirse
// en el archivo user_config.php)
defined('LOCAL_DIR') or define("LOCAL_DIR", "/DSG-Appweb");

defined('DEVELOPER_MODE') or define("DEVELOPER_MODE", false);

defined('DB_HOST') or define("DB_HOST", "localhost");
defined('DB_NAME') or define("DB_NAME", "dsg");
defined('DB_USER') or define("DB_USER", "root");
defined('DB_PASSWORD') or define("DB_PASSWORD", "");

// Expresiones regulares
define("REG_NUMERICO", "/^[0-9]+$/");
define("REG_ALFABETICO", "/^\s*[a-zA-ZáÁéÉíÍóÓúÚüÜñÑ., ]*\s*$/");
define("REG_ALFANUMERICO", "/^\s*[0-9a-zA-ZáÁéÉíÍóÓúÚüÜñÑ., ]*\s*$/");
define("REG_CLAVE", "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/");