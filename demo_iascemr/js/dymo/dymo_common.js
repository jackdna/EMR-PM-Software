
/*Triggered on Cheking che checkbox to use Dymo printers*/
function selectDymoPrinter(){
	
	var dymoPritners = document.getElementById("dymoPrintersList");
	if($('#sel_dymo').is(':checked')){
		
		dymoPritners.disabled=false;
		top.$(".loader").fadeIn('fast').show('fast');
		
		loadDymoPrinters();
		
		top.$(".loader").fadeOut('fast').hide('fast');
	}
	else{
		dymoPritners.disabled=true;
		$(dymoPritners).html('').selectpicker('refresh');
		$('#pinters_div').slideUp('slow');
	}
}

function loadDymoPrinters(){
	
	var printersSelect = document.getElementById("dymoPrintersList");
	$(printersSelect).html('');

	var printers = dymo.label.framework.getLabelWriterPrinters();
	if (printers.length == 0){
		modalAlert("No DYMO LabelWriter printers are installed.<br />Install DYMO LabelWriter printers.");
		return;
	}

	for (var i = 0; i < printers.length; ++i){
		var printer = printers[i];
		var printerName = printer.name;
		
		var option = document.createElement('option');
		option.value = printerName;
		if(printerName === selectedPritner){
			$(option).attr('selected', true);
		}
		option.appendChild(document.createTextNode(printerName));
		
		$(printersSelect).append(option);
	}
	$(printersSelect).selectpicker('refresh');
	$('#pinters_div').slideDown('slow');
}

function printLabels(printersName, labelData){
	
	try{
		
		printersName = $.trim(printersName);
		
		if(printersName==""){
			modalAlert("No DYMO LabelWriter printers are installed. Install DYMO LabelWriter printers.");
			return;
		}
		
		// Load label Structure
		var label;
		// use jQuery API to load label
		$.ajax({
			url: "js/dymo/Address.label",
			cache: false,
			async: false,
			success:function(data){
				label = dymo.label.framework.openLabelXml(data);
			}
		});
		
		if (!label){
			modalAlert("Load label before printing");
			return;
		}
		
		if(labelData.length>0){
			// set data using LabelSet and text markup
			var labelSet = new dymo.label.framework.LabelSetBuilder();
			var record; 
			/*Getting Data from Tabele*/
			copies = parseInt($('#range').val());
			$.each(labelData, function(key, values){
				
				for(i=0; i<copies; i++){
					content = values.dos+"<br/>";
					content += values.surgeon+"<br/>";
					content += '<b>'+values.pt+"</b><br/>";
					content += '<b>'+values.dob+"</b><br/>";
					content += '<b>'+values.proc+"</b><br/>";

					textArea = document.createElement('textarea');
					textArea.innerHTML = content;
					content  = textArea.value;
					//console.log(content);
					/*Add Data to Dymo LabelSet*/
					record = labelSet.addRecord();
					record.setTextMarkup('ADDRESS', content);
				}
				/*End Add Data to Dymo LabelSet*/
			
			});
			label.print(printersName, null, labelSet.toString());
			delete labelSet;
		}
		else{
			modalAlert("Data does not exists to print labels");
		}
		/*End Getting Data from Table*/
	}
	catch(e){
		modalAlert(e.message || e);
	}
}

$.fn.serializeObject = function(){
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] || o[this.name] == '') {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};