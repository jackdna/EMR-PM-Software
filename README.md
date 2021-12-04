ReadMe

IMWEMR is EMR/PM software, along with integrated iascemr (surgerycenter) and optical, repo is ready and preconfigured for subdomain http://demo.domain.com
you just need to place files in webroot and replace apache conf files, if you want to setup new site please follow below instruction.

Login to CentOS server and switch user.
#sudo su

Install Repos and Services
Check if EPEL is installed
#yum -v repolist

Install EPEL repository 
#yum install https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm

      ------yum install epel-release
Install remi repository (used for PHP 5)
#yum install http://rpms.remirepo.net/enterprise/remi-release-7.rpm

Install mysql CentOS 7 repo
#yum -y install  yum localinstall https://dev.mysql.com/get/mysql57-community-release-el7-9.noarch.rpm

Make sure EPEL, REMI, and MYSQL repos are installed
#yum -v repolist
 
Disable remi-php54 and enable remi-php73
#yum-config-manager --disable remi-php54
#yum-config-manager --enable remi-php73

Install basic utilities
#yum -y install vim wget zip unzip bzip2 screen htop rsync unar bind-utils yum-utils pv
      ------bind-utils for nslookup--------

Install some more uilities for Application
#yum -y install ImageMagick ImageMagick-devel ImageMagick-c++ ghostscript zbar zbar-devel java subversion git git-core 

         -----ImageMagick is required for creating thumbnails and doing other image manipulation
         -----zbar barcode scanning
         -----java required for signature pads
         -----git git-core if you are using versioning
		 
#yum -y localinstall https://www.linuxglobal.com/static/blog/pdftk-2.02-1.el7.x86_64.rpm
         -----pdftk is for pdf splitting and merging
		 
Install Apache
#yum install httpd mod_ssl
   
   
If the server is also the DB server, install the mysql server along with client
#yum -y remove mariadb-libs
#yum localinstall https://dev.mysql.com/get/mysql57-community-release-el7-9.noarch.rpm
#yum update
#yum install mysql-community-server 
	  
Get mysql temp password  
#grep 'A temporary password' /var/log/mysqld.log |tail -1 

#service mysqld start
	  
#/usr/bin/mysql_secure_installation
	  
Create DB

CREATE NEW DATABASES
	#mysql> CREATE DATABASE demo_imwemr;
	#mysql> CREATE DATABASE demo_imwemr_scan;
	#mysql> CREATE DATABASE demo_iascemr;
 
CREATE MYSQL USER FOR DATABASE
#mysql> CREATE USER 'db_user'@'localhost' IDENTIFIED BY 'password';
 
GRANT Permission to User on Database
#mysql> GRANT ALL ON *.* TO 'db_user'@'localhost';
 
RELOAD PRIVILEGES
#mysql> FLUSH PRIVILEGES;

	  
Install PHP 7
#yum install php php-mysqlnd php-tidy php-json php-process php-gd php-pear php-bcmath php-common php php-pdo php-soap php-runtime php-mbstring php-cli php-xml php-devel php-pecl-zip php-pecl-imagick php-pecl-ssh2 php-zip php-mcrypt php-curl php-pecl-apcu php-pecl-apcu-devel --enablerepo=remi-php73	  
      
One time machine security config
#vim /etc/selinux/config
Set SELINUX=permissive
#setenforce 0
#getenforce
This should return "Permissive"
If firewall is there
Allow http/https/MySQL Traffic

Configure PHP
#vim /etc/php.ini
      short_open_tag = on
      max_input_vars = 100000
      upload_max_filesize = 36M
      post_max_size = 36M
      memory_limit = 1024M
	  max_execution_time=60
Seach for and add the following line: 
      cgi.fix_pathinfo=0

    
Enable and start Apache
#systemctl start httpd
#systemctl enable httpd
 
Check Versions
#php -v
#httpd -v


Create test files
#cd /var/www/html

#vim info.php

Put below code in info.php file to get information about PHP7.
<?php
   phpinfo();
?>

Check in browser by visiting url and if works we will proceed to next steps.

Restart Apache
#systemctl restart httpd



Application Install 
	
Copy all application files into /var/www/html/
	
Change owner of files
#cd imwemr/
#chown -R apache.apache cache/
#chown -R apache.apache iMedicMonitor/cache/
#chown -R apache.apache data

