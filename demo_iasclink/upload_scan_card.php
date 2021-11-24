<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php 
session_start();
include_once("common/conDb.php"); 
$userauthorized = $_SESSION['iolink_loginUserId'];

//$fdr_pat_img="../patient_access/patient_photos/";

// Turn off all error reporting
  error_reporting(0);

  //Username + password
  //$user = "file";
  //$pass = "password";
  
  #### NO DEBUG
  		print_r($_SESSION);
		print_r($_GET);
		print_r($_POST);		  	
  #### NO DEBUG
  //Show the number of files to upload
  $files_to_upload = 1;

  //Directory where the uploaded files have to come
  //RECOMMENDED TO SET ANOTHER DIRECTORY THEN THE DIRECTORY WHERE THIS SCRIPT IS IN!!
  $upload_dir = "imedic_uploaddir"; //"../patient_access/patient_photos/";

  // **************** ADDED BY LAB Asprise! ********************

  $allowed_ext = "jpeg, jpg, gif, png, pdf";
  $max_size = 1024 * 500; // Max: 500K.



  // -------------------------------- //
  //           UPDATE LOG             //
  // -------------------------------- //
  // Version 1.0 -> version 1.1
  // - Confirm the deletion of the file
  // - Download a file (handy for PHP files ;))
  // - Set the number of files to upload
  //
  // Version 1.1 -> version 1.11
  // - Able to rename the files
  // -------------------------------- //

  // -------------------------------- //
  //     SCRIPT UNDER THIS LINE!      //
  // -------------------------------- //

  //session_start();

  //When REGISTERED_GLOBALS are off in php.ini
 // $_POST    = $HTTP_POST_VARS;
  //$_GET     = $HTTP_GET_VARS;
  //$_SESSION = $HTTP_SESSION_VARS;

  // User authentication disabled for the sake of simplicity
  
  //Any other action the user must be logged in!
 

  if($_GET['method'])
  {

    //Upload the file
    if($_GET['method'] == "upload")
    {

      //$file_array = $HTTP_POST_FILES['file'];
     // $_SESSION['message'] = "";
      $uploads = false;
	  
      for($i = 0 ; $i < $files_to_upload; $i++)
      {
		  
        if($_FILES['file']['name'][$i])
        {
          $uploads = true;
          if($_FILES['file']['name'][$i])
          {

            // ******** CHECK FILE EXTENSION & FILE SIZE*******************

            $extension = pathinfo($_FILES['file']['name'][$i]);
            $extension = $extension['extension'];
            $extensionAccepted = 0;
            $allowed_paths = explode(", ", $allowed_ext);
            for($j = 0; $j < count($allowed_paths); $j++) {
                 if ($allowed_paths[$j] == "$extension") {
                     $extensionAccepted = 1;
                 }
            }
			//$aa = fopen("imedic_uploaddir/data.txt","a"); //For Debugging
			//fwrite($aa, $_REQUEST['patient_id']." @@ ".$_FILES['file']['name'][$i]." @@ ".$imageName." @@ ".$PSize." @@ ".$pConfirmId." @@ ".$patient_id." @@ ".$patient_in_waiting_id." @@ ".$scanClinical." <br> \n\r "); //For Debugging
            /*
			if(!$extensionAccepted) {
                $message .= $_FILES['file']['name'][$i] . " has invalid extension.<br>";
                continue;
            }*/
            
            // donot allow .php
            if(preg_match('/^.*\.(php)$/i', $_FILES['file']['name'][$i])) {
                $message .= $_FILES['file']['name'][$i] . " has invalid extension 2.<br>";
                continue;
            }

            /* if($_FILES['file']['size'][$i] > $max_size) {
                $message .= "Size of the file " . $_FILES['file']['name'][$i] . " is too large.<br>";
                continue;
            } */

            // ******

            /// Create Directory of Patient and upload image
			
			if(isset($_REQUEST['patient_id']) && !empty($_REQUEST['patient_id']))
			{
				$pid = $_REQUEST['patient_id'];
				//Patient Directory Name
				$patientDir = "/PatientId_".$pid;				
				//Check
				if(!is_dir($upload_dir.$patientDir))
				{
					//Create patient directory
					mkdir($upload_dir.$patientDir, 0777,true);		
				}else{
					chmod($upload_dir.$patientDir,0777);				
				}
			}
			else
			{
				//Tmp Directory
				$patientDir = "/temp";					
				//Check
				if(!is_dir($upload_dir.$patientDir))
				{
					//Create patient directory
					mkdir($upload_dir.$patientDir, 0700);						
				}
			}		
			
			$fileName = $_FILES['file']['name'][$i];
			$fileNameExtIndex = strrpos ( $fileName, "." );
			
			if($fileNameExtIndex === false)
			{
				//Add Ext.
				$fileNameExt = ".jpg";
			}
			else
			{
				//separate image name and ext
				$fileNameExt = substr($fileName,$fileNameExtIndex);					
				$fileName = substr($fileName,0,$fileNameExtIndex);						
			}
			
			//Apend timestamp in fileName
			$fileName .= "-".time();					
			
			//New File Name
			$fileName = $fileName.$fileNameExt;										
			$file_to_upload = $upload_dir.$patientDir."/".$fileName;
			/////			
			
			//$file_to_upload = $upload_dir."/".$_FILES['file']['name'][$i];
            move_uploaded_file($_FILES['file']['tmp_name'][$i],$file_to_upload);
            // echo $_FILES['file']['tmp_name'][$i] . " | " . $file_to_upload . "<hr>";
            chmod($file_to_upload,0777);
			
			$pid = $_REQUEST['patient_id'];
			$doctitle = $_POST["DocTitle"]; 	
			$scandoc = $patientDir."/".$fileName; //$_FILES['file']['name'][$i];
			$tp="";
			$tp=$_GET['tp'];
			$isRecordExists = $_GET['isRecordExists']; 
			//$_SESSION['tp']=$_GET['tp'];		
			$ins_caseid = $_REQUEST['currentCaseid'];
			$patient_id = $_REQUEST['patient_id'];	
			$operator_id = $_SESSION['iolink_loginUserId'];	
			$type = $_REQUEST['tp'];				
			if($isRecordExists)
			{
				$qry = "select scan_card,scan_card2  from insurance_data where id = $isRecordExists";
				$qryId = imw_query($qry);
				list($scan_card,$scan_card2) = imw_fetch_array($qryId);
				if($scan_card != '' && $scan_card2 == ''){
					$qryupd=imw_query("update insurance_data set scan_card2='$scandoc', scan_label2='$doctitle', cardscan1_datetime= now() where id = $isRecordExists");
				}
				else if($scan_card == ''){
					$qryupd=imw_query("update insurance_data set scan_card='$scandoc', scan_label='$doctitle', cardscan_date = now() where id = $isRecordExists");
				}
			}
			else
			{
				$insuranceSess = "scan_card_".$tp;				
				$insuranceSessLabel = "scan_label_".$tp;				
				$_SESSION[$insuranceSess] = $scandoc;
				$_SESSION[$insuranceSessLabel] = $doctitle;	
				$qry = "select scan_documents_id,scan_card from iolink_insurance_scan_documents 
						where type = '$type' and ins_caseid = $ins_caseid
						and patient_id = $patient_id and document_status = '0'";
				$qryId = imw_query($qry);
				list($scan_documents_id,$scan_card) = imw_fetch_array($qryId);
				if($scan_documents_id == ''){
					$qry = "insert into iolink_insurance_scan_documents set 
							type = '$type',ins_caseid = $ins_caseid,
							patient_id = $patient_id,scan_card = '$scandoc',
							scan_label = '$doctitle',created_date = now(),cardscan_date = now(),
							operator_id = $operator_id";
				}
				else if($scan_documents_id != '' && $scan_card == ''){
					$qry = "update iolink_insurance_scan_documents set 
							type = '$type',ins_caseid = $ins_caseid,
							patient_id = $patient_id,scan_card = '$scandoc',
							scan_label = '$doctitle',created_date = now(),cardscan_date = now(),
							operator_id = $operator_id
							where scan_documents_id = $scan_documents_id";
				}
				else if($scan_documents_id != '' && $scan_card != ''){
					$qry = "update iolink_insurance_scan_documents set 
							type = '$type',ins_caseid = $ins_caseid,
							patient_id = $patient_id,scan_card2 = '$scandoc',
							scan_label2 = '$doctitle',cardscan1_date = now(),
							operator_id = $operator_id
							where scan_documents_id = $scan_documents_id";
				}
				imw_query($qry);
			}						
            $message .= $_FILES['file']['name'][$i]." uploaded.<br>";
          }
        }
      }
      if(!$uploads)  $message = "No files selected!";
    }	
	header("Location: new_scan_card.php");
	exit;
  }

  //HTML STARTING
