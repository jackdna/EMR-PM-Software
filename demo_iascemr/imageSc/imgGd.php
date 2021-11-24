<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
 /* Strat */
 /* Get Pixels and Image Name and create img On the fly */	
	function drawOnImage2($pixls,$imgName,$pathSave, $LnStrVal="1",$pdfFolderPath)
	{	
			
		//echo $imgName."<br>";//dirname(__FILE__);
		//If No Image set White Image
		//$imgPath = "imageSc/images/icon/";
		$imgPath = "html2pdf/";
		if($pdfFolderPath){$imgPath = $pdfFolderPath; }
		$imgDefault = $imgPath."bgGridNN.jpg";	
		$imgName = $imgPath.$imgName;
		if((!file_exists($imgName)) && (file_exists($imgDefault)))
		{
			$imgName = $imgDefault;
		}		
		//echo '<br>new: '.$imgName;
		//Make two array
		$arrstrings = array();		
		$arrpixels = array();			
		$arrElem = array();
		// Color Vars
		//$r=0;
		//$g=0;
		//$b=0;

		//get image dimension
		if(file_exists($imgName)){
			list($widthP, $heightP, $typeP, $attrP) = getimagesize($imgName);
		}
		
		// Shapes		
		$rectString = "TUp";
		$ovalString = "CFill";
		$trDownString = "TDn";
		$cDrawString = "CDr";
		$crossString = "Cr"; //
		$lineString = "Ln"; //
		$lineStringVerti = "Lv"; //

		//Path 
		$pathTUp = $imgPath.$rectString.".jpg";
		$pathCFill = $imgPath.$ovalString.".jpg";
		$pathTDn = $imgPath.$trDownString.".jpg";
		$pathCDr = $imgPath.$cDrawString.".jpg";
		$pathCr = $imgPath.$crossString.".jpg";
		
		//header("Content-type: image/jpeg");			
		//$imgcr = imagecreate(400,400);
		$imgcr = imagecreatefromjpeg($imgName);
		//$imgcr = imagecreatefromgif("../../../images/la.gif");
		//$img_clr_all_2 = imagecolorallocate($imgcr,0,0,0);
			
	 	$arrstrings = explode(";",$pixls);
		
		$strings_count = count($arrstrings);
		
		$strings_in = 0;
		
		$lc = 10;	
		
		while($strings_in < $strings_count) // String 0 Se Count  String tak Chale
		{			
			
			$eType = $xTmp = $yTmp = "";
			$arrElem = explode(":",$arrstrings[$strings_in]);
			
			$eType = $arrElem[0]; // Element Type			
			
			$arrpixels = explode(",",$arrElem[1]); // Pixels
			
			$pixels_count = count($arrpixels);
			
			$pixels_in_0 = 0;			
			
			$xTmp = $arrpixels[0];
			$yTmp = $arrpixels[1];			
			
			$strings_in++;
			if(($eType == "") || ($xTmp == "") || ($yTmp == "")){
				continue;
			}			
			
			$w=$h=14;
			$isString = false;			
			
			if($eType ==  $rectString){ //TUp
				$imgCp = imagecreatefromjpeg($pathTUp);				
			}else if($eType ==  $ovalString){ //CFill
				$imgCp = imagecreatefromjpeg($pathCFill);				
			}else if($eType ==  $trDownString){ //TDn
				$imgCp = imagecreatefromjpeg($pathTDn);				
			}else if($eType ==  $cDrawString){ //CDr
				$imgCp = imagecreatefromjpeg($pathCDr);				
			}else if($eType ==  $crossString){ //Cr
				$imgCp = imagecreatefromjpeg($pathCr);				
			}else if($eType ==  $lineString){ //Ln
				
				imagesetthickness($imgcr, $LnStrVal);
				$line_color = ImageColorAllocate ($imgcr, 255, 0, 0);
				imageline ($imgcr,$xTmp,$yTmp,$widthP,$yTmp,$line_color); 
				imagesetthickness($imgcr, 1);

				continue;
			}else if($eType ==  $lineStringVerti){ //LnVerti
				imagesetthickness($imgcr, $LnStrVal);
				$line_color = ImageColorAllocate ($imgcr, 255, 0, 0);
				imageline ($imgcr,$xTmp,$yTmp,$xTmp,$heightP,$line_color); 
				imagesetthickness($imgcr, 1);
				continue;
			}else{ //Text	
				$isString = true;
				$textcolor = imagecolorallocate($imgcr, 0, 0, 0);	
			}
			
			if($isString == true){
				$strText = (strlen($eType) > 11) ? substr($eType,0,9).".." : $eType ;
				imagestring ( $imgcr , 5 , $xTmp , $yTmp -14 , $strText , $textcolor ); // Write String				
			}else{				
				imagecopy($imgcr, $imgCp,$xTmp,$yTmp,0,0,$w,$h); // Copy Image	
			}			
			
		}	
		
		
		imagejpeg($imgcr,$pathSave,100);	
		//imagejpeg($imgcr);	
		//imagegif($imgcr,"123.gif");	
		imagedestroy($imgcr);
		//echo "Hi";
		
	} 
	 /* End */
	 
