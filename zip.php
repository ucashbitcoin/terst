<?php 

$list=file_get_contents("http://postalpincode.in/api/pincode/400101");
//$encode =json_encode($list);
//print_r($encode);
$decode=json_decode($list,ture);
print_r($decode['PostOffice'][0]['District']);
print_r($decode['PostOffice'][0]['State']);

?>
