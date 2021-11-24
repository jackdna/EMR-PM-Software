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
/*
File: pdf_thumb.php
Purpose: This file creates a thumb nail from pdf file.
Access Type : Direct
*/
?>
<?php
include_once(dirname(__FILE__)."/../../../config/globals.php");
function thumbPdf($pdf, $width){    
	global $phpServerIP;
	$rootDir = substr(data_path(), 0, -1);
	$srcDir = substr(data_path(1), 0, -1);
	
	try    {        
		//$tmp = 'tmp';        
		$tmp = $rootDir."/tmp";
		if(!is_dir($tmp)){
			mkdir($tmp, 0777,true);
		}		
		$format = "png";
		
		$source = $pdf.'[0]';        
		$dest = "$tmp/".time()."".session_id()."pdfthumb.$format";
		$out="";
		if (!file_exists($dest)) { 
			//if(!copy($GLOBALS['fileroot']."/images/pdf_icon[1].png",$dest)){echo "Copy Failed.";} //Demo
			$exe_path = $GLOBALS['IMAGE_MAGIC_PATH'];
			if(!empty($exe_path)){$exe_path .= "/";}
			
			//COMMAND CHANGED A BIT, AS IT WAS NOT SHOWING THUMB FOR PDF.
			//$exec = $exe_path."convert -density 300 -trim '$source' -strip -quality 95 -interlace line -scale $width -unsharp 1.5�1.0+1.5+0.02 $dest 2>&1 ";
			//$exec = $exe_path.'convert -density 300 -trim "'.$source.'" -strip -quality 95 -interlace line -colorspace RGB -resize '.$width.' -unsharp 1.5�1.0+1.5+0.02 "'.$dest.'" 2>&1';
			//$exec = $exe_path.'convert -density 300 -background white -flatten -trim "'.$source.'" -strip -quality 95 -interlace line -colorspace RGB -resize '.$width.' -unsharp 1.5�1.0+1.5+0.02 "'.$dest.'" 2>&1';
			$exec = $exe_path.'convert -flatten "'.$source.'" -quality 95 -thumbnail "'.$width.'" -colorspace RGB -unsharp 1.50.0+1.5+0.020  "'.$dest.'"';
			if($phpServerIP == "74.94.52.21"||$phpServerIP == "70.89.36.81"){//stop on athwal and brian			
			}else{
				if(constant("STOP_CONVERT_COMMAND")=="YES") {
					//STOP CONVERT COMMAND
				}elseif(file_exists($pdf)) { 
					$out = shell_exec($exec);
				}
			}
			if(!empty($out)){
				//$exec = "convert_im -density 300 -background white -flatten -trim '$source' -strip -quality 95 -interlace line -scale $width -unsharp 1.5�1.0+1.5+0.02  $dest 2>&1 ";	
				$exec = 'convert_im -flatten "'.$source.'" -quality 95 -thumbnail "'.$width.'" -colorspace RGB -unsharp 1.50.0+1.5+0.020  "'.$dest.'"';
				if(constant("STOP_CONVERT_COMMAND")=="YES") {
					//STOP CONVERT COMMAND
				}elseif(file_exists($pdf)) {
					$out = shell_exec($exec);
				}
				//if(!empty($out)){echo "<pre>".$out."</pre>";exit();}
			} // only for debugging			
			
		}else{ 
			echo "FILE NOT FOUND"; exit();
		}		
		
		//$im = new Imagick($dest);       
		$im = imagecreatefrompng($dest);
		//$im2 = imagecreate($width, $width);
		//$white = imagecolorallocate( $im2, 255, 255, 255 );
		// copy image into new resource
		if($im){
			list($width, $height, $type, $attr) = getimagesize($dest);
			$type = image_type_to_mime_type($type); 
			//imagecopy( $im2, $im, 0, 0, 0, 0, $width, $height );		
		}else{
			$im = imagecreate($width, $width);
			$tc  = imagecolorallocate($im, 0, 0, 0);
			$white = imagecolorallocate( $im, 255, 255, 255 );
			imagefilledrectangle($im, 0, 0, $width, $width, $white);
			//imagestring($im, 5, 5, 5, "PDF ICON", $tc);
			//fill the background with white (not sure why it has to be in this order)
			imagefill( $im, 0, 0, $white );
			
			$src = imagecreatefrompng($GLOBALS["srcdir"].'/images/test_pdf_Icon.png');
			// Copy
			imagecopy($im, $src, 0, 0, 20, 13, 85, 110);
			
			$type="image/png";			
		}		
		header("Content-Type:".$type);
		imagepng($im);
		imagedestroy($im);
		unlink($dest);
	}    
	catch(Exception $e)   
	{       
		echo $e->getMessage();    
	}	
} 


$file = $_GET['pdf'];
$size = $_GET['size'];
if ($file && $size){    thumbPdf($file, $size);}

?>