Permissions on SigPlus
#cd /var/www/html/
#chown -R apache.apache SigPlusLinuxHSB

------Sigplus is signature application -----------


Import IMW Databases (Clean Database for New Clients)
We have created 3 Databases main database is demo_imw, for scanned document data its demo_imw_scan database and demo_scemr for surgery center database.
These databases are clean databases which will enough to start provisioning new client and ready to go with production.
From database folder restore all these databases in MySQL server and set user with appropriate permissions.
   
   
Set up a site to with Subdomain setup
#cd /var/www/html/imwemr/config

Edit globals.php accordingly.  
     
#vim globals.php
         $RootDirectoryName = "imwemr";
         $webServerRootDirectoryName = "/var/www/html/"
         define('PRODUCT_VERSION_DATE', 'Jan 24, 2021 - V1.0.0'); //April 24, 2020

Create the config folder for the site
#cd /var/www/html/imwemr/config
#mkdir -p demo
#cd demo/
#vim config_demo.php
         
         $myInternalIP = "demo.yourdomain.com";
         $myExternalIP = "demo.yourdomain.com";
         $phpServerIP = "demo.yourdomain.com";

         Also modify DB connections
         $sqlconf = array();
         $sqlconf["host"]= 'db-cluster.host';
         $sqlconf["port"] = '3306';
         $sqlconf["login"] = 'imw_user';
         $sqlconf["pass"] = 'password1';
         $sqlconf['idoc_db_name'] = 'demo_imwemr';
         $sqlconf['scan_db_name'] = 'demo_imwemr_scan';



Create apache config file for site
#cd /etc/httpd/conf.d
#vim demo.conf
      <VirtualHost *:80>
    DocumentRoot "/var/www/html/imwemr"
    ServerName demo.yourdomain.com

 
</VirtualHost>


Restart apache
#systemctl restart httpd


Test Application
Go to URL and test application and login
Username: demo
Password: P@ssw0rd1


Optical Setup

#cd /var/www/html/demo_optical
   

Edit config.php -- Go to config directory, copy sample file, edit it
#cd /var/www/html/demo_optical/config
   
#vim config.php
Edit the config file and change the following three variables to the Site-Specific values:
   $host='demo.yourdomain.com';
   $GLOBALS['DIR_PATH']="/var/www/html/demo_optical";
   $GLOBALS['IMW_DIR_PATH']= "/var/www/html/imwemr";
   $GLOBALS['SUB_DOMAIN']="demo";

   $GLOBALS['IMW_DB_NAME'] = "demo_imw";
   $sqlconf["host"]= 'yourdatabaseserverhostname';
   $sqlconf["port"] = '3306';
   $sqlconf["login"] = 'mysql_user';
   $sqlconf["pass"] = 'mysqluserpassword';


Assign file permissions
#cd /var/www/html/demo_optical
#chown -R apache.apache interface/patient_interface/uploaddir/
#chown -R apache.apache images/
#chown -R apache.apache library/new_html2pdf/

Modify config file to enable Optical icon in EMR/PM
#cd /var/www/html/imwemr/config/demo
#vim config_demo.php

Uncomment this line and set the directory name to “optical”
$GLOBALS['optical_directory_name'] = "optical";// Optical Directory Name

Uncomment the following line as well
define("connect_optical","1");//IF connect_optical VALUE IS 1 THEN Optical Functionality WILL BE Work in iDOC. 

Copy .htaccess file from imwemr directory
#cp /var/www/html/imwemr/.htaccess /var/www/html/demo_optical/

Edit apache config to enable Aliases. Reload Apache
#vim /etc/httpd/conf.d/demo.conf

Remove comment under <VirtualHost 172.31.100.131:80>
Alias /optical /var/www/html/demo_optical


Reload apache
#service httpd reload

Test Application
Go to URL and test application and login
Username: demo
Password: P@ssw0rd1

We can change password from application even from mysql tables (SHA256) based password used with lower case. Go to any online password website and generate your own password.



IASCEMR Setup

Configure SurgeryCenter to connect to IMWEMR
#cd /var/www/html/demo_iascemr/
#vim connect_imwemr.php
Configure to connect to demo_imwemr
      $imw_db_name = 'demo_imwemr';
      $imw_host = 'mysqlserverhostnameorip';
      $imw_port = '3306';
      $imw_login = 'mysql_user';
      $imw_pass = 'password1';

