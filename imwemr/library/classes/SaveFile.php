<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: SaveFile.php
Coded in PHP7
Purpose: This Class provides general functions for File saving and retrieval in chart notes.
Access Type : Include file
*/
?>
<?php
// SaveFile.php --

class SaveFile
{
	public $pId;  // patient Id or User Id
	public $upDir; // Upload Directory
	public $pDir;
	public $flgUsr;
	public $upDirWeb;
	public $prnt_fldr;

	//Set Upload paths
	public function __construct($pid=0, $flgUser=0, $prnt_fldr=""){ //0 directory for  temporary
		if(empty($pid)){ $pid="0"; }
		$this->pId = $pid;
		$this->flgUsr = $flgUser;
		$this->upDir = (isset($GLOBALS['file_upload_dir']) && !empty($GLOBALS['file_upload_dir'])) ? $GLOBALS['file_upload_dir'] : $GLOBALS['fileroot']."/data/".PRACTICE_PATH ; //set in chart_globals.php
		$this->upDirWeb = str_replace($GLOBALS['fileroot'], $GLOBALS['webroot'], $this->upDir);

		$this->pDir = ($this->flgUsr==1) ? "/UserId_".$this->pId : "/PatientId_".$this->pId;

		if(!empty($prnt_fldr)&&!empty($this->pId)){
			$this->prnt_fldr = $prnt_fldr;
			$this->pDir = "/".$prnt_fldr.$this->pDir;
		}

		if(empty($this->pId)){ $this->pDir = ""; }

		//Create Dir
		$this->ptDir();

	}

	function getFileDir($file){
		return dirname($file);
	}

	//Check if patient dir exists else create one
	public function ptDir($subDir="",$flgRetType=""){
		$ptdir = $this->upDir.$this->pDir;

		//Practice Directory
		if(!is_dir($this->upDir)){
			mkdir($this->upDir,0777,true);
			chmod($this->upDir, 0777);
		}

		//tmp directory in practice
		if(!is_dir($this->upDir."/tmp")){
			mkdir($this->upDir."/tmp",0777,true);
			chmod($this->upDir."/tmp", 0777);
		}

		if(!is_dir($ptdir)){
			mkdir($ptdir,0777,true);
			chmod($ptdir, 0777);
		}

		//Temp Dir
		if(!is_dir($ptdir."/tmp")){
			mkdir($ptdir."/tmp",0777,true);
			chmod($ptdir."/tmp", 0777);
		}
		if(!is_dir($ptdir."/tmp_scan")){
			mkdir($ptdir."/tmp_scan",0777,true);
			chmod($ptdir."/tmp_scan", 0777);
		}

		//Create Sub Dirs
		if(!empty($subDir)){
			$retPath = "";
			$arrDirs = explode("/",$subDir);
			$len = count($arrDirs);
			if($len > 0){
				for($i=0;$i<$len;$i++){
					if(!empty($arrDirs[$i])){
						$subNm = $this->validate($arrDirs[$i]);

						if(!empty($subNm)){
							$retPath .= "/".$subNm;
							if(!empty($this->pId)){
								if(!is_dir($ptdir.$retPath)){
									mkdir($ptdir.$retPath,0777,true);
									chmod($ptdir.$retPath, 0777);
								}
							}else{
								if(!is_dir($this->upDir.$retPath)){
									mkdir($this->upDir.$retPath,0777,true);
									chmod($this->upDir.$retPath, 0777);
								}
							}
						}
					}
				}
			}
		}
		///return
		if(!empty($flgRetType)){

			if($flgRetType=="i"){ $retPath = $this->pDir.$retPath;  }

			$retPath = $this->getFilePath($retPath,$flgRetType);

			if($flgRetType=="s")	{
				$retPath = realpath($retPath);
			}

			return $retPath;

		}else{
			return !empty($subDir) ? $retPath : 1;
		}
	}

	//removing special character and spaces from names
	public function validate($str){
		$str = str_replace(array(" ","%20"),"",$str);
		$str = preg_replace('/[^a-zA-Z0-9_-]/s', '', $str);
		return $str;
	}

