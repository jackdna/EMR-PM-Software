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

?><?php
require_once('../../../../config/globals.php');
include_once('../../../../library/classes/common_function.php');
$targetWidth = $_REQUEST["tWidth"];
$targetHeight = $_REQUEST["tHeight"];
$picName = $_REQUEST["pName"];
$providerId = $_REQUEST['pId'];
$upload_dir = substr(data_path() . "UserId_".$providerId."/profile_img/",0,-1);   //       D:/imedic/apache/htdocs/iMedicWareR8-Dev/data/imedicwarer8-dev
if(file_exists($upload_dir.'/'.$picName)){
		 $img_size = getimagesize($upload_dir.'/'.$picName);
		 $width = $img_size[0];
		 $height = $img_size[1];
		 
		 do{
			 if($width > $targetWidth){
				$width=$targetWidth;
				$percent=$img_size[0]/$width;
				$height=$img_size[1]/$percent; 
			 }
			 if($height > $targetHeight){
				$height=$targetHeight;
				$percent=$img_size[1]/$height;
				$width=$img_size[0]/$percent; 
			 }
			 
		 }while($width > $targetWidth || $height > $targetHeight);
		 echo $width."~".$height;
}
else
{
    echo "";
}
?>