Configure IMW to connection to SC
#cd /var/www/html/imwemr/config/demo
#vim config_demo.php
Uncomment the SC connection info at bottom of file and fill in DB name: ['sc_db_name'] = 'demo_scemr';
      $sqlconf['sc_db_name'] = 'demo_scemr';

Configure SurgeryCenter
#cd /var/www/html/demo_iascemr/common
#vim conDb.php
   
      $asc_db_name = 'demo_scemr';
      $asc_host = 'mysqlhostnameorip';
      $asc_port = '3306';
      $asc_login = 'mysql_user';
      $asc_pass = 'password1';

      $surgeryCenterDirectoryName='demo_iascemr';
      $iolinkDirectoryName='demo_iasclink';
      $imwPracticeName='demo';
      
      Also change “imwDirectoryName”. This name should be same as of Root Dir of EHR/PM.
      $imwDirectoryName='imwemr';

Assign File Permissions
   cd /var/www/html/demo_iascemr
   chown -R apache.apache new_html2pdf
   chown -R apache.apache html2pdf
   chown -R apache.apache xml
   chown -R apache.apache SigPlus_images
   chown -R apache.apache sx_grid_images
   chown -R apache.apache pdf
   chown -R apache.apache html2pdfnew
   chown -R apache.apache testPdf.html
   chown -R apache.apache white.jpg
   chown -R apache.apache crons/logs
   chown -R apache.apache admin/pdfFiles
   chown apache.apache new_html2pdf_reports 
   chown apache.apache new_html2pdf_reports/*.jpg 
   chown apache.apache new_html2pdf_reports/*.html 

Edit Apache Config
#vim /etc/httpd/conf.d/demo.conf
   Remove comment under <VirtualHost ...:80>
      Alias /iascemr /var/www/html/demo_iascemr

   
Reload the apache service
#service httpd reload

Go to the url and test and login
SurgeryCenter is not integrated with main application so visit according to Alias.
for example : demo.yourdomain.com/iascemr/
   Username: admin
   Password: P@ssw0rd1
   
 

IASCLink Setup

Configure IASCLink to connect to IMW
#cd /var/www/html/demo_iasclink/
#vim connect_imwemr.php
Configure to connect to demo_imwemr
      $imw_db_name = 'demo_imwemr';
      $imw_host = 'mysqlhostnameorip';
      $imw_port = '3306';
      $imw_login = 'mysql_user';
      $imw_pass = 'password1';

Configure iASCLink
#cd /var/www/html/demo_iasclink/common
#vim conDb.php
      $asc_db_name = 'demo_scemr';
      $asc_host = 'msyqlhostnameorip';
      $asc_port = '3306';
      $asc_login = 'mysql_user';
      $asc_pass = 'password1';

      $surgeryCenterDirectoryName='demo_iascemr';
      $iolinkDirectoryName='demo_iasclink';
      $imwPracticeName='demo';
      
      Also change “imwDirectoryName”. This name should be same as of Root Dir of EHR/PM.
      $imwDirectoryName='imwemr';

Set permissions
   cd /var/www/html/demo_iasclink
   chown -R apache.apache html2pdfnew
   chown -R apache.apache imedic_uploaddir
   chown -R apache.apache SigPlus_images
   chown -R apache.apache new_html2pdf
   chown -R apache.apache new_html2pdf/pdffile.html
   chown -R apache.apache testPdf.html
   chown -R apache.apache pdfSplit
   chown -R apache.apache admin/pdfFiles

Edit Apache Config
#vim /etc/httpd/conf.d/demo.conf
Remove comment under <VirtualHost ...:80>
      Alias /iasclink /var/www/html/demo_iasclink

   

Reload the apache service
#service httpd reload

Enable the iASCLink in EMR
#cd /var/www/html/imwemr/config/demo
#vim config_demo.php
   Change value of $GLOBALS['iasclink_directory_name'] to "iasclink"
      $GLOBALS['iasclink_directory_name']     = "iasclink";// iASCLink Directory Name

Go to url and test and login with 
   Username: iolink
   Password: P@ssw0rd1
-------------------------------------------

Notes : 
1. There are additional software needed for scanning and other equipments, get vendor support for that.
2. Any PHP error, enable php log within each pages or globally.
3. Password can be change both from application and MySQL tables usualy in "users" table.
4. Software might have errors please raise bug fix issues.
