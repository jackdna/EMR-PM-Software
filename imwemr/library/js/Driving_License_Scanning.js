	var scan_completed = 0;
	function set_Focus(){
		//Resets the value of the invis text box to null
		document.getElementById("scanner").value="";
		if( document.getElementById("divAjaxLoader") ) {
			document.getElementById("divAjaxLoader").style.display = 'block';
		} else if( top.show_loading_image) {
				top.show_loading_image('show');
		}	
		//sets the focus to the invisiable text box.
		document.getElementById("scanner").focus();
		scan_completed=0;
		
		//Run this check 6 times at 10 seconds intervals. This is the dirty way to do this.
		setTimeout(function(){scan_Complete()},5000);
		setTimeout(function(){scan_Complete()},10000);
		setTimeout(function(){scan_Complete()},15000);
		setTimeout(function(){scan_Complete()},20000);
		setTimeout(function(){scan_Complete()},25000);
		setTimeout(function(){scan_Complete()},30000);
	}

	function scan_Complete(){
		//Check this variable before doing anything else. 
		if(scan_completed == 1){
			if( document.getElementById("divAjaxLoader") ) {
					document.getElementById("divAjaxLoader").style.display = 'none';
			} else if( top.show_loading_image) {
				top.show_loading_image('hide');
			}
 			
			return;
		}else{
			//Pulled from the scanner element	
			x = document.getElementById("scanner").value;
			n = x.indexOf("*");
			//checks for a * in the string
			if(n > 0 ){
				//also kill this function
					
				//If the check comes back greater then 0 then run the string through the parsing function
				parse_Driving_License_Feed(x);	
			}			
		}
	}


	function parse_Driving_License_Feed(data){
		console.log(data);
		scan_completed = 1;
		
		processing = data.split(";");

		processing_fname = processing[0];
		processing_mname = processing[1];
		processing_lname = processing[2];
		processing_dob = processing[3];
		processing_address1 = processing[4];
		processing_address2 = processing[5];
		processing_city = processing[6];
		processing_state = processing[7];
		processing_zip = processing[8];
		processing_driving_license = processing[9];
		processing_gender = processing[10];

		parsed_first_name = processing_fname.split(":");
		parsed_m_name = processing_mname.split(":");
		parsed_last_name = processing_lname.split(":");
		parsed_dob = processing_dob.split(":");
		parsed_address1 = processing_address1.split(":");
		parsed_address2 = processing_address2.split(":");
		parsed_city = processing_city.split(":");
		parsed_state = processing_state.split(":");
		parsed_zip = processing_zip.split(":");
		parsed_driving_license = processing_driving_license.split(":");
		parsed_gender = processing_gender.split(":");


		first_name = parsed_first_name[1];	
		m_name = parsed_m_name[1];
		last_name = parsed_last_name[1];
		dob = parsed_dob[1];
		address1 = parsed_address1[1];
		address2 = parsed_address2[1];
		city = parsed_city[1];
		state = parsed_state[1];
		zip = parsed_zip[1];
		driving_license = parsed_driving_license[1];
		gender =  parsed_gender[1];

		
		//Sometimes DL list gender with a 1 or 2, 1 being male, 2 being female

			if(gender == 1||gender == "M"){
				gender = "Male";				
			}
			if(gender == 2|| gender == "F"){
				gender = "Female";				
			}

		//Cut up the zip code
			if(zip.length>5){
				//grabs the zipcode
				zip1 = zip.substr(0,5);
				//grabs the extention
				zip2 = zip.substr(5,8);
			}else{	
				//This meaning that only the first 5 digits of the zip code are present.
				zip1 = zip;
				zip2 = "";
			}

		
		//DOB REFORMATING

		//Grabs the year
			DOB1 = dob.substr(0,4);
		//grabs the month
			DOB2 = dob.substr(4,2);
		//grabs the day
			DOB3 = dob.substr(6,2);

			dob = DOB2 + "-" + DOB3 + "-" + DOB1;
			
		push_Data_To_Document(first_name, m_name, last_name, dob, address1, address2, city, state, zip1, zip2, driving_license, gender);
	}



	function push_Data_To_Document(first_name, m_name, last_name, dob, address1, address2, city, state, zip1, zip2, driving_license, gender){
		if(confirm("Do you want to auto populate these values \n First Name: " + first_name + "\n Middle Name: " + m_name + "\n Last Name : " + last_name + "\n Date of Birth: " + dob + "\n Address 1: " + address1 + "\n Address 2: " + address2 + "\n City: " + city + "\n State: " + state + "\n Zipcode: " + zip1 + "\n Zipcode Extension: " + zip2 + "\n Driving License: " + driving_license + "\n Gender: " + gender )){
			document.getElementById("fname").value = first_name;
			document.getElementById("mname").value = m_name;
			document.getElementById("lname").value = last_name;
			document.getElementById("dob").value = dob;
			document.getElementById("street").value = address1;
			document.getElementById("city").value = city;
			document.getElementById("state").value = state;
			document.getElementById("code").value = zip1;
			document.getElementById("dlicence").value = driving_license;															
			if(gender == "Male"){
				document.getElementById("title").selectedIndex = 1;
				if(document.getElementById("sex")!=null){
					document.getElementById("sex").selectedIndex = 1;
				}
				if(document.getElementById("selGender")!=null){
					document.getElementById("selGender").selectedIndex = 1;
				}
			}
				
			if(gender == "Female"){
				document.getElementById("title").selectedIndex = 3;		
				if(document.getElementById("sex")!=null){
					document.getElementById("sex").selectedIndex = 2;
				}
				if(document.getElementById("selGender")!=null){
					document.getElementById("selGender").selectedIndex = 2;
				}
			}
		}
		
		if( document.getElementById("divAjaxLoader") ) {
			document.getElementById("divAjaxLoader").style.display = 'none';
		} else if( top.show_loading_image) {
			top.show_loading_image('hide');
		}	
		
	}