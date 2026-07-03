<?php  
    $host="localhost";
    $dbuser="root";
    $dbpass="";
    $dbname="lectsure";
    
    $conn = mysqli_connect($host, $dbuser, $dbpass, $dbname);

    if (mysqli_connect_errno()){
        echo "Connection failed: " . mysqli_connect_error();
        exit();
    }

?>