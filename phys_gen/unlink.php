<?php
$filename = $_GET['file']; //get the filename
unlink($filename); //delete it
header('location: index.php'); //redirect back to the other page
?>