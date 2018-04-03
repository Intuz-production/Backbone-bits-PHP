<?php
require("config/configuration.php");

if(!$gnrl->checkLogin()) {
	$gnrl->redirectTo("login.php");
}
else {
	$gnrl->redirectTo("dashboard");
}
?>