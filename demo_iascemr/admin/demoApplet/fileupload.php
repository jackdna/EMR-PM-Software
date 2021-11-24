<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php

//require_once("../../../system/global.php");

// Turn off all error reporting
  error_reporting(0);

  //Username + password
  $user = "file";
  $pass = "password";

  //Show the number of files to upload
  $files_to_upload = 1;

  //Directory where the uploaded files have to come
  //RECOMMENDED TO SET ANOTHER DIRECTORY THEN THE DIRECTORY WHERE THIS SCRIPT IS IN!!
  $upload_dir = "uploaddir";

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

  session_start();

  //When REGISTERED_GLOBALS are off in php.ini
  //$_POST    = $HTTP_POST_VARS;
 // $_GET     = $HTTP_GET_VARS;
 // $_SESSION = $HTTP_SESSION_VARS;

  // User authentication disabled for the sake of simplicity

  //When logging in, check username and password
  //if($_GET['method'] == "login")
  //{
  //  if($_POST['username'] == $user && $_POST['password'] == $pass)
  //  {
      //Set the session for logged in to true
      //session_register('logged_in');
      $_SESSION['logged_in'] = true;
  //    Header("Location: ".$_SERVER['PHP_SELF']);
  //  }
  //}

  //Any other action the user must be logged in!
  if($_GET['method'])
  {

    //When not logged in, the user will be notified with a message
    //if(!session_is_registered('logged_in'))
	if(!isset($_SESSION['logged_in']))
    {
      not_allowed();
      exit;
    }
   // session_register('message');

    $_SESSION['message'] = "Ready";

    //Upload the file
    if($_GET['method'] == "upload")
    {

      //$file_array = $HTTP_POST_FILES['file'];
      $_SESSION['message'] = "";
      $uploads = false;

      for($i = 0 ; $i < $files_to_upload; $i++)
      {
        if($_FILES['file']['name'][$i])
        {
		  $_SESSION['message'] = "File data is there<br>";
		  $_SESSION['message'] = $_SESSION['message'].$_FILES['file']['name'][$i]."<br>";
          $uploads = true;
          if($_FILES['file']['name'][$i])
          {

            // ******** CHECK FILE EXTENSION & FILE SIZE*******************

            $extension = pathinfo($_FILES['file']['name'][$i]);
            $extension = $extension[extension];
            $extensionAccepted = 0;
            $allowed_paths = explode(", ", $allowed_ext);
            for($j = 0; $j < count($allowed_paths); $j++) {
                 if ($allowed_paths[$j] == "$extension") {
                     $extensionAccepted = 1;
                 }
            }

            if(! $extensionAccepted) {
                $_SESSION['message'] .= $_FILES['file']['name'][$i] . " has invalid extension.<br>";
                continue;
            }
            
            // donot allow .php
            if(eregi('\.php', $_FILES['file']['name'][$i])) {
                $_SESSION['message'] .= $_FILES['file']['name'][$i] . " has invalid extension 2.<br>";
                continue;
            }

            if($_FILES['file']['size'][$i] > $max_size) {
                $_SESSION['message'] .= "Size of the file " . $_FILES['file']['name'][$i] . " is too large.<br>";
                continue;
            }

            // ******


            $file_to_upload = $upload_dir."/".$_FILES['file']['name'][$i];
            move_uploaded_file($_FILES['file']['tmp_name'][$i],$file_to_upload);
			// echo $_FILES['file']['tmp_name'][$i] . " | " . $file_to_upload . "<hr>";
            chmod($file_to_upload,0777);
            $_SESSION['message'] .= $_FILES['file']['name'][$i]." uploaded.<br>";
			
          }
        }
      }
      if(!$uploads)  $_SESSION['message'] = "No files selected!";
    }

    //Logout
    elseif($_GET['method'] == "logout")
    {
      session_destroy();
    }

    //Delete the script
    elseif($_GET['method'] == "delete" && $_GET['file'])
    {
      //if(!@unlink($upload_dir."/".$_GET['file']))
      //  $_SESSION['message'] = "File not found!";
      //else
      //  $_SESSION['message'] = $_GET['file'] . " deleted";
      $_SESSION['message'] = "Ready";
    }

    //Download a file
    elseif($_GET['method'] == "download" && $_GET['file'])
    {
      $file = $upload_dir . "/" . $_GET['file'];
      $filename = basename( $file );
      $len = filesize( $file );
      header( "content-type: application/stream" );
      header( "content-length: " . $len );
      header( "content-disposition: attachment; filename=" . $filename );
      $fp=fopen( $file, "r" );
      fpassthru( $fp );
      exit;
    }

    //Rename a file
    elseif( $_GET['method'] == "rename" )
    {
      //rename( $upload_dir . "/" . $_GET['file'] , $upload_dir . "/" . $_GET['to'] );
      //$_SESSION['message'] = "Renamed " . $_GET['file'] . " to " . $_GET['to'];
      $_SESSION['message'] = "Ready";
    }

    // Show source
    elseif( $_GET['method'] == "source" )
    {
        highlight_file("fileupload.php");
        exit;
    }

    //Redirect to the script again
    Header("Location: " . $_SERVER['PHP_SELF']);
  }

  //HTML STARTING
