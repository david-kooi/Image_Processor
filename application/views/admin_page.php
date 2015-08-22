<!DOCTYPE html>
<html lang="en">

<script src='<?php echo base_url();?>js/jquery-1.11.2.js'></script>
<script src='<?php echo base_url();?>js/handlebars-v3.0.3.js'></script>

<script>
	
	/*

		[action dropdown] [reciver dropdown]
	
		{Section A
			- If create company or ratio:
				Skip to SectionB and load empty fields

			- If edit or delete company or ratio:
				{templateA_comp_or_ratio
					[obj dropdown]
				}

			- If create or update or delete relation:
				{templateA_relation
					[relation template]
				}
					
		}
		
		{Section B
			- If company:
				{templateB_comp_or_ratio_fields
					-Load company fields
				}
			- If ratio:
				{templateB_comp_or_ratio_fields
					-Load ratio fields
				}
			- If relation
				- Do nothing...template already loaded

		}

	*/


	$(document).ready(function(){
		sectionA_Handler()
	});

	

	/*
		Handles generation of section A
	*/
	function sectionA_Handler(){
		//Clear all previous templates
		clearTemplates();

		console.log('----SectionA_Handler-----');

		var action = document.getElementById('actionDropDown').value;
		var reciever = document.getElementById('recieverDropDown').value;

		console.log('action: ' + action);
		console.log('reciever: ' + reciever);

		// reciever case
		// Load company list so user can select what company to apply relation to
		if (reciever == 'relation'){
			console.log('relation case');

			// Gets company list
			// Data handler populates the objSelector
			processObj_JSON_list(dataHandler, 'companyList');
			
		}
		// create company or ratio case
		else if(action == 'create'){

			//No need for user to select company or ratio...they are creating a new one
			sectionB_create_with_empty_fields();

		}
		else if(action == 'update' || action == 'delete'){
			//Populate object list
			var objList = null;
			if(reciever == 'company'){
				processObj_JSON_list(dataHandler, 'companyList');
			}
			else if(reciever == 'ratio'){
				processObj_JSON_list(dataHandler, 'ratioList');
			}
			sectionB_Handler();
		}
	
	}

	/*
		Data Requests
	*/
	function processObj_JSON_list(dataHandler, request){
		console.log('--- processObj_JSON_list ---');
		console.log('request: ' + request);

		$.ajax({
			url:"<?echo base_url()?>master_controller/clientRequest",
			data:{requestHeader: request},
			method:'POST'
		}).done(function(data){
			dataHandler(data);
		});
	}
	
	function dataHandler(data){
		var reciever = document.getElementById('recieverDropDown');

		console.log('-----Data Handler-----');
		dataObj = JSON.parse(data);
		console.log('data: ' + data)

		switch(dataObj.header){
			case 'companyList':
				console.log('companyList recieved.');
				LOAD_select_comp_or_ratio_template(dataObj['data']);
				break;
			case 'ratioList':
				console.log('ratioList recieved');
				LOAD_select_comp_or_ratio_template(dataObj['data']);
				break;
			case 'emptyCompany':
				console.log('emptyCompany recieved');
				LOAD_input_fields(dataObj['data']);
				break;
			case 'emptyRatio':
				console.log('emptyRatio recieved');
				LOAD_input_fields(dataObj['data']);
				break;
			case 'companyRatioList':
				console.log('companyRatioList recieved');
				LOAD_relation_template(dataObj['data']);
				break;
			case 'updateCompany':
				LOAD_input_fields(dataObj['data']);
				break;
			case 'updateRatio':
				LOAD_input_fields(dataObj['data']);
			default:
				console.log('dataObj header not recognized.');


		}
	}

	function sectionB_create_with_empty_fields(){
		console.log('Creating fields as EMPTY');

		var reciever = document.getElementById('recieverDropDown').value;

		fieldData = null;
		//Get empty fields
		if (reciever == 'company'){
			//Add empty company fields
			processObj_JSON_list(dataHandler, 'emptyCompany');
		}
		else if (reciever == 'ratio'){
			//Add empty ratio fields
			processObj_JSON_list(dataHandler, 'emptyRatio');
		}
	}
	/*
		Handles fields
	*/
	function sectionB_Handler(){
		console.log('------- Section B Handler --------');

		var action = document.getElementById('actionDropDown').value;
		var reciever = document.getElementById('recieverDropDown').value;
		var objId = document.getElementById('objSelector').value;

		console.log('objId: ' + objId);

		// reciever case:
		// Load reciever template
		if (reciever == 'relation'){
			request = 'companyRatioList_' + objId;
			processObj_JSON_list(dataHandler, request);
		}
		// delete Case:
		// - Nothing to be loaded...delete handled by adminSubmit
		else if (action == 'delete'){
			//Do Nothing
		}
		// update case:
		else if(action == 'update'){
			
			if(reciever == 'company'){
				
				console.log('update company: ' + objId);
				request = 'updateCompany_' + objId;
				processObj_JSON_list(dataHandler, request);
			}
			else if (reciever == 'ratio'){

				console.log('update ratio: ' + objId);
				request = 'updateRatio_' + objId;
				processObj_JSON_list(dataHandler, request);
			}
		}

	}

	function sleep(milliseconds) {
	  var start = new Date().getTime();
		for (var i = 0; i < 1e7; i++) {
		    if ((new Date().getTime() - start) > milliseconds){
		      break;
		    }
		  }
		}

	function clearTemplates(){
		clearObjFields();
		clearRelationTemplate();
		clearObjSelector();
	}

	function clearObjFields(){

		// If objFields exists remove so it can be reloaded
		if($('#objFields') != null){
			$('#objFields').remove();
		}

	}

	function clearObjSelector(){
		// If objSelector exists remove so it can be reloaded
		if($('#objSelector') != null){
			$('#objSelector').remove();
		}
	}

	function clearRelationTemplate(){
		if($('#relationCheckForm') != null){
			$('#relationCheckForm').remove();
		}
	}

	/*
		Loads dropdown for create/edit companies and ratios
	*/
	function LOAD_select_comp_or_ratio_template(data){
		clearObjSelector();
		clearObjFields();

		console.log('Loading objSelector');

		//Wrap data
		var dataToPass = [];
		dataToPass['data'] = data;
		console.log('data: ' + dataToPass['data']);

		var source = $("#select_comp_or_ratio_template").html();
		var s_c_r_template = Handlebars.compile(source);

		//dat = {'array':[{"id":"2","name":"CompanyA"},{"id":"3","name":"CompanyB"}]}
		var html = s_c_r_template(dataToPass);

		//Append to template div
		$('.sectionA').append(html);

		sectionB_Handler();

	}

	function LOAD_input_fields(data){
		var action = document.getElementById('actionDropDown').value;
		if(action != 'update'){
			clearObjSelector();
		}
		clearObjFields();

		console.log('LOADING input_fields');

		// adminSubmit now valid
		$('#adminSubmit').fadeIn();

		//Filter and wrap data
		dataToPass = [];
		fields = [];

		for(var key in data){
			//No need to specify Id
			// That is done by the database
			if(key == 'id' || key == 'checked'){
				continue;
			}

			field = [];
			field['fieldName'] = key;
			field['fieldValue'] = data[key];
			fields.push(field);

			console.log('Field: ' + field['fieldName']);
		}

		dataToPass['fields'] = fields;

		var source = $('#input_fields_template').html();
		var i_f_template = Handlebars.compile(source);

		var html = i_f_template(dataToPass);

		$('.sectionB').append(html);

	}

	function LOAD_relation_template(data){
		clearObjFields();
		clearRelationTemplate();

		console.log('LOADING relation_template');

		//Wrap data
		var dataToPass = [];
		dataToPass['ratios'] = data;
		console.log('ratios: ' + dataToPass['ratios']);

		// adminSubmit now valid
		$('#adminSubmit').fadeIn();

		var source = $('#relation_template').html();
		var r_template = Handlebars.compile(source);

		var html = r_template(dataToPass);
		$('.sectionB').append(html);
	}

	function adminSubmit(){
		var action = document.getElementById('actionDropDown').value;
		var reciever = document.getElementById('recieverDropDown').value;

		// Relation Case
		if (reciever == 'relation'){
			var company = document.getElementById('objSelector').value;

			//Get section B data, wrap in Command, then POST to server
		}
		// Company and Ratio
		else if (reciever == 'company'){
			// Get company field data, wrap in Command, then POST
		}
		else if (reciever == 'ratio'){
			// Get ratio field data, wrap in Command, then POST
		}
		
	}


