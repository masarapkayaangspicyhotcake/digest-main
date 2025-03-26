<?php

include 'database.class.php';

session_start();
session_unset();
session_destroy();

header('location:../home.php');

?>