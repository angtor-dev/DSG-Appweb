<?php
/// Default user configs (estas constantes pueden sobrescribirse en el archivo user_config.php)
if (file_exists("user_config.php"))
    require_once "user_config.php";
date_default_timezone_set('America/Caracas');
const APP_NAME = "DSG";

// Opciones de desarrollo
defined('LOCAL_DIR') or define("LOCAL_DIR", "/DSG-Appweb");
defined('DEVELOPER_MODE') or define("DEVELOPER_MODE", true);
defined('ENABLE_REPORTS') or define("ENABLE_REPORTS", true); // Testlink Reports
defined('APP_URL') or define("APP_URL", "http://localhost/DSG-Appweb/");

// Configuración de la base de datos
defined('DB_HOST') or define("DB_HOST", "localhost");
defined('DB_NAME') or define("DB_NAME", "dsg_db_testmerge");
defined('DB_USER') or define("DB_USER", "root");
defined('DB_PASSWORD') or define("DB_PASSWORD", "");

// Configuración de la base de datos de usuarios
defined('DB_USERS_HOST') or define("DB_USERS_HOST", "localhost");
defined('DB_USERS_NAME') or define("DB_USERS_NAME", "dsg_db_users");
defined('DB_USERS_USER') or define("DB_USERS_USER", "root");
defined('DB_USERS_PASSWORD') or define("DB_USERS_PASSWORD", "");
defined('DB_USERS_PORT') or define("DB_USERS_PORT", "");

// Configuración de la aplicación
defined('DEP_NAME') or define("DEP_NAME", "División"); // para cambiar el nombre al modulo fácilmente
defined('DEP_NAME_M') or define("DEP_NAME_M", "división"); // minúsculas
defined('DEP_NAME_S') or define("DEP_NAME_S", "divisiones"); // plural
defined('ASISTENCIAS_SEMANALES') or define("ASISTENCIAS_SEMANALES", true);

// Credenciales de correo
defined('SYS_EMAIL') or define("SYS_EMAIL", "reivax.zeraus@gmail.com");
defined('SYS_EMAIL_PASS') or define("SYS_EMAIL_PASS", 'gexv tail oxwx ejwt');
defined('SYS_EMAIL_NAME') or define("SYS_EMAIL_NAME", "Dirección de Servicios Generales");
defined('SYS_EMAIL_HOST') or define("SYS_EMAIL_HOST", "smtp.gmail.com");

// Configuración de TestLink y pruebas automatizadas
defined('TEST_LIST') or define('TEST_LIST', array());
defined('PROJECT_INFO') or define('PROJECT_INFO', array());
defined('TEST_PLAN_INFO') or define('TEST_PLAN_INFO', array());
defined('TEST_BUILD_INFO') or define('TEST_BUILD_INFO', array());
defined('TEST_SUITE_ALFA_INFO') or define('TEST_SUITE_ALFA_INFO', array());
defined('TESTLINK_URL') or define('TESTLINK_URL', "http://localhost:8080/testlink-1.9.20/lib/api/xmlrpc/v1/xmlrpc.php");
defined('TESTLINK_USER_API_KEY') or define('TESTLINK_USER_API_KEY', "874e34254509eb018661b58e2921c097");
defined('TESTLINK_API_KEY') or define('TESTLINK_API_KEY', "de713869d1db126cff30a1fc0e990962fbb277905c67ac8f363f7a22e50eb95b");
defined('TESTPLAN_ID') or define('TESTPLAN_ID', 1);
defined('PROJECT_ID') or define('PROJECT_ID', 3);

// Expresiones regulares
const REG_NUMERICO = "/^[0-9]+$/";
const REG_ALFABETICO = "/^[a-zA-ZáÁéÉíÍóÓúÚüÜñÑ.,\s_-]+$/";
const REG_ALFANUMERICO = "/^\s*[0-9a-zA-ZáÁéÉíÍóÓúÚüÜñÑ., _-]*\s*$/";
const REG_CEDULA = "/^[0-9]{7,8}$/";
const REG_TELEFONO = "/^[0-9]{11}$/";
const REG_CLAVE = "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/";
const REG_FECHA = "/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/";