/* Get Pixels and Image Name and create img On the fly */	
	function drawOnImageHTML($pixls,$imgName="",$pathSave="",$LnStrVal="1", $pdfFolderPath="")
	{	
		$imgPath = "new_html2pdf/";
		if($pdfFolderPath){$imgPath = $pdfFolderPath; }
		$imgDefault = $imgPath."bgTest.jpg";	
		$imgName = $imgPath.$imgName;
		$imgName = (!file_exists($imgName) && file_exists($imgDefault) ) ? $imgDefault : $imgName;
				
		//Make two array
		$arrstrings = array();		
		$arrpixels = array();			
		$arrElem = array();
		
		//get image dimension
		if(file_exists($imgName)){
			list($widthP, $heightP, $typeP, $attrP) = getimagesize($imgName);
		}
		
		//Shapes		
		$rectString = "TUp";
		$ovalString = "CFill";
		$trDownString = "TDn";
		$cDrawString = "CDr";
		$crossString = "Cr"; //
		$lineString = "Ln"; //
		$lineStringVerti = "Lv"; //

		//Path 
		$pathTUp = $imgPath.$rectString.".jpg";
		$pathCFill = $imgPath.$ovalString.".jpg";
		$pathTDn = $imgPath.$trDownString.".jpg";
		$pathCDr = $imgPath.$cDrawString.".jpg";
		$pathCr = $imgPath.$crossString.".jpg";
		
		$imgcr = imagecreatefromjpeg($imgName);
		$arrstrings = explode(";",$pixls);
		
		$strings_count = count($arrstrings);
		$strings_in = 0;
		$lc = 10;	
		
		while($strings_in < $strings_count) // String 0 Se Count  String tak Chale
		{			
			
			$eType = $xTmp = $yTmp = "";
			
			$arrElem = explode(":",$arrstrings[$strings_in]);
			$eType = $arrElem[0]; // Element Type			
			$arrpixels = explode(",",$arrElem[1]); //Pixels
			$pixels_count = count($arrpixels);
			$pixels_in_0 = 0;			
			$xTmp = $arrpixels[0];
			$yTmp = $arrpixels[1];			
			
			$strings_in++;
			if(($eType == "") || ($xTmp == "") || ($yTmp == "")){
				continue;
			}			
			
			$w=$h=14;
			$isString = false;			
			
			if($eType ==  $rectString){ //TUp
				$imgCp = imagecreatefromjpeg($pathTUp);				
			}else if($eType ==  $ovalString){ //CFill
				$imgCp = imagecreatefromjpeg($pathCFill);				
			}else if($eType ==  $trDownString){ //TDn
				$imgCp = imagecreatefromjpeg($pathTDn);				
			}else if($eType ==  $cDrawString){ //CDr
				$imgCp = imagecreatefromjpeg($pathCDr);				
			}else if($eType ==  $crossString){ //Cr
				$imgCp = imagecreatefromjpeg($pathCr);				
			}else if($eType ==  $lineString){ //Ln
				imagesetthickness($imgcr, $LnStrVal);
				$line_color = ImageColorAllocate ($imgcr, 255, 0, 0);
				imageline ($imgcr,$xTmp,$yTmp,$widthP,$yTmp,$line_color); 
				imagesetthickness($imgcr, 1);
				continue;
			}else if($eType ==  $lineStringVerti){ //LnVerti
				imagesetthickness($imgcr, $LnStrVal);
				$line_color = ImageColorAllocate ($imgcr, 255, 0, 0);
				imageline ($imgcr,$xTmp,$yTmp,$xTmp,$heightP,$line_color); 
				imagesetthickness($imgcr, 3);
				continue;
			}else{ //Text	
				$isString = true;
				$textcolor = imagecolorallocate($imgcr, 0, 0, 0);	
			}
			
			if($isString == true){
				$strText = (strlen($eType) > 11) ? substr($eType,0,9).".." : $eType ;
				imagestring ( $imgcr , 5 , $xTmp , $yTmp -14 , $strText , $textcolor ); // Write String				
			}else{				
				imagecopy($imgcr, $imgCp,$xTmp,$yTmp,0,0,$w,$h); // Copy Image	
			}			
			
		}	
		
		
		imagejpeg($imgcr,$pathSave,100);	
		imagedestroy($imgcr);
	} 
	 /* End */		 
	 
	function filter_data($data = '')
	{
		if( !$data) return;
		
		$arr = explode("~",$data);
		$return = array();
		foreach($arr as $node)
		{
			$tmpString = '';
			$node = substr($node,-1,1) == ',' ? substr($node,0,-1) : $node;
			$node_arr = explode(',',$node);
			
			$tmp = explode(".",substr(strrchr($node_arr[0],'/'),1));
			$type = $tmp[0];
			$coord_x = $node_arr[1];
			$coord_y = $node_arr[2];
			$call = end($node_arr);
				
			if( $node_arr[0] == 'funDrawTextCan')
			{
				$text = end($node_arr);
				$tmpString = $text . ':'.$coord_x.','.$coord_y;
			}
			else
			{
				$type = ($call == 'funDrawReblock') ? 'Lv' : $type;
				$tmpString = $type . ':'.$coord_x.','.$coord_y;
			}
			
			array_push($return,$tmpString);
			
		}
			
		return implode(";",$return);
	}
	
	function create_html_data_image($pConfId, $html_grid_data = '',$grid_image_path = '', $startTime = '', $imgName="bgTest.jpg")
	{
		$img_name = ( $grid_image_path ) ? $grid_image_path : 'new_html2pdf/tess_'.$pConfId.'.jpeg'; 
		$tmp_image = 'new_html2pdf/tess_tmp_'.$pConfId.'.jpeg';
		
		$imgName = trim($imgName) ? trim($imgName) : 'bgTest.jpg';
		$ekgRedLineThikness = 3;
		$htmlGridData= filter_data($html_grid_data);
		drawOnImageHTML($htmlGridData,$imgName,$tmp_image,$ekgRedLineThikness);
		
		$bakImgResource 	= imagecreatefromjpeg('new_html2pdf/bgTest.jpg');	
		$canvasImgResource= imagecreatefromjpeg($tmp_image);
		imagecopy($bakImgResource, $canvasImgResource, 0, 0, 0, 0, 1001, 369);
		
		if(!empty($startTime)){
			$startTime = str_replace(array("A","P"), "", $startTime);
			$startTime_arr = explode(":",$startTime);
			$start_time_hr = trim($startTime_arr[0]);
			$start_time_min = trim($startTime_arr[1]);
			if($start_time_min<=15){$start_time_min="0";}
			if($start_time_min>=30&&$start_time_min<=45){$start_time_min="30";}
			if($start_time_min>=45){$start_time_min="45";}
			
			$pw=29-15;
			$ph=1;
			$pd=48;
			
			// White background and blue text			
			$tmp_textcolor = imagecolorallocate($bakImgResource, 0, 0, 0);
			
			// Write the string at the top left
			for($i=0,$w=0;$i<22;$i++){
				
				if($start_time_hr <"10" ){  $start_time_hr="0".(int)$start_time_hr; }
				if($start_time_min == "0"){  $start_time_min="00"; }
				$tmp_txt = $start_time_hr.":".$start_time_min;
				
				imagestring($bakImgResource, 1, $pw, $ph, $tmp_txt, $tmp_textcolor);
				
				$pw = (int)$pw+(int)$pd;
				
				//echo "\n".$tmp_txt;
				
				$start_time_min = (int)$start_time_min + 15;
				
				if($start_time_min == "60"){
					$start_time_hr = (int)$start_time_hr+1;
					$start_time_min ="0";
				}
				
				if($start_time_hr == "13"){$start_time_hr = "1";}
				
			}

		}
				
		imagejpeg($bakImgResource, $img_name); 
		imagedestroy($bakImgResource);
		imagedestroy($canvasImgResource);
				
		if( file_exists($tmp_image))
			unlink($tmp_image);
		
		return $img_name;									
	}
	
	
	
?>