?>
<html>
  <head>
  <title>iMedic</title>
  <link href="<? echo $URL_DIR_CSS; ?>asprise.css" rel='stylesheet' type='text/css'>
</head>
<body bgcolor="#FFFFFF"><br><br>
<p align="center" class=titleOne>File Management System</p>
<p><br>
<table width=90% cellspacing="6" align="center" class=rowOdd>
  <tr>
    <?php

      //When there is a message, after an action, show it
      if(isset($_SESSION['message']))
      {
        echo "<td colspan=6 class='header' align='center'><font color='red'>" . $_SESSION['message'] . "</font></td></tr><tr>";
      }else{
          echo "<td colspan=6 class='header' align='center'><font color='red'>Ready</font></td></tr><tr>";
      }
    ?>
                       <td colspan=4 class='header'>File</td>
                       <td class='header'>Size</td>
                       <td class='header'>Created on</td></tr>
    <?php
      //Handle for the directory
      $handle = opendir($upload_dir);

      //Walk the directory for the files

    // ************* SORTING *********************

    $d = dir($upload_dir);

    $c = 0; // array counter

    while($current_file = $d->read()) {
        //  don't want the dot files

        if ($current_file != "." && $current_file != "..") {

            $files[$c]['name'] = $current_file ;

            // get the last mod time, we need it as a unix timestamp so we
            // can sort it

            $files[$c]['lastmod_time'] = "" . (filemtime($upload_dir . "/" .  $current_file));
            $c ++ ;
        }
    }

    // now we need a sort function
    function time_cmp ($a, $b) {
        return strcmp($b['lastmod_time'], $a['lastmod_time']);
    }

    // sort the array
    usort ($files, "time_cmp");


    $counter = 0;
    // ***********

      while(list($key, $val) = each($files))
      {
        if((! $showall) && $counter > 20) {
?>
<tr>
<td colspan=6><p align=center><br> <A href="<? echo $_SERVER['PHP_SELF']; ?>?showall=yes';"><font face=arial size=2 color=#3333ff>Above lists 20 files uploaded (through web or JTwain Web Applet) lately. Click here to show all <? echo sizeof($files); ?> files ...</font></a></p></td>
</tr>
<?
            break;
        }
            
          $entry = $val['name'];

        if($entry != ".." && $entry != ".")
        {

          //Set the filesize type (bytes, KiloBytes of MegaBytes)
          $filesize = filesize($upload_dir . "/" . $entry);
          $type = Array ('b', 'KB', 'MB');
          for ($i = 0; $filesize > 1024; $i++)
            $filesize /= 1024;
          $filesize = round ($filesize, 2)." $type[$i]";

          $filetime = filemtime($upload_dir . "/" . $entry);
  ?>
                      <tr>
                        <td width=20><A href="javascript:if(confirm('Are you sure to delete <?=$entry;?>?')) location.href='<? echo $_SERVER['PHP_SELF']; ?>?method=delete&amp;file=<?=$entry;?>';"><img src='cross.gif' alt='Delete <?=$entry;?>' border=0></a></td>
                        <td width=20><A href='<? echo $_SERVER['PHP_SELF']; ?>?method=download&amp;file=<?=$entry;?>'><img src='dl.gif' alt='Download <?=$entry;?>' border=0></a></td>
                        <td width=20><A href="javascript: var inserttext = ''; if(inserttext = prompt('Rename <?=$entry;?>. Fill in the new name for the file.','<?=$entry;?>')) location.href='<? echo $_SERVER['PHP_SELF']; ?>?method=rename&amp;file=<?=$entry;?>&amp;to='+inserttext; "><img src='edit.gif' alt='Rename <?=$entry;?>' border=0></a></td>
                        <td class='filenfo' ><a href='<?=$upload_dir;?>/<?=$entry;?>' target='_blank'><?=$entry;?></a></td>
                        <td class='filenfo' width=70><?=$filesize;?></td>
                          <td class='filenfo' width=200><? echo date ("F d Y H:i:s.", $filetime);?></td>
                      </tr>
  <?php
          $counter ++;
        }
      }
  ?>
                     <tr>
                 <td colspan=6 height="20"></td></tr><tr>
    <td colspan=6 bordercolor="#999999" bgcolor="#666666" height="1"></td>
                     </tr><tr>
                 <td colspan=6 height="10"></td></tr>
                     <form method='post' enctype='multipart/form-data' action='<? echo $_SERVER['PHP_SELF']; ?>?method=upload'>
  <?php
      for( $i = 0; $i < $files_to_upload; $i++ )
      {
  ?>

					<tr align="center" valign="middle">
						  <td colspan=6 class="text">
						  <p>
						  <font color=red>Please do not upload pornographics. Your IP will be logged.</font>
						  </p>
						  <p>
						  Upload a file:
							<input type='file' name='file[]' style='width: 300' class=textbox>
						  </td>
                     </tr>
  <?php
      }
  ?>
                     <tr>
                       <td colspan=6 align='center'><input type='submit' value='Upload' class=button></td>
                     </tr>
                     </form>
                   </table>

<div align="center"><span class="text"><br>
  FileUpload version 1.1 Written
  by My-PHP | Modified by <a href="http://asprise.com/" target="_blank">LAB
  Asprise!</a> | <a href='<? echo $_SERVER['PHP_SELF']; ?>?method=source' target=_blnak>Show Source</a>
  </span> </div>
</body>
</html>


