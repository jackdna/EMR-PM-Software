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
File: imgGdFun.php
Purpose: This file provides drawing image in work view.
Access Type : Include file
*/
?>
<?php

 /* Strat */
 /* Get Pixels and Image Name and create img On the fly */	
	function drawOnImage_new($pixls,$imgName,$saveImg="0",$img_nam_prov="",$pkid = "")
	{
		//If No Image set White Image
		$imgDefault = dirname(__FILE__)."/../../../library/images/white.png";
		if((!file_exists($imgName)) && (file_exists($imgDefault)))
		{
			$imgName = $imgDefault;
		}		
		
		//Make two array
		$arrstrings = array();		
		$arrpixels = array();			
		
		// Color Vars
		$r=0;
		$g=0;
		$b=0;
		if(stripos($imgName, ".gif")!==false){ $imgcr = imagecreatefromgif($imgName); }
		else{	$imgcr = imagecreatefromjpeg($imgName); }
			
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
						
						//
						
						//
					
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
					
					
					//gr.setColor(strcolor);	// Color Set Kita
					//gr.drawLine(val_1,val_2,val_3,val_4);	// Line Draw Ki			
					
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
		
		global $gdFilename;
		global $$img_nam_prov;
		//echo $$imageCreatedName;
		$gdFilename="";	
		if($saveImg == "1"){			
			//$dir = "images/tmp/";
			//$dir = "html2pdfprint/";
            $dir = dirname(__FILE__)."/../../../data/".PRACTICE_PATH."/tmp/";
			if(file_exists($dir)){				
				$gdFilename2=sprintf($dir."%d.jpg", time());
				$gdFilename=str_replace("html2pdfprint/","",$gdFilename2);
			}
		}else if($saveImg == "2"){
			$dir = dirname(__FILE__)."/../../../data/".PRACTICE_PATH."/tmp/"; 
			if(file_exists($dir)){				
				$gdFilename=sprintf("%d.jpg", time());
				$gdFilename2=sprintf($dir.$gdFilename);
				//$gdFilename = $gdFilename2;
			}else{
				//echo "NO FILE";
			}
		}
		else if($saveImg == "3"){
			$dir = dirname(__FILE__)."/../../../data/".PRACTICE_PATH."/tmp/";
			//$dirOthr = dirname(__FILE__)."/../reports/new_html2pdf/tmp/"; //CODE COMMENTED BECAUSE LOCATIONS NOT EXISTS IN R8
			$dirOthr = dirname(__FILE__)."/../../../data/".PRACTICE_PATH."/tmp/";
			if(file_exists($dir)){				
				$gdFilename=sprintf("%d.jpg", time());
				$gdFilename2=sprintf($dir.$gdFilename);
				//$gdFilename = $gdFilename2;
				/*if(file_exists($dir.$gdFilename)) {
					copy($dir.$gdFilename,$dirOthr.$gdFilename)	;
				}*/
			}else{
				//echo "NO FILE";
			}
		}else if($saveImg == "4"){
			$dir = dirname(__FILE__)."/../audit/sig_images/";
			if(file_exists($dir)){				
				$tim=time()+$pkid;
				$$img_nam_prov=sprintf("%d.jpg", $tim);
				$gdFilename2=sprintf($dir.$$img_nam_prov);
				//$gdFilename = $gdFilename2;
			}else{
				//echo "NO FILE";
			}
		}else if($saveImg == "5"){
			$dir = dirname(__FILE__)."/../audit/sig_images/";
			if(file_exists($dir)){	
				$tim1=time()+2+$pkid;
				$$img_nam_prov=sprintf("%d.jpg", $tim1);
				$gdFilename2=sprintf($dir.$$img_nam_prov);
				//$gdFilename = $gdFilename2;
			}else{
				//echo "NO FILE";
			}
		}else if(!empty($saveImg)){
			//$dir = dirname(__FILE__)."/../common/new_html2pdf/tmp/";
			//if(file_exists($dir)){				
				$gdFilename="".$saveImg;
				$gdFilename2="".$gdFilename;
				//$gdFilename = $gdFilename2;
			//}else{
				//echo "NO FILE";
			//}
		}
		
		imagejpeg($imgcr,$gdFilename2,100);
		if($saveImg=="3"){
			if(file_exists($dir.$gdFilename)) {
				copy($dir.$gdFilename,$dirOthr.$gdFilename);
			}
		}
		//imagejpeg($imgcr);	
		//imagegif($imgcr,"123.gif");	
		imagedestroy($imgcr);
		//echo "Hi";
		
	} 
	 /* End */

?>