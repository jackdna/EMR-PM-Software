<?php
//refine by $_POST['query'] parameter received
$array[0]['id']= 1; 
$array[0]['name']='Toronto';
$array[1]['id']= 2; 
$array[1]['name']='Montreal';
$array[2]['id']= 2; 
$array[2]['name']='New York';
$array[3]['id']= 4; 
$array[3]['name']='Buffalo';
$array[4]['id']= 5; 
$array[4]['name']='Boston';
$array[5]['id']= 6; 
$array[5]['name']='Columbus';
$array[6]['id']= 7; 
$array[6]['name']='Dallas';
$array[7]['id']= 8; 
$array[7]['name']='Vancouver';
$array[8]['id']= 9; 
$array[8]['name']='Seattle';
$array[9]['id']= 10; 
$array[9]['name']='Los Angeles';
$array[10]['id']= 11; 
$array[10]['name']=$_POST['query'];

echo json_encode($array);
?>