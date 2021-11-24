<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php 
ini_set('display_errors', '0');
/** 
 * PNG ALPHA CHANNEL SUPPORT for imagecopymerge(); 
 * This is a function like imagecopymerge but it handle alpha channel well!!! 
 **/ 

// A fix to get a function like imagecopymerge WITH ALPHA SUPPORT 
// Main script by aiden dot mail at freemail dot hu 
// Transformed to imagecopymerge_alpha() by rodrigo dot polo at gmail dot com 
require_once('../../config/globals.php');

function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){ 
    if(!isset($pct)){ 
        return false; 
    } 
    $pct /= 100; 
    // Get image width and height 
    $w = imagesx( $src_im ); 
    $h = imagesy( $src_im ); 
    // Turn alpha blending off 
    imagealphablending( $src_im, false ); 
    // Find the most opaque pixel in the image (the one with the smallest alpha value) 
    $minalpha = 127; 
    for( $x = 0; $x < $w; $x++ ) 
    for( $y = 0; $y < $h; $y++ ){ 
        $alpha = ( imagecolorat( $src_im, $x, $y ) >> 24 ) & 0xFF; 
        if( $alpha < $minalpha ){ 
            $minalpha = $alpha; 
        } 
    } 
    //loop through image pixels and modify alpha for each 
    for( $x = 0; $x < $w; $x++ ){ 
        for( $y = 0; $y < $h; $y++ ){ 
            //get current alpha value (represents the TANSPARENCY!) 
            $colorxy = imagecolorat( $src_im, $x, $y ); 
            $alpha = ( $colorxy >> 24 ) & 0xFF; 
            //calculate new alpha 
            if( $minalpha !== 127 ){ 
                $alpha = 127 + 127 * $pct * ( $alpha - 127 ) / ( 127 - $minalpha ); 
            } else { 
                $alpha += 127 * $pct; 
            } 
            //get the color index with new alpha 
            $alphacolorxy = imagecolorallocatealpha( $src_im, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha ); 
            //set pixel with the new color + opacity 
            if( !imagesetpixel( $src_im, $x, $y, $alphacolorxy ) ){ 
                return false; 
            } 
        } 
    } 
    // The image copy 
    imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
} 

// USAGE EXAMPLE: 


function callNum($value)
{
	if($value>0 && $value<10)
	{
		if($value==9)
		{
		$var1 = 216/$value;	
		return $value*round($var1,1);
		}
		else
		{
		$var1 = 216/$value;	
		return $value*round($var1,1);
		}
	}
	if($value>=10 && $value<12)
	{
		if($value==11)
		{
		$var1 = 200/$value;	
		return $value*round($var1,1);
		}
		else
		{
		$var1 = 208/$value;	
		return $value*round($var1,1);
		}
	}
	if($value>=12 && $value<14)
	{
		if($value==13)
		{
		$var1 = 184/$value;	
		return $value*round($var1,1);
		}
		else
		{
		$var1 = 192/$value;	
		return $value*round($var1,1);
		}
	}
	if($value>=14 && $value<16)
	{
		if($value==15)
		{
		$var1 = 168/$value;	
		return $value*round($var1,1);
		}
		else
		{
		$var1 = 176/$value;	
		return $value*round($var1,1);
		}
	}
	if($value>=16 && $value<18)
	{
		if($value==17)
		{
		$var1 = 152/$value;	
		return $value*round($var1,1);
		}
		else
		{
		$var1 = 160/$value;	
		return $value*round($var1,1);
		}
	}
	if($value>=18 && $value<20)
	{
		if($value==19)
		{
		$var1 = 136/$value;	
		return $value*round($var1,1);
		}
		else
		{
		$var1 = 144/$value;	
		return $value*round($var1,1);
		}
	}
	if($value>=20 && $value<22)
	{
		if($value==21)
		{
		$var1 = 120/$value;	
		return $value*round($var1,1);
		}
		else
		{
		$var1 = 128/$value;	
		return $value*round($var1,1);
		}
	}
	if($value>=22 && $value<24)
	{
		if($value==23)
		{
		$var1 = 104/$value;	
		return $value*round($var1,1);
		}
		else
		{
		$var1 = 112/$value;	
		return $value*round($var1,1);
		}
	}
	if($value>=24 && $value<26)
	{
		if($value==25)
		{
		$var1 = 88/$value;	
		return $value*round($var1,1);
		}
		else
		{
		$var1 = 96/$value;	
		return $value*round($var1,1);
		}
	}
	if($value>=26 && $value<28)
	{
		if($value==27)
		{
		$var1 = 72/$value;	
		return $value*round($var1,1);
		}
		else
		{
		$var1 = 80/$value;	
		return $value*round($var1,1);
		}
	}
	if($value>=28 && $value<30)
	{
		if($value==29)
		{
		$var1 = 54/$value;	
		return $value*round($var1,1);
		}
		else
		{
		$var1 = 64/$value;	
		return $value*round($var1,1);
		}
	}
	if($value>=30 && $value<32)
	{
		if($value==31)
		{
		$var1 = 40/$value;	
		return $value*round($var1,1);
		}
		else
		{
		$var1 = 48/$value;	
		return $value*round($var1,1);
		}
	}
	if($value>=32 && $value<34)
	{
		if($value==33)
		{
		$var1 = 24/$value;	
		return $value*round($var1,1);
		}
		else
		{
		$var1 = 32/$value;	
		return $value*round($var1,1);
		}
	}
	if($value>=34 && $value<36)
	{
		if($value==35)
		{
		$var1 = 8/$value;	
		return $value*round($var1,1);
		}
		else
		{
		$var1 = 16/$value;	
		return $value*round($var1,1);
		}
	}
}

