<?php
	session_start();
	session_destroy();
	echo json_encode($_SESSION);
	//header('location:../../../../index.php'); 
	}
?>