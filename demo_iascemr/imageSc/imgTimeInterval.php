<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
 /* Strat */
 /* Get Pixels and Image Name and create img On the fly */	

/***************************DRAW IMAGE FOR APPLET DATA*********************************/

	function drawOnImagetime($pixls,$imgNameTime,$pathSave)
	{		
		$imgPath = "html2pdfnew/";
		$imgDefault = $imgPath."blank_timeInterval.jpg";	
		$imgName = $imgPath.$imgNameTime;
		if((!file_exists($imgName)) && (file_exists($imgDefault)))
		{
			$imgName = $imgDefault;
		}		
		$arrstrings = array();		
		$arrpixels = array();			
		$arrElem = array();

		$imgcr = imagecreatefromjpeg($imgName);

	 	$arrstrings = explode(",",$pixls);
		
		$strings_count = count($arrstrings);
		
		$strings_in = 0;
		
		$lc = 10;
		
		while($strings_in < $strings_count) // String 0 Se Count  String tak Chale
		{			
			$var_space="  ";
			$time_Interval = $time_Interval.$var_space.$arrstrings[$strings_in];
			$strings_in++;
			$string_img=$time_Interval;
			
		}	
		$textcolor = imagecolorallocate($imgcr, 0, 0, 0);	
		imagestring ( $imgcr , 3 , 0 , 0 , $string_img , $textcolor ); // Write String				
		imagejpeg($imgcr,$pathSave,100);	
		imagedestroy($imgcr);
		
	} 
	 /* End */
/***************************APPLET DATA IMAGE DRAWN*********************************/

?>