	//return string of new name of file with time stamp attached to make uniqueness
	public function getFileNm($fname){

		$fExtIndex = strrpos($fname, "." );
		if($fExtIndex === false)
		{
			//Add Ext.
			$fNameExt = ".jpg";
		}
		else
		{
			//separate image name and ext
			$fNameExt = substr($fname,$fExtIndex);
			$fname = substr($fname,0,$fExtIndex);
			// Remove Other Characters
			$fname = $this->validate($fname);
		}

		//Apend timestamp in fileName
		$fname .= "-".time();

		//New File Name
		$fname = $fname.$fNameExt;
		return $fname;
	}

	//Create Thumb images and return tmp path $returnType is default is png.
	function createThumbs($pathToImages,$pathToThumbs="",$thumbWidth="75",$thumbHeight="75",$returnType=""){
		if(!is_file($pathToImages)||!file_exists($pathToImages)){
			//print("File Not Exits: ".$pathToImages);
			//print("File Does Not Exist.");
			return;
		}
		//
		if(strpos($pathToThumbs,".jpg")!==false&&empty($returnType)){ $returnType="jpg"; }
		//retPath
		$pathToWeb = $pathToThumbs;
		//set temp path
		if(empty($pathToThumbs)){
			$pathToThumbs = $this->upDir.$this->pDir."/tmp";
			//fileNm
			$random = rand(1,20)."".rand(21,40);
			$fileNmTmp = "/".$random."".time()."-".session_id().".png";
			$pathToThumbs .= $fileNmTmp;
			$pathToWeb = $this->pDir."/tmp".$fileNmTmp;
		}
		// parse path for the extension
		$info = pathinfo($pathToImages);
		// continue only if this is a JPEG image
		if ( strtolower($info['extension']) != 'pdf' ){
			// load image and get image size
			if ( strtolower($info['extension']) == 'jpg' || strtolower($info['extension']) == 'jpeg'){
				$img = imagecreatefromjpeg( "{$pathToImages}" );
			}
			else if ( strtolower($info['extension']) == 'gif' ){
				$img = imagecreatefromgif( "{$pathToImages}" );
			}
			else if ( strtolower($info['extension']) == 'png' ){
				$img = imagecreatefrompng( "{$pathToImages}" );
			}
			else if ( strtolower($info['extension']) == 'tif' || strtolower($info['extension']) == 'tiff' ){
				if($GLOBALS['gl_browser_name']!='ie'){
					$tifThumbPath = substr($pathToImages,0,-4).'_thumb.jpg';
					if(!file_exists($tifThumbPath)){
						exec('convert -density 150 -trim "'.$pathToImages.'" -strip -quality 100 -interlace line -colorspace RGB -resize '.$thumbWidth.' "'.$tifThumbPath.'"', $output, $return_var);
					}
					$tif_thumb_info = getimagesize($tifThumbPath);
					return array("imgWidth" => $tif_thumb_info[0], "imgHeight" => $tif_thumb_info[1]);
				}else{
					return array("imgWidth" => $width, "imgHeight" => $height);
				}
			}else{
				$tmp_img = $GLOBALS['incdir']."/../library/images/no_photo.jpg"; //dirname(__FILE__)."/../../../images/no_photo.png"
				$img = imagecreatefrompng($tmp_img);
			}

			$f_width = $f_height = $width = $height = 0;
			if($img) $f_width = $width = imagesx( $img );
			if($img) $f_height = $height = imagesy( $img );
			list($widthFrGetImSize, $heightFrGetImSize, $typeFrGetImSize, $attrFrGetImSize) = getimagesize("$pathToImages");
			if(($f_width == 0) && ($width == 0)){
				$f_width = $widthFrGetImSize;
				$width = $widthFrGetImSize;
			}
			if(($f_height == 0) && ($height == 0)){
				$f_height = $heightFrGetImSize;
				$height = $heightFrGetImSize;
			}

			$clm=3;
			$cc=1;
			do{
				$flgW = $flgH = true;
				//Check w/h smaller
				if($thumbWidth <= $thumbHeight){
					// calculate thumbnail size
					$height = floor( $height * ( $thumbWidth / $width ) );
					$width = $thumbWidth;
					if($height > $thumbHeight){
						$flgH = false;
					}
					if($height >= $f_height)
					$height = $f_height;
					if($width >= $f_width)
					$width = $f_width;
			  	}
				else{
					// calculate thumbnail size
					$width = floor( $width * ( $thumbHeight / $height ) );
					$height = $thumbHeight;
					if($width > $thumbWidth){
						$flgW = false;
					}
					if($height >= $f_height)
					$height = $f_height;
					if($width >= $f_width)
					$width = $f_width;
				}
				$cc = $cc + 1;
				if($cc == $clm){
					$flgW = $flgH = true;
				}
			}while($flgW != true || $flgH != true);

			//echo $width."---".$height."---".$widthFrGetImSize."---".$heightFrGetImSize."---".$typeFrGetImSize."---".$attrFrGetImSize."<br>";
			if($img){
				// create a new temporary image
				$tmp_img = imagecreatetruecolor( $width, $height );

				imagealphablending($tmp_img, false);
				imagesavealpha($tmp_img,true);
				$transparent = imagecolorallocatealpha($tmp_img, 255, 255, 255, 127);
				imagefilledrectangle($tmp_img, 0, 0, $width, $height, $transparent);

				// copy and resize old image into new image
				imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $width, $height, $f_width, $f_height );
				// save thumbnail into a file
				if($returnType==""){
					imagepng( $tmp_img, "{$pathToThumbs}",9 );
				}
				else if($returnType=="jpg"){
					imagejpeg( $tmp_img, "{$pathToThumbs}",100 );
				}
			}
			else{
				$arrReturnSize = array("imgWidth" => $width, "imgHeight" => $height);
			}
			if(file_exists($pathToThumbs) == true){
				$ret = (string)$pathToWeb;
			}
			else{
				$ret = (array)$arrReturnSize;
			}
		}
		return $ret;
	}

	function resize_image($filename, $w, $h, $w1, $h1){ //resizeImage($filename, 320, 240, 730, 465)

		$thumb = imagecreatetruecolor($w1,$h1);
		$source = imagecreatefromjpeg($filename);
		imagecopyresized($thumb, $source, 0, 0, 0, 0, $w1, $h1, $w, $h);
		imagejpeg($thumb,$filename,100);

	}

	//copy file to patient dir and return pointer to save in db
	public function copyfile($file,$test="",$f_namesave="",$subDirPath="",$stop_unlink='0'){
		$file_pointer = "";
		$f_name = $file["name"];
		$f_type=$file["type"];
		$f_size=$file["size"];
		$f_tmp=$file["tmp_name"];

		if(file_exists($f_tmp)){
			if($subDirPath) {
				$this->pDir = $subDirPath;
			}
			//Check Folder exists for patient
			if($this->ptDir()){
				//get file up name if not provided
				$f_name = !empty($f_namesave) ? $f_namesave : $this->getFileNm($f_name);
				//set patient dir
				$ptDir = $this->pDir;
				if(!empty($test)){
					$test = $this->ptDir($test);
					if(!empty($test)){
						$ptDir .= $test;
					}
				}

				//Set Upload path;
				$file_upload_path = $ptDir."/".$f_name;

				//copy file
				if(is_uploaded_file($f_tmp)){
					 move_uploaded_file($f_tmp,$this->upDir.$file_upload_path);
					 chmod($this->upDir.$file_upload_path,0777);
					 if(file_exists($f_tmp)){unlink($f_tmp);}
				}else{
					copy($f_tmp,$this->upDir.$file_upload_path);
					if(file_exists($f_tmp) && !$stop_unlink){unlink($f_tmp);}
				}
				if(!is_dir($this->upDir.$ptDir."/thumbnail")){
					mkdir($this->upDir.$ptDir."/thumbnail",0777,true);
				}
				$this->createThumbs(realpath($this->upDir.$file_upload_path),realpath($this->upDir.$ptDir."/thumbnail")."/".$f_name,100,100);

				if(!is_dir($this->upDir.$ptDir."/thumb")){
					mkdir($this->upDir.$ptDir."/thumb",0777,true);
				}
				$this->createThumbs(realpath($this->upDir.$file_upload_path),realpath($this->upDir.$ptDir."/thumb")."/".$f_name,500,500);
				//copy file
				//Set file pointer
				$file_pointer = $file_upload_path;

				//remove prefix
				if(!empty($this->prnt_fldr)&&!empty($this->pId)){
						$file_pointer = str_replace("/".$this->prnt_fldr, "", $file_pointer);
				}
			}
		}else{
			print("Error: file not exits.:".$f_tmp);
		}

		return $file_pointer;
	}

	//Unlink File
	public function unlinkfile($file){
		if(!empty($file) && file_exists($this->upDir.$file)){
			unlink($this->upDir.$file);

			//check related back file
			if(strpos($this->upDir.$file,".png")!==false){
				$drawingbackfile_b = str_replace(".png","_b.png",$this->upDir.$file);
				if(file_exists($drawingbackfile_b)){
					unlink($drawingbackfile_b);
				}
			}
		}
	}

	public function getFilePath($file, $type=""){
		if(!empty($file)){

			//add prefix
			if($type != "db2"){
				if(!empty($this->prnt_fldr)&&!empty($this->pId)){
						$file = "/".$this->prnt_fldr.$file;
				}
			}

			/*
			if(!function_exists("checkUrl4Remote")){
				include_once(dirname(__FILE__)."/functions.php");
			}
			*/
			if($type == "http"){
				if(!empty($file)){
					$tmp_http = "http" . (($_SERVER['SERVER_PORT']==443) ? "s://" : "://") . $_SERVER['HTTP_HOST'];
					$uppth = $this->upDirWeb;
					$file = $tmp_http."".$uppth.$file;
				}
			}else if($type == "w"){ //web
				//$file = checkUrl4Remote($GLOBALS['rootdir']."/main/uploaddir".$file);
				$file = $this->upDirWeb.$file;

			}else if($type == "h"){ // http
				$file = $this->upDirWeb.$file;
			}else if($type == "i"){ // include
				$file = $this->upDir.$file;
			}else if($type == "s"){ // save
				$file = $this->upDir.$this->pDir.$file;
			}else if($type == "db"){ // dbsave
				$file = $this->pDir.$file;
			}else if($type == "db2"){ // dbsave
				$dirpth = realpath($this->upDir);
				//
				if(!empty($this->prnt_fldr)&&!empty($this->pId)){
					$dirpth = $dirpth."/".$this->prnt_fldr;
				}

				$file = str_replace('\\', '/', $file);
				$dirpth = str_replace('\\', '/', $dirpth);
				if(stripos($file,$dirpth)!==false){
					$file=str_ireplace($dirpth, "",$file);
				}

			}else if($type == "w2"){ // web path from abs
				global $web_RootDirectoryName;
				$dirpth = empty($web_RootDirectoryName) ? $this->upDir : realpath($this->upDir); //if path is link, then do not realpath
				$dirpth = str_replace('\\', '/', $dirpth);
				if(stripos($file,$dirpth)!==false){
					$file=str_ireplace($dirpth, "",$file);
					$file = $this->upDirWeb.$file;
				}
			}else if($type == "w2i"){ //web to inc
				$file = str_replace($GLOBALS['webroot'], "", $file);
				$file = $GLOBALS['fileroot']."".$file;
			}
			else{
				$file = $this->upDir.$file;
			}
		}
		return $file;
	}

	function isFileExists($str){
		$ret=0;
		$s = $this->getFilePath($str, "i");
		if(file_exists($s)){$ret=1;}
		return $ret;
	}

	//Create Thumb images from IMagick and return tmp path
	function createThumbsFromIMagick($pathToImages,$pathToThumbs="",$thumbWidth="75",$thumbHeight="75"){
		if(!file_exists($pathToImages)){
			print("File Does Not Exist.");
			return;
		}
		$image = new Imagick($pathToImages);
		$type = $image->getimageFormat();
		//retPath
		$pathToWeb = $pathToThumbs;
		// continue only if this is a JPEG image
		if ( strtolower($type) != 'pdf' ){
			//set temp path
			if(empty($pathToThumbs)){
				$pathToThumbs = $this->upDir.$this->pDir."/tmp";
				//fileNm
				$random = rand(1,20)."".rand(21,40);
				$fileNmTmpOri = $random."".time()."-".session_id().".".$type;
				$fileNmTmp = "/".$fileNmTmpOri;
				$pathToThumbs .= $fileNmTmp;
				$pathToWeb = $this->pDir."/tmp".$fileNmTmp;
			}
			// load image and get image size
			$f_width = $width = $image->getimageWidth();
			$f_height = $height = $image->getimageHeight();
			$clm=3;
			$cc=1;
			do{
				$flgW = $flgH = true;
				//Check w/h smaller
				if($thumbWidth < $thumbHeight){
				  // calculate thumbnail size
				  $height = floor( $height * ( $thumbWidth / $width ) );
				  $width = $thumbWidth;
				  if($height > $thumbHeight){
					$flgH = false;
				  }
				}
				else{
				  // calculate thumbnail size
				  $width = floor( $width * ( $thumbHeight / $height ) );
				  $height = $thumbHeight;
				  if($width > $thumbWidth){
					$flgW = false;
				  }
				}
				$cc = $cc + 1;
				if($cc == $clm){
					$flgW = $flgH = true;
				}
			}while($flgW != true || $flgH != true);

			$image->resizeImage($width,$height,Imagick::FILTER_LANCZOS, 1);
			$image->setImagePage($width,$height, 0, 0);
			$image->writeImage($pathToThumbs);
		}
		$image->clear();
		$image->destroy();
		return (file_exists($pathToThumbs) ? $pathToWeb : "");
	}

	/// is this file OK
	function isFileOk($str){
		$str=strtolower($str);
		if(strpos($str,".jpg")===false&&strpos($str,".jpeg")===false&&strpos($str,".gif")===false&&
			strpos($str,".tiff")===false&&strpos($str,".png")===false){
			return false;
		}
		return true;
	}

	//move file  and provide pointer to save
	function copySign($str_specimen_sign,$flgCopy=0,$newnm=""){
		$retpath = "";
		$fullpath_tmp = $this->getFilePath($str_specimen_sign,"i"); //getfullpath
		if(file_exists($fullpath_tmp)){
			$tmpDirPth_up = $this->upDir;
			$tmpDirPth_sign = $this->ptDir("sign");
			$tmpDirPth_pt = ($this->flgUsr=="1") ? "/UserId_".$this->pId : "/PatientId_".$this->pId;
			$form_sign_path = $tmpDirPth_pt.$tmpDirPth_sign;
			$tmp_sign_path=realpath($tmpDirPth_up.$form_sign_path);

			//Make Image
			$img_nm = basename($fullpath_tmp);
			if(isset($newnm) && !empty($newnm)){$img_nm = $newnm;}
			$tmp_sign_path2=$tmp_sign_path."/".$img_nm;

			$bl = false;
			if($flgCopy==1){
				$bl = copy($fullpath_tmp, $tmp_sign_path2);
			}else{
				$bl = rename($fullpath_tmp, $tmp_sign_path2);
			}

			if($bl){
				$retpath = $form_sign_path."/".$img_nm;
			}
		}
		return $retpath;
	}

	//This Function create signature images in patient directory and provide save path which can be saved in dB
	function createSignImages($strpixls,$form_id,$signType){

		if($this->flgUsr=="1"){$form_id=rand(); $signType=rand();}

		$ret="";
		if(!empty($strpixls)){

			//include_once($GLOBALS['incdir']."/main/imgGdFun.php");

			$tmpDirPth_up = $this->upDir;
			$tmpDirPth_sign = ( $this->pId > 0 ) ? $this->ptDir("sign") : $this->ptDir("tmp/sign");
			$tmpDirPth_pt = '';
			if( $this->pId > 0 )
				$tmpDirPth_pt = ($this->flgUsr=="1") ? "/UserId_".$this->pId : "/PatientId_".$this->pId;
			$form_sign_path = $tmpDirPth_pt.$tmpDirPth_sign;
			$tmp_sign_path=realpath($tmpDirPth_up.$form_sign_path);

			//Make Image
			$img_nm = "/sig".$signType."_".time()."_".$form_id.".jpg";
			$tmp_sign_path2=$tmp_sign_path.$img_nm;


			//global $gdFilename;
			//$imgName= dirname(__FILE__)."/../../../images/white2.jpg";

			if(!empty($strpixls)&& strpos($strpixls,"data:image/png;base64,") !== false){
				$img_nm_tmp = "/".time().".png";
				$tmp_sign_path1=$tmp_sign_path.$img_nm_tmp;
				$strpixls = str_replace("data:image/png;base64,","",$strpixls);
				$r = file_put_contents($tmp_sign_path1, base64_decode($strpixls));

				//$backImg=$imgName;
				//if(!empty($backImg)){
					//$bakImgResource = imagecreatefromjpeg($backImg);
					$bakImgResource = imagecreatetruecolor(400,90);
					$canvasImgResource = imagecreatefrompng($tmp_sign_path1);
					// sets background to red
					$white = imagecolorallocate($bakImgResource, 255, 255, 255);
					imagefill($bakImgResource, 0, 0, $white);

					imagecopy($bakImgResource, $canvasImgResource, 0, 0, 0, 0, imagesx($bakImgResource), imagesy($bakImgResource));
					imagepng($bakImgResource, $tmp_sign_path1); //replace
				//}

			}
			//else	if($strpixls!="0-0-0:;"){
				//$img_nm_tmp = "/".time().".jpg";
				//$tmp_sign_path1=$tmp_sign_path.$img_nm_tmp;
				//drawOnImage_new($strpixls,$imgName,$tmp_sign_path1); //This is in img_
			//}

			$url = $this->createThumbs($tmp_sign_path1,$tmp_sign_path2,$thumbWidth="225",$thumbHeight="45",$returnType="jpg");
			$this->unlinkfile($form_sign_path.$img_nm_tmp);
			$ret= $form_sign_path.$img_nm;
		}

		return $ret;
	}

	//make drawing image from binary
	function mkHx2Img($data,$test, $indx=""){

		if(!empty($indx)){$indx="_".$indx;}
		$retpath = $this->ptDir($test);
		$drwName="".time().$indx.".png";
		$drawingFilePath= $this->getFilePath($retpath, "s");

		$drawingFilePath=realpath($drawingFilePath);
		$drawingFilePath.="/".$drwName;

		$data = str_replace("data:image/png;base64,","",$data);

		$r = file_put_contents($drawingFilePath, base64_decode($data));

		if($r!==false){
			//make thumb with back
			$backImg="";
			if($test=="CVF"){
				$backImg= $GLOBALS['incdir']."/../library/images/bg_drw_cvf.jpg"; //  dirname(__FILE__)."/../../../images/picEomCvf.jpg";
			}else if($test=="AmslerGrid"){
				$backImg= $GLOBALS['incdir']."/../library/images/amsler.jpg"; //dirname(__FILE__)."/../../../images/amsler.jpg";
			}

			if(!empty($backImg)){

				$drawingFilePath_s = str_replace(".png","_b.png",$drawingFilePath);

				$bakImgResource = (strpos($backImg,".png")!==false) ? imagecreatefrompng($backImg): imagecreatefromjpeg($backImg);
				$canvasImgResource = imagecreatefrompng($drawingFilePath);
				imagecopy($bakImgResource, $canvasImgResource, 0, 0, 0, 0, imagesx($bakImgResource), imagesy($bakImgResource));
				imagepng($bakImgResource, $drawingFilePath_s);
			}

			return  $this->pDir.$retpath."/".$drwName ;
		}else{
			return "";
		}
	}

	function getMiME($ext){
		$ret = "";
		$ext=strtolower($ext);
		switch($ext){
			case "gif":
			$ret = "image/gif";
			break;

			case "jpg":
			case "jpeg":
			$ret = "image/jpeg";
			break;

			case "png":
			$ret = "image/png";
			break;

			case "pdf":
			$ret = "application/pdf";
			break;

		}
		return $ret;

	}


	/**
		create a file
		$loc = "/test/fnm.html" OR "fnm.html"
		$dtd=optional
		return full path
		Example
		// will create under practice/ folder
		$osv = new SaveFile();
		$ret = $osv->cr_file("abc.html");

		// will create under patient
		$osv = new SaveFile(1); // argument patientid
		$ret = $osv->cr_file("/test1/test2/abc.html", "content");

		// will create under user
		$osv = new SaveFile(2, 1); // argument userid, 1
		$ret = $osv->cr_file("/test1/test2/abc.html", "content");

	**/
	function cr_file($loc, $data="", $chk_file_append=""){
		$ptDir = $this->upDir.$this->pDir;
		if(!empty($loc)){
			$loc = str_replace('\\',"/",$loc);
			if(strpos($loc,"/")!==false){
				$f_nm = basename($loc);
				$loc2 = str_replace($f_nm,'',$loc);
				if(!empty($loc2) && $loc2!="/"){
					$test = $this->ptDir($loc2);
					if(!empty($test)){
						$ptDir .= $test;
					}
				}
			}else{
				$f_nm = $loc;
			}

			//Set Upload path;
			$file_upload_path = $ptDir."/".$f_nm;

			if($chk_file_append!=""){
				$chk_file_append=FILE_APPEND;
			}else{
				$chk_file_append=0;
			}

			file_put_contents($file_upload_path,$data,$chk_file_append);

			//Set file pointer
			$file_pointer = $file_upload_path;
		}else{
			$file_pointer =$ptDir;
		}

		return $file_pointer;

	}

	function corImgPath4Pdf($str){
		$str = str_replace("src=\"".$GLOBALS['webroot'], "src=\"".$GLOBALS['fileroot'], $str);
		return $str;
	}

	// copy from pointer to pointer
	function copyFileP2P($ps, $pd){
		$pth = $this->upDir;
		copy($pth.$res["drwpth_od"],$pth.$destpath_od);
	}

	function getUploadDirPath($web=""){
		if($web==1){
			return $this->upDirWeb;
		}
		return $this->upDir;
	}

	function copy_file_p2p($source, $destination, $source_f_name, $dest_f_name){
		if( !is_dir($destination) ){
			mkdir($destination, 0777, true);
		}

		if(file_exists($source.'/'.$source_f_name)){
			copy($source.'/'.$source_f_name, $destination.'/'.$dest_f_name);
		}
	}

	function get_exam_new_section_file_dir(){
		$up = $this->getUploadDirPath();
		$up = $up."/exam_ext/html";
		if(!is_dir($up)){
			mkdir($up,0777,true);
			chmod($up, 0777);
		}
		return $up;
	}

	function cr_exam_new_section_file($str_name, $str_con){
		$up = $this->get_exam_new_section_file_dir();
		//
		if(!empty($str_con)){
			$file_upload_path = $up."/".$str_name;
			file_put_contents($file_upload_path,$str_con);
		}
	}

	/**
	purpose create work view db files xml
	**/
	function cr_wvexams_db($one_file=""){
		$up = $this->getUploadDirPath();
		$up = $up."/exam_ext/xml";
		$flgdo=0;
		if(!is_dir($up)){
			mkdir($up,0777,true);
			chmod($up, 0777);
			$flgdo=1;
		}
		if($flgdo==1||!empty($one_file)){
			//copy xml
			$src = $GLOBALS['incdir']."/chart_notes/xml";
			$dir = opendir($src);
			while(false !== ( $file = readdir($dir)) ) {
				if (( $file != "." ) && ( $file != ".." )) {

					if(!empty($one_file)){
						if($one_file != $file){ continue; }
					}

					copy($src . "/" . $file,$up . "/" . $file);
				}
			}
			closedir($dir);
		}
		return $up;
	}

	function get_print_file_path($fn){
		$ptDir = $this->upDir.$this->pDir;
		return $ptDir."/tmp/".$fn;
	}

	function get_dicom_data_path($c=""){
		$up = $this->getUploadDirPath();
		$up = $up."/dicom/";
		if(!empty($c)){
			if(!is_dir($up)){
				mkdir($up,0777,true);
				chmod($up, 0777);
			}
		}else{
			if(!is_dir($up)){$up="";}
		}
		return $up;
	}
}

?>