function createGraph($od,$os)
{

$myArr = array();
if($od>0){
	$myArr[0] = $GLOBALS['fileroot']."/library/images/sign_o.png";
}
if($os>0){
	$myArr[1] = $GLOBALS['fileroot']."/library/images/sign_x.png";
}

for($i=0;$i<=count($myArr);$i++)
{
	if($i==0)
	{
		$img_a = imagecreatefrompng($GLOBALS['fileroot'].'/library/images/graph_base.png');
	}

	$img_b = imagecreatefrompng($myArr[$i]);

	$white = imagecolorallocate($img_a, 255, 255, 255);
	$red = imagecolorallocate($img_a, 255, 0, 0);
	$black = imagecolorallocate($img_a, 0, 0, 0);
	$grey = imagecolorallocate($img_a, 211, 211, 211);

// SAME COMMANDS: 
	if($i==0)
	{
		imagecopymerge_alpha($img_a, $img_b, 18, callNum($od), 0, 0, imagesx($img_b), imagesy($img_b),100);
		if($od!=""){
			//if($od>34){
				$im = imagecreatetruecolor(350, 30);
				// Create some colors
				$white = imagecolorallocate($img_a, 0, 0, 0);
				$grey = imagecolorallocate($img_a, 128, 128, 128);
				$black = imagecolorallocate($img_a, 255, 255, 255);
				imagefilledrectangle($img_a, 0, 0, 399, 0, $black);
				// The text to draw
				$text = '('.$od.')';
				// Replace path by your own font path
				$font = $GLOBALS['fileroot'].'/library/fonts/souveni1.ttf';
				
				// Add some shadow to the text
				imagettftext($im, 10, 0, 11, 21, $grey, $font, $text);
				
				// Add the text
				imagettftext($img_a, 10, 0, 38, callNum($od)+11, $white, $font, $text);
			//}
		}
	}	
	if($i==1)
	{
		imagecopymerge_alpha($img_a, $img_b, 197, callNum($os), 0, 0, imagesx($img_b), imagesy($img_b),100);
		if($os!=""){
			//if($os>34){
				$im = imagecreatetruecolor(350, 30);
				// Create some colors
				$white = imagecolorallocate($img_a, 0, 0, 0);
				$grey = imagecolorallocate($img_a, 128, 128, 128);
				$black = imagecolorallocate($img_a, 255, 255, 255);
				imagefilledrectangle($img_a, 0, 0, 399, 0, $black);
				// The text to draw
				$text = '('.$os.')';
				// Replace path by your own font path
				$font = $GLOBALS['fileroot'].'/library/fonts/souveni1.ttf';
				
				// Add some shadow to the text
				imagettftext($im, 10, 0, 11, 21, $grey, $font, $text);
				
				// Add the text
				imagettftext($img_a, 10, 0, 215, callNum($os)+11, $white, $font, $text);
			//}
		}
		
	}
}
// OUTPUT IMAGE: 
header("Content-Type: image/png"); 
imagesavealpha($img_a, true); 
imagepng($img_a, NULL);

}

if($_REQUEST)
{
	createGraph($_REQUEST['od'],$_REQUEST['os']);	
}


?>
