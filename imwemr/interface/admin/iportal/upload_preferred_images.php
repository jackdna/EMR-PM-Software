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
require_once("../../../config/globals.php");

$u_img = $_FILES["u_img"];
$dir_path = $GLOBALS['fileroot'].'/data/'.PRACTICE_PATH.'/preferred_images/';
if(!is_dir($dir_path)){
	mkdir($dir_path);
}
if($u_img["error"] == 0)
{
	
	$msg = "";
	if(file_exists($dir_path.$u_img["name"]))
	{
		$u_img["name"] = $u_img["name"];
        $remove_ext_pos=strripos($u_img["name"],".");
        $extension=substr($u_img["name"],$remove_ext_pos);
        $independent_name=substr($u_img["name"],0,$remove_ext_pos);         
        $name_set_pos=strripos($independent_name,"_");
        
        $rename_no=1;
        $app_file_name = $independent_name."_".$rename_no.$extension;
        while(file_exists($dir_path.$app_file_name))
        {
            $rename_no++;
            $app_file_name = $independent_name."_".$rename_no.$extension;
        }
        $u_img["name"]=$app_file_name;			
	}

	$allowed_types = array("image/jpg", "image/jpeg", "image/png", "image/gif");

	if(in_array($u_img["type"], $allowed_types))
	{
		$imageDirPath = $dir_path.$u_img["name"];
		if(strtolower(mime_content_type($imageDirPath))!='image/jpeg' || strtolower(mime_content_type($imageDirPath))!='image/png' || strtolower(mime_content_type($imageDirPath))!='image/gif')
		{	
			if(move_uploaded_file($u_img["tmp_name"], $dir_path.$u_img["name"]))
			{
				$img_name = $u_img["name"];
				$target_file_name  = $dir_path.$u_img["name"];
				list($width_orig, $height_orig) = getimagesize($target_file_name);
				if($width_orig > 128)
				{
					$ratio_orig = $width_orig/$height_orig;
					$width_set = 128;
					$height_set = $width_set/$ratio_orig;
					$image_p = imagecreatetruecolor($width_set, $height_set);

					if($u_img["type"] == 'image/gif')
					{
							$image_orig = imagecreatefromgif($target_file_name);
					}
					elseif($u_img["type"] == 'image/jpeg')
					{
							$image_orig = imagecreatefromjpeg($target_file_name);	
					}
					elseif($u_img["type"] == 'image/jpg')
					{
							$image_orig = imagecreatefromjpeg($target_file_name);
					}
					elseif($u_img["type"] == 'image/png')
					{
							$image_orig = imagecreatefrompng($target_file_name);
					}
					else
					{
							$image_orig = null;	
					}
					if($image_orig!=null)
					{
						imagecopyresampled($image_p, $image_orig, 0, 0, 0, 0, $width_set, $height_set, $width_orig, $height_orig);
						imagejpeg($image_p,$dir_path.$u_img["name"], 100);
					}	
				}	
					
				$req_qry = "INSERT INTO iportal_preferred_images SET name = '$img_name'";
				imw_query($req_qry);
				$msg = "File uploaded successfully"; 
			}		
		}
		else
		{
			$msg = "Upload format not supported";
		}	
	}
	else {
		$msg = "Upload format not supported";
	}	
}
header('location:preferred_images.php?msg='.$msg);
?>