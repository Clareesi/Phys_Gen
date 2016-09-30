<?php
$filename = 'mcquestion_file.txt'; // of course find the exact filename....        
header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false); // required for certain browsers 
header('Content-Type: text/plain');

header('Content-Disposition: attachment; filename="'. basename($filename) . '";');
header('Content-Transfer-Encoding: binary\n');
header('Content-Length: ' . filesize($filename));

readfile($filename);

exit;
?>