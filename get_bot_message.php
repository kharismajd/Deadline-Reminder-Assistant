<?php
include('database.inc.php');
$txt = mysqli_real_escape_string($con,$_POST['txt']);
$sql = "select reply from questions where question like '%$txt%'";
$res = mysqli_query($con,$sql);
if (mysqli_num_rows($res) > 0) {
	$row = mysqli_fetch_assoc($res);
	echo $row['reply'];
} 
else {
	echo "Maaf, saya tidak mengerti maksud Anda.";
}
?>