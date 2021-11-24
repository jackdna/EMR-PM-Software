<?php
$secure_key = "cW8pQr9B4DAz";
$img_id=0;
$fileName='';
if($_POST['imgdata']){
	
	$sourceData= $secure_key.trim($_POST['patient_id']).trim($_POST['imgdata']);	
	$md5_imedic= md5($sourceData);
	
	if($md5_imedic===trim($_POST['md5_val'])){
		$strToTime=strtotime(date('Y-m-d H:i:s'));
		$imgDir='screenshots';
		$imgName= $_POST['patient_id']."_".$strToTime."_img.jpg";
		$fileName= $imgDir."/".$imgName;
		base64_to_image(trim($_POST['imgdata']), $fileName);

		if(file_exists($fileName)){
			
			//CROP UNWANTED AREA OF IMAGE
			$original_img=$fileName;
			list($original_w, $original_h) = getimagesize($original_img);
			$new_w = $original_w-230;
			$new_h = $original_h;
			$new_img = imagecreatetruecolor( $new_w, $new_h);
			//AFTER CUTTING RIGHT SIDE AGAIN SAVE THE IMAGE
			if(imagecopy( $new_img, imagecreatefromjpeg($original_img), 0, 0, 0, 0, $new_w, $new_h)){
				//SAVE IMAGE AFTER CROPPING
				imagejpeg($new_img, $original_img,100);
			}			
			//-----------------------------
			
			//SAVE IN DATABASE
			$qry="Insert INTO toric_pt_images SET
			patient_id='".$_POST['patient_id']."',
			dt_time='".date('Y-m-d H:i:s')."',
			img_path='".$fileName."'";
			$rs=imw_query($qry);
			$img_id= imw_insert_id();
			
			//CREATE THUMBNAIL
/*			$imgW = 80;
			$imgH = 100;
			$pathToImages=$fileName;
			if(!is_dir($imgDir."/thumb")){	mkdir($imgDir."/thumb",0777,true);	}
			$thumbPath = $imgDir."/thumb"."/".$imgName;
			
			$pathThumb = $oSaveFile->createThumbs($pathToImages,$thumbPath,$imgW,$imgH);	*/
			//----------------
		}else{
			//echo 'Image Capture Failed.';
		}
	
	
/*		if($_FILES['imgdata']['type']=='image/png' || $_FILES['imgdata']['type']=='image/jpeg'){
		
			$cp = copy ($_FILES['imgdata']['tmp_name'],'screenshots/'.$_POST['patient_id'].'_'.$_FILES['imgdata']['name']);
			if($cp){
				echo 'Image Uploaded to Server.';
			}else{
				echo 'Image Capture Failed.';
			}
		}else{
			echo 'Invalid Image Type: '.$_FILES['imgdata']['type'].'.';
		}*/
		
	}else{
		//echo 'Error: Data not matched.';
	}
}else{
	//echo 'Error! No data uploaded.';
}

//FUNCTIONS
function base64_to_image($base64_string, $output_file) {
	//$data = 'data:image/png;base64,'.trim($base64_string);
	$data = trim($base64_string);
	list($type, $data) = explode(';', $data);
	list(, $data)      = explode(',', $data);
	$data = base64_decode($data);
	file_put_contents($output_file, $data);
}

$img_id='25065';
echo json_encode(array('patient_id'=>$_POST['patient_id'], 'image_path'=>$fileName, 'img_id'=>$img_id));
//BELOW IS FUNCTION THAT ADDON IS CALLING THAT EXISTS IN OPENER WINDOW
//getCapturedImageInfo(patient_id, image_path, image_id)
?>