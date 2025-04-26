<?php 
	spl_autoload_register(function($class){
			$lcClass = ucfirst($class);
		if(file_exists("Models/".$lcClass.".php")){
			require_once("Models/".$lcClass.".php");
		}
	});
 ?>