</script>


<script id="select_comp_or_ratio_template" type="text/x-handlebars-template">

	<select id='objSelector' onchange='sectionB_Handler()'>
	{{#each data}}
		<option value={{this.id}}>{{this.name}}</option>
	{{/each}}
	</select>

</script>

<script id="input_fields_template" type="text/x-handlebars-template">

	<form id='objFields'>
		{{#each fields}}
			<p>{{this.fieldName}}</p>
			<input type='text' id='{{this.fieldName}}' value='{{this.fieldValue}}'>
			<br>
		{{/each}}
	</form>

</script>

<script id='relation_template' type='text/x-handlebars-template'>
	

	<form id='relationCheckForm'>
		{{#each ratios}}
			<input type='checkbox' value='{{this.id}}'>{{this.name}}<br>
		{{/each}}

	</form>


</script>


<head>

</head>

<body>

	<div id='selections'>
		<select id='actionDropDown' onchange='sectionA_Handler()'>
			<option value='create'>Create</option>
			<option value='update'>Update</option>
			<option value='delete'>Delete</option>
		</select>


		<select id='recieverDropDown' onchange='sectionA_Handler()'>
			<option value='company'>Company</option>
			<option value='ratio'>Ratio</option>
			<option value='relation'>Relation</option>
		</select>
	</div>

	<div class='sectionA'>


	</div>

	<div class='sectionB'>


	</div>

	<button onclick='adminSubmit()' id='adminSubmit' hidden>Submit</button>

</body>