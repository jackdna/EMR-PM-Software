// correction value calculator

function myTrim(str,f)
{
	str = str.replace(/^\s+|\s+$/, '');
	if(f==1)str = str.replace(/^(\&nbsp\;)+|(\&nbsp\;)+$/g, '');
	return str;
}

function cor_refineCorrectionValue(mxc,mnc,mx,mn,av)
{
	var totalVal = parseInt(mx) + parseInt(mn);
	var avgVal = parseInt(totalVal/2);
	//alert(av +" : "+ avgVal);
	if(av >= avgVal)
	{
		return mnc;
	}else if(av < avgVal)
	{
		return mxc;
	} 	
}

function cor_getCorrectionValue(val)
{
	var maxVal,minVal,corVal,maxCorVal,minCorVal;
	
	
	if((val < 445) || (val > 645))
	{
		//alert("Average Value is out of the range of Correction Table (445 - 645).");		
		if(val > 645){
			return ">-7";
		}
		else if(val < 445){
			return ">7";
		}
		
	}
	else if((val >= 445) && (val < 455))
	{
		maxCorVal = 7;
		minCorVal = 6;
		maxVal = 445;
		minVal = 455;		
	}
	else if((val >= 455) && (val < 465))
	{
		maxCorVal = 6;
		minCorVal = 6;
		maxVal = 455;
		minVal = 465;		
				
	}
	else if((val >= 465) && (val < 475))
	{
		maxCorVal = 6;
		minCorVal = 5;
		maxVal = 465;
		minVal = 475;					
	}
	else if((val >= 475) && (val < 485))
	{
		maxCorVal = 5;
		minCorVal = 4;
		maxVal = 475;
		minVal = 485;					
	}
	else if((val >= 485) && (val < 495))
	{
		maxCorVal = 4;
		minCorVal = 4;
		maxVal = 485;
		minVal = 495;					
	}
	else if((val >= 495) && (val < 505))
	{
		maxCorVal = 4;
		minCorVal = 3;		
		maxVal = 495;
		minVal = 505;			
	}
	else if((val >= 505) && (val < 515))
	{
		maxCorVal = 3;
		minCorVal = 2;
		maxVal = 505;
		minVal = 515;					
	}
	else if((val >= 515) && (val < 525))
	{
		maxCorVal = 2;
		minCorVal = 1;
		maxVal = 515;
		minVal = 525;					
	}
	else if((val >= 525) && (val < 535))
	{
		maxCorVal = 1;
		minCorVal = 1;
		maxVal = 525;
		minVal = 535;					
	}
	else if((val >= 535) && (val < 545))
	{
		maxCorVal = 1;
		minCorVal = 0;
		maxVal = 545;
		minVal = 535;					
	}
	else if((val >= 545) && (val < 555))
	{
		maxCorVal = 0;
		minCorVal = -1;
		maxVal = 545;
		minVal = 555;				
	}
	else if((val >= 555) && (val < 565))
	{
		maxCorVal = -1;
		minCorVal = -1;
		maxVal = 555;
		minVal = 565;					
	}
	else if((val >= 565) && (val < 575))
	{
		maxCorVal = -1;
		minCorVal = -2;
		maxVal = 565;
		minVal = 575;					
	}
	else if((val >= 575) && (val < 585))
	{
		maxCorVal = -2;
		minCorVal = -3;
		maxVal = 575;
		minVal = 585;						
	}
	else if((val >= 585) && (val < 595))
	{
		maxCorVal = -3;
		minCorVal = -4;
		maxVal = 585;
		minVal = 595;					
	}
	else if((val >= 595) && (val < 605))
	{
		maxCorVal = -4;
		minCorVal = -4;
		maxVal = 595;
		minVal = 605;					
	}
	else if((val >= 605) && (val < 615))
	{
		maxCorVal = -4;
		minCorVal = -5;
		maxVal = 605;
		minVal = 615;					
	}
	else if((val >= 615) && (val < 625))
	{
		maxCorVal = -5;
		minCorVal = -6;
		maxVal = 615;
		minVal = 625;					
	}
	else if((val >= 625) && (val < 635))
	{
		maxCorVal = -6;
		minCorVal = -6;
		maxVal = 625;
		minVal = 635;					
	}
	else if((val >= 635) && (val <= 645))
	{
		maxCorVal = -6;
		minCorVal = -7;
		maxVal = 635;
		minVal = 645;		
	}
	corVal = cor_refineCorrectionValue(maxCorVal,minCorVal,maxVal,minVal,val);
		
	return corVal; 
}

function cor_isValid(strVal)
{
	var bag = "0123456789,";
	var strLength = strVal.length;
	var chr;
	
	for(i=0;i<strLength;i++)
	{
		chr = strVal.charAt(i);
		if(bag.indexOf(chr) == -1)
		{
			// no Char 
			return false;
		}			
	}
	return true;
}

function cor_calCorrectionVal(val)
{	
	var correctionVal,avgReading,counter;
	var isvalid = false;
	//Remove Spaces
	var strVal = myTrim(val);	
	//Check Value for numeric
	if(!cor_isValid(strVal))
	{
		top.fAlert("Please enter comma separated Numeric values only.<br />(0123456789,)");
	}
	else
	{	
		isvalid = true;		
		var arrReadings = new Array();	
		arrReadings = val.split(",");
		//Length of Array
		var arrLength = arrReadings.length;
		counter = 0;
		var pachyReading = 0;				
		//Add all values
		for(i=0;i<arrLength;i++)
		{
			if(myTrim(arrReadings[i]) != "")
			{		
				pachyReading = parseInt(pachyReading) + parseInt(arrReadings[i]);
				counter += 1;			
			}			
		}				
		//Get Avg 
		avgReading = parseInt(pachyReading)/parseInt(counter);
		avgReading = Math.round(avgReading);
		//Correction Value 	
		correctionVal = cor_getCorrectionValue(avgReading);  
	}
	
	return {"isvalid": isvalid, "correctionVal":correctionVal, "avgReading":avgReading, "counter":counter};
}	