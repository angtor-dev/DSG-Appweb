<?php
date_default_timezone_set('America/Caracas');
const APP_NAME = "DSG";

// Default user configs (estas constantes pueden sobrescribirse
// en el archivo user_config.php)
defined('LOCAL_DIR') or define("LOCAL_DIR", "/DSG-Appweb");

defined('DEVELOPER_MODE') or define("DEVELOPER_MODE", true);

defined('DB_HOST') or define("DB_HOST", "localhost");
defined('DB_NAME') or define("DB_NAME", "dsg_db_testmerge");
defined('DB_USER') or define("DB_USER", "root");
defined('DB_PASSWORD') or define("DB_PASSWORD", "");

defined('DB_USERS_HOST') or define("DB_USERS_HOST", "localhost");
defined('DB_USERS_NAME') or define("DB_USERS_NAME", "dsg_db_users");
defined('DB_USERS_USER') or define("DB_USERS_USER", "root");
defined('DB_USERS_PASSWORD') or define("DB_USERS_PASSWORD", "");
defined('DB_USERS_PORT') or define("DB_USERS_PORT", "");


// defined('DB_USERS_HOST') or define("DB_USERS_HOST", "61z0o.h.filess.io");
// defined('DB_USERS_NAME') or define("DB_USERS_NAME", "dsgusers_tiredsleep");
// defined('DB_USERS_USER') or define("DB_USERS_USER", "dsgusers_tiredsleep");
// defined('DB_USERS_PASSWORD') or define("DB_USERS_PASSWORD", "4ace3bab5b2d174f65e4520361fb21c267a75084");
// defined('DB_USERS_PORT') or define("DB_USERS_PORT", "port=61000;");


// Expresiones regulares
const REG_NUMERICO = "/^[0-9]+$/";
const REG_ALFABETICO = "/^[a-zA-ZáÁéÉíÍóÓúÚüÜñÑ.,\s_-]+$/";
const REG_ALFANUMERICO = "/^\s*[0-9a-zA-ZáÁéÉíÍóÓúÚüÜñÑ., _-]*\s*$/";
const REG_CEDULA = "/^[0-9]{7,8}$/";
const REG_TELEFONO = "/^[0-9]{11}$/";
const REG_CLAVE = "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/";
const REG_FECHA = "/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/";