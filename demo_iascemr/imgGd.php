<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");

	function drawOnImage($pixls,$imgName,$img_name_tmp)
	{		
		$imgDefault = "html2pdf/black.jpg";	
		if((!file_exists($imgName)) && (file_exists($imgDefault)))
		{
		$imgName = $imgDefault;
		}		
		$arrstrings = array();		
		$arrpixels = array();		
		// Color Vars
		$r=0;
		$g=0;
		$b=0;
	
		$imgcr = imagecreatefromjpeg($imgName);
		
	 	$arrstrings = explode(";",$pixls);
		
		$strings_count = count($arrstrings);
		
		$strings_in = 0;
		
		$lc = 10;
		
		while($strings_in < $strings_count) // String 0 Se Count  String tak Chale
		{			
			$arrpixels = explode(" ",$arrstrings[$strings_in]);
						
			$pixels_count = count($arrpixels);
						
			$pixels_in_0 = 0;
			
			while($pixels_in_0 < $pixels_count)
			{				
				//
				$pixels_in_1 = $pixels_in_0 + 1; // Agle teen set Kiye
				 
				$pixels_in_2 = $pixels_in_0 + 2;		
		
				$pixels_in_3 = $pixels_in_0 + 3;
				
				//
				if(($arrpixels[$pixels_in_0]) && ($arrpixels[$pixels_in_1]) && ($arrpixels[$pixels_in_2]) && ($arrpixels[$pixels_in_3]))
				{			
				//	
					$str_val_1 = $arrpixels[$pixels_in_0]; //Sare Pixels Ki values Li
					
					$str_val_2 = $arrpixels[$pixels_in_1];
					
					$str_val_3 = $arrpixels[$pixels_in_2];
					
					$str_val_4 = $arrpixels[$pixels_in_3];
										
					$tmp_int = strpos($str_val_1,":");
										
					if($tmp_int === false)
					{				
						//Do Nothing
					}
					else
					{
						$clrstr = "";												
												
						$clrstr = substr($str_val_1,0,$tmp_int);
																		
						$str_val_1 = substr($str_val_1,$tmp_int + 1);
												
						$arrclr = array();
												
						$arrclr =  explode("-",$clrstr); //Us Ko Split Kiya '-' se 
																	
						$r = $arrclr[0];
												
						$g = $arrclr[1];
												
						$b = $arrclr[2];	
						
					}			
					
					$img_clr_all_2 = imagecolorallocate($imgcr,$r,$g,$b);
										
					$tmp_int = strpos($str_val_3,":");
					
					if($tmp_int === false) // Check Kiya Ki Coolon hai
					{
					}
					else
					{						
						$str_val_3 = substr($str_val_3,$tmp_int + 1); 											
					}
										
					$val_1 = $str_val_1;
									
					$val_2 = $str_val_2;
										
					$val_3 = $str_val_3;
									
					$val_4 = $str_val_4;
					
						
					
					imageline($imgcr,$val_1,$val_2,$val_3,$val_4,$img_clr_all_2);
					$pixels_in_0 += 2;
					
				}
				else
				{				
					$pixels_in_0 += 2;
					continue;					
				}	
			
			}
			
			$strings_in += 1;
		}	
		
		imagejpeg($imgcr,"html2pdf/".$img_name_tmp,100);
		
	} 
	
	$pixels = "";	
	
	//Get Values
	if(!empty($_GET["id"]))
	{
		$id = $_GET["id"];
		$tbl = $_GET["tbl"];
		$pixelField = $_GET["pixelField"];
		$idField = $_GET["idField"];
		$imgName = $_GET["imgName"];
		$tmp_imgname=$_GET["tmp_imgname"];
		
		
		print $qry = "SELECT $pixelField FROM $tbl WHERE $idField = $id";		
		$res = imw_query($qry);	
		$row=imw_fetch_array($res);
		$pixels = $row[$pixelField];				
		drawOnImage($pixels,$imgName,$tmp_imgname); 								
	}
	//Get Image	
	


?>