?>
<html>
  <head>
  <title>FileUpload version 1.0</title>
  <link href="<?php echo $URL_DIR_CSS; ?>asprise.css" rel='stylesheet' type='text/css'>
</head>
<body bgcolor="#FFFFFF"><br><br>
<p align="center" class=titleOne>File Management System</p>
<p><br>
<table width=90% cellspacing="6" align="center" class=rowOdd>
  <tr>
    <?php

      //When there is a message, after an action, show it
     // if(session_is_registered('message'))
	 if(!isset($_SESSION['message']))
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
<td colspan=6><p align=center><br> <A href="<?php echo $_SERVER['PHP_SELF']; ?>?showall=yes';"><font face=arial size=2 color=#3333ff>Above lists 20 files uploaded (through web or JTwain Web Applet) lately. Click here to show all <?php echo sizeof($files); ?> files ...</font></a></p></td>
</tr>
<?php
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
                        <td width=20><A href="javascript:if(confirm('Are you sure to delete <?php echo $entry;?>?')) location.href='<?php echo $_SERVER['PHP_SELF']; ?>?method=delete&amp;file=<?php echo $entry;?>';"><img src='cross.gif' alt='Delete <?php echo $entry;?>' border=0></a></td>
                        <td width=20><A href='<?php echo $_SERVER['PHP_SELF']; ?>?method=download&amp;file=<?php echo $entry;?>'><img src='dl.gif' alt='Download <?php echo $entry;?>' border=0></a></td>
                        <td width=20><A href="javascript: var inserttext = ''; if(inserttext = prompt('Rename <?php echo $entry;?>. Fill in the new name for the file.','<?php echo $entry;?>')) location.href='<?php echo $_SERVER['PHP_SELF']; ?>?method=rename&amp;file=<?php echo $entry;?>&amp;to='+inserttext; "><img src='edit.gif' alt='Rename <?php echo $entry;?>' border=0></a></td>
                        <td class='filenfo' ><a href='<?php echo $upload_dir;?>/<?php echo $entry;?>' target='_blank'><?php echo $entry;?></a></td>
                        <td class='filenfo' width=70><?php echo $filesize;?></td>
                          <td class='filenfo' width=200><?php echo date ("F d Y H:i:s.", $filetime);?></td>
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
                     <form method='post' enctype='multipart/form-data' action='<?php echo $_SERVER['PHP_SELF']; ?>?method=upload'>
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
  Asprise!</a> | <a href='<?php echo $_SERVER['PHP_SELF']; ?>?method=source' target=_blnak>Show Source</a>
  </span> </div>
</body>
</html>
