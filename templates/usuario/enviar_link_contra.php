<?php

include_once '../../includes/db_connect.php';


if ($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
        $userId = $_GET["id"];
        
    }
}

?>