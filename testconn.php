<?php
$link = mysqli_connect('localhost', 'root', '', 'retail_store');
// If connection fails, you'll see an error message
if (!$link) {
die('Could not connect: ' . mysql_error());
}
// If connection is successful, you'll see this message
echo 'Connected successfully';
mysqli_close($link);
?>