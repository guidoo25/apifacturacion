<?php
include ('dbconection.php');
$con = dbconection();
$query="SELECT * FROM `emisor` ";
$exe=mysqli_query($con,$query);


$arr = [];
while ($row=mysqli_fetch_array($exe)) {
    $arr[] = $row;
}
print(json_encode($arr));

?>

