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
			case 'companyOptions':
				console.log('companyOptions recieved');
				LOAD_relation_template(dataObj['data']);
				break;
			case 'deleteOption':
				console.log('deleteOption completed');

				// Now reload sectionB
				sectionB_Handler();
				break;
			case 'emptyOptionWithRatio':
				console.log('emptyOptionWithRatio recieved');
				LOAD_relation_template(dataObj['data']);
				break;
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
			//request = 'companyRatioList_' + objId;
			request = 'companyOptions_' + objId;
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

		var ratioList = data['ratioList'];
		var optionsList = data['optionsList'];

		var checkedRatios = [];
		var uncheckedRatios = [];

		//console.log('ratioObj: ' + ratioObj['ratio']);

		// If there are no options then all options are unchecked
		if(optionsList.length == 0){
			uncheckedRatios = ratioListConverter(ratioList);
		}

		console.log('ratioList length: ' + ratioList.length);

		// If there are options combine ratio & options for template
		for(option of optionsList){

			for(ratio of ratioList){
				
				// Empty option case
				if(ratio['emptyOption'] == true){
					var ratioObj = generateRatioObj(ratio, option);

					checkedRatios.push(ratioObj);
				}// General Case
				else if(option['ratio_id'] == ratio['id']){
					var ratioObj = generateRatioObj(ratio, option);

					checkedRatios.push(ratioObj);
				}else{

					//Option was not matched with ratio so generate with null option
					var ratioObj = generateRatioObj(ratio, null);

					uncheckedRatios.push(ratioObj);
				}
			}
		}

		console.log('checked: ' + checkedRatios);
		console.log('unchecked: ' + uncheckedRatios);

		//Wrap data
		var dataToPass = [];
		dataToPass['checkedRatios'] = checkedRatios;
		dataToPass['uncheckedRatios'] = uncheckedRatios;

		var source = $('#relation_template').html();
		var r_template = Handlebars.compile(source);

		var html = r_template(dataToPass);
		$('.sectionB').append(html);

		// adminSubmit now valid
		$('#adminSubmit').fadeIn();

		addCheckBoxHandlers();

		
	}

	function generateRatioObj(ratio, option){
		console.log('-> generateRatioObj');
		console.log('ratioName: ' + ratio['name']);


		var ratioObj = getObjTemplate('ratioObj');

		ratioObj['ratio_id'] = ratio['id'];
		ratioObj['ratio_name'] = ratio['name'];
		ratioObj['div_id'] = ratio['name'];
		ratioObj['ratio_value'] = ratio['value'];
		ratioObj['ratio_checkboxID'] = getRatioCheckBoxId(ratio);
		ratioObj['option'].push(option);

		return ratioObj;
	}

	//Convert ratiolist to ratioObjFormat
	function ratioListConverter(ratioList){
		ratioObjList = [];

		for(ratio of ratioList){
			ratioObj = getObjTemplate('ratioObj');	

			ratioObj['ratio_id'] = ratio['id'];
			ratioObj['div_id'] = ratio['name'];
			ratioObj['ratio_name'] = ratio['name'];
			ratioObj['ratio_value'] = ratio['value'];
			ratioObj['ratio_checkboxID'] = getRatioCheckBoxId(ratio);

			ratioObjList.push(ratioObj);
		}

		return ratioObjList;
	}

	function getRatioCheckBoxId(ratio){
		return ratio['name'] + '_checkBox';
	}

	function addCheckBoxHandlers(){

		//Attach event handlers to checkboxes
		var inputs = document.forms['relationCheckForm'].getElementsByTagName('input');
		var checkedBoxes = [];
		var uncheckedBoxes = [];

		//Filter inputs down to checkboxes
		for(var i = 0; i < inputs.length; i++){
			var input = inputs[i];

			if(input.type == 'checkbox'){
				console.log
				input.onclick = getCall(input);
			}

		}
	}

	function getCall(checkbox){
		console.log('inputClass: ' + checkbox.className);
		console.log('nameId: ' + checkbox.name);


		if(checkbox.className == 'checkedOption'){
			
			return function(){
				checkedBoxHandler(checkbox);
			}

		}
		else if (checkbox.className == 'uncheckedOption'){

			return function(){
				uncheckedBoxHandler(checkbox);
			}

		}

	}

	function deleteTextInputFields(ratioId){
		// Take ratioId and compId and delete row from Options table
		compId = document.getElementById('objSelector').value;

		console.log('objSelector: ' + compId);
		var request = generateDeleteOptionRequest(compId, ratioId);
		processObj_JSON_list(dataHandler, request);



	}

	//Delete an existing option
	function checkedBoxHandler(checkbox){
		msg = checkbox.name + " CheckBox UnPressed";
		console.log(msg);
		console.log("Alert User of Delete");

		var reply = confirm('Do you want to delete this ratio?');
		console.log('Reply: ' + reply);

		if(reply == true){
			// Delete all entry fields
			deleteTextInputFields(checkbox.value);
		}else{
			console.log('ID: ' + checkbox.id)
			//Reset checkbox
			document.getElementById(checkbox.id).checked = true;

		}
	}

	// create an option
	function uncheckedBoxHandler(checkbox){
		msg = checkbox.name + " CheckBox Pressed";
		console.log(msg);
		console.log('AlertUser of creation');

		var reply = confirm("Do you want to create this ratio?");
		console.log('Reply: ' + reply);

		if(reply == true){
			//Add fields
			var ratioId = checkbox.value;
			processObj_JSON_list(dataHandler, 'emptyOptionWithRatio_' + ratioId);

		}else{
			//Reset checkbox
			document.getElementById(checkbox.id).checked = true;
		}
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


	//Object factory
	function getObjTemplate(obj){
		if(obj == 'ratioObj'){
			var ratioObj = {
				ratio_id:null,
				ratio_name:null,
				ratio_value:null,
				div_id:null,
				ratio_checkboxID:null,
				option_id:null,

				option: []
			};

			return ratioObj;
		}
	}


	// Wrap delete option request
	// This is sensative shtuff.
	function generateDeleteOptionRequest(compId, ratioId){
		// Request format: 
		// deleteOption_`compId`_`ratioId`
		// compId MUST precede ratioId

		return 'deleteOption_' + compId + '_' + ratioId; 
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
		{{#each checkedRatios}}
			<div class='checkedOptionDiv' id='{{this.div_id}}'>
				<input type='checkbox' value='{{this.ratio_id}}' class='checkedOption' id="{{this.ratio_checkboxID}}"  name='{{this.ratio_name}}' checked>{{this.ratio_name}}<br>
				{{#each option}}
					<p>Small</p>
					<input type='text' id='{{this.id}}_x_small' value={{x_small}}>
				    <input type='text' id='{{this.id}}_y_small' value={{y_small}}> <br>
				    <br>
				    <p>Med</p>
				    <input type='text' id='{{this.id}}_x_med' value={{x_med}}>
				    <input type='text' id='{{this.id}}_y_med' value={{y_med}}> <br>
				    <br>
				    <p>Large</p>
				    <input type='text' id='{{this.id}}_x_large' value={{x_large}}>
				    <input type='text' id='{{this.id}}_y_large' value={{y_large}}> <br>
				{{/each}}
			</div>

		{{/each}}
		{{#each uncheckedRatios}}
			<div class='uncheckedOptionDiv' id='{{this.div_id}}'>
				<input type='checkbox' value='{{this.ratio_id}}' class='uncheckedOption' id='{{this.ratio_checkboxID}}' name='{{this.ratio_name}}' >{{this.ratio_name}}<br>

			</div>
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
