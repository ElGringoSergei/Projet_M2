<?php

    session_start();
    if(isset($_SESSION['sname']) && $_SESSION['expire'] > time()) {
		$_SESSION['expire'] = time() + $_SESSION['timeout'];
	} else {
		$_SESSION = [];
	}

    if(isset($_GET['submit-search'])) { 
        $search = $_GET['search'];
        
    }

    $_SESSION['message'] = $_GET['search'];
    #$search = '<script>alert("xss");</script>';

    header("Location: ../search_result.php");
?>
