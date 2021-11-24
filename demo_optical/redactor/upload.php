<?php
include_once("../config/config.php");
 
// This is a simplified example, which doesn't cover security of uploaded images.
// This example just demonstrate the logic behind the process.
 
// files storage folder
$dir = 'images/';
 
$_FILES['file']['type'] = strtolower($_FILES['file']['type']);
 
if ($_FILES['file']['type'] == 'image/png'
|| $_FILES['file']['type'] == 'image/jpg'
|| $_FILES['file']['type'] == 'image/jpeg')
{
    $filename = md5(date('YmdHis')).'.jpeg';
    $file = $dir.$filename;

    //Quick explaination of what is going on here. Our PDF2HMTL convert seemed to only accept .jpeg files (dont ask me didnt code it)
    //So if i recieve a png file I need to actually convert the file from png to jpep. (naming convention is not enough)
    // So I am searching to see if the file is png type, then converting it. finally moving it. 
    //If its a regular jpg then i just move it. 
    if($_FILES['file']['type'] == 'image/png')
        {
            $image = imagecreatefrompng($_FILES['file']['tmp_name']);
            imagejpeg($image, $file, 100);
            imagedestroy($image);

            //move_uploaded_file($filename, $file);

        }
    else
        {   
            // setting file's mysterious name
    
            
         
            // copying
            move_uploaded_file($_FILES['file']['tmp_name'], $file);

        }


    
 
    // displaying file
    $array = array(
        'filelink' => "https://". $_SERVER['SERVER_NAME'] . $GLOBALS['webroot'] . '/redactor/images/'.$filename
    );
 
    echo stripslashes(json_encode($array));
 
}


?>