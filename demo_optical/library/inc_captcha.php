<? $ignoreAuth = true; 
include(dirname(__FILE__)."/../config/config.php"); 
//ob_get_contents();
//ob_end_clean();
//die('this is testing');
//@session_start();
create_image();
function create_image()
{
	$cod="";
	for($i=0;$i<=4;$i++)
	{
		$c=rand(97,120); $cod.=chr($c);
	}
	//session_register('vericode');
	$_SESSION['vericode']=$cod;
	
	$im = imagecreate (85, 40);
	$bgcolor = imagecolorallocate ($im, 0xef, 0xef, 0xef);
	$font = imagecolorallocate ($im, 0x33, 0x00, 0x00);
	imagettftext ($im, 18, 10, 10, 35, $font, "arial.ttf", $_SESSION['vericode']);	
	//Tell the browser what kind of file is come in
    header("Content-Type: image/jpeg");

    //Output the newly created image in jpeg format
    imagejpeg($im);
//echo 'Variable $im = '.$im;
    //Free up resources
    imagedestroy($im);
}
?>