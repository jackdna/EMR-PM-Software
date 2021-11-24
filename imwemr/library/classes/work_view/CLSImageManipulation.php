<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: CLSImageManipulation.php
Coded in PHP7
Purpose: This class provides functions to alter/modify drawing images.
Access Type : Include file
*/
?>
<?php
set_time_limit(60);
class CLSImageManipulation{
	public $image;
	public $imageType;
	function load($filename){
		$arrImageInfo = getimagesize($filename);	 
		$this->imageType = $arrImageInfo[2];
		$this->image = imagecreatefromstring(file_get_contents($filename));
		/*if($this->imageType == IMAGETYPE_JPEG ){
		$this->image = imagecreatefromstring(file_get_contents($filename));		
		}
		elseif( $this->imageType == IMAGETYPE_GIF ) {
		$this->image = imagecreatefromgif($filename);
		}
		elseif( $this->imageType == IMAGETYPE_PNG ) {
		$this->image = imagecreatefrompng($filename);
		}*/
	}
	function save($filename, $imageType=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
		if( $permissions != null) {
			chmod($filename,$permissions);
		}
		if( $imageType == IMAGETYPE_JPEG ) {
			imagejpeg($this->image,$filename,$compression);
		}
		elseif( $imageType == IMAGETYPE_GIF ) {
			imagegif($this->image,$filename);         
		}
		elseif( $imageType == IMAGETYPE_PNG ) {
			imagepng($this->image,$filename);
		}
	}
	function output($imageType=IMAGETYPE_JPEG) {
		if( $imageType == IMAGETYPE_JPEG ) {
			imagejpeg($this->image);
		}
		elseif( $imageType == IMAGETYPE_GIF ) {
			imagegif($this->image);         
		}
		elseif( $imageType == IMAGETYPE_PNG ) {
			imagepng($this->image);
		}   
	}
	function getWidth() {
		return imagesx($this->image);
	}
	function getHeight() {
		return imagesy($this->image);
	}
	function resizeToHeight($height) {
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width,$height);
	}
	function resizeToWidth($width) {
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		$this->resize($width,$height);
	}
	function scale($scale) {
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100; 
		$this->resize($width,$height);
	}
	function resize($width,$height,$blPreserveTransparency = false){
		$new_image = imagecreatetruecolor($width, $height);
		
		if($blPreserveTransparency == true){
			imagealphablending($new_image, false);
			imagesavealpha($new_image,true);
			$transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagefilledrectangle($new_image, 0, 0, $width, $height, $transparent);
		}
		
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->image = $new_image;   
	}      
}
?>