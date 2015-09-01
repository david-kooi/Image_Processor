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

	function client_JSON_dataPush(process, header, data){
		$.ajax({
			url:"<?echo base_url()?>master_controller/clientPush",
			data:{pushHeader: header,
				  data: data,
				  process: process},
		    method:'POST'
		}).done(function(status){

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
				break;
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
			if(key == 'id' || key == 'checked' || key == 'emptyOption' || key == 'fk_comp'){
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


	function UPDATE_relation_template(data){
		console.log('LOADING relation_template');

		var ratioList = data['ratioList'];
		var optionsList = data['optionsList'];

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

		console.log('optionList length: ' + optionsList.length);
		console.log('ratioList length: ' + ratioList.length);

		// If there are options combine ratio & options for template
		for(option of optionsList){

			for(ratio of ratioList){
				
				// General Case
				 if(option['ratio_id'] == ratio['id']){
					console.log('ratio-option match');	
					ratio['checked'] = true;

					var ratioObj = generateRatioObj(ratio, option);

					checkedRatios.push(ratioObj);
				}
			}
		}

		console.log('Adding unchecked ratios');
		// Iterate through ratio list and all all unchecked ratio's to unchecked list
		for(ratio of ratioList){

			if(ratio['checked'] == false){
				//Option was not matched with ratio so generate with null option
				var ratioObj = generateRatioObj(ratio, null);

				uncheckedRatios.push(ratioObj);
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

		if(option != null){
			console.log('optionId: ' + option['id']);
			ratioObj['optionId'] = option['id'];
		}

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

	function deleteTextInputFields(checkbox){
		// Take ratioId and compId and delete row from Options table
		var optionDiv = checkbox.parent();
		optionDiv.attr('class', 'uncheckedOptionDiv');
		//parentDiv.setAttribute('class', 'uncheckedOptionDiv');
		//document.getElementById(parentDiv.id)

		console.log('optionDiv: ' + optionDiv.attr('id'));
		inputKids = optionDiv.children();

		//Remove Inputs
		for(var i = 0; i < inputKids.length; i++){
			input = inputKids[i];
			console.log('input: ' + input.id);


			//Continue if checkbox
			if(input.type=='checkbox'){
				continue;
			}
			input.remove();
		}

		//Reset the checkbox handlers
		addCheckBoxHandlers();

		//Actual delete handled by adminSubmit

	}

	//Delete an existing option
	function checkedBoxHandler(checkbox){
		msg = checkbox.name + " CheckBox UnPressed";
		console.log(msg);
		console.log("Alert User of Delete");

		var reply = confirm('Do you want to delete this ratio?');
		console.log('Reply: ' + reply);

		if(reply == true){
			// Delete all entry fields from parent
			checkbox.setAttribute('class', 'uncheckedOption');
			checkbox.setAttribute('checked', false);
			deleteTextInputFields($('#'+checkbox.id));
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
			var compId = document.getElementById('objSelector').value;
			processObj_JSON_list(dataHandler, 'emptyOptionWithRatio_' + ratioId + '_' + compId);

		}else{
			//Reset checkbox
			document.getElementById(checkbox.id).checked = true;
		}
	}

	function adminSubmit(){
		console.log('-----adminSubmit-----');

		var action = document.getElementById('actionDropDown').value;
		var reciever = document.getElementById('recieverDropDown').value;

		// Relation Case
		if (reciever == 'relation'){
			console.log('reciever: relation');

			var companyId = document.getElementById('objSelector').value;

			//This is what will be sent to server
			var relationPackage = getObjTemplate('relationPackage');
			relationPackage['comp_id'] = companyId;


			var checkedOptions = $("form").children('.checkedOptionDiv');

			//List to hold objects
			var optionList = []
			// Create an option object for each
			var option = getObjTemplate('option');
			for(var i = 0; i < checkedOptions.length; i++){
				optionDiv = checkedOptions[i];

				console.log('ratioId: ' + optionDiv.dataset.ratioid);
				option['ratio_id'] = optionDiv.dataset.ratioid;

				//Iterate through sizes
				var sizeInputs = $(optionDiv).children('input');
				for(var j = 0; j < sizeInputs.length; j++){
					size = sizeInputs[j];

					//Skip checkbox
					if(size.type != 'text'){
						continue;
					}

					console.log('option[' + size.id + '] = ' + size.value);
					option[size.id] = size.value;
				}
				option['comp_id'] = companyId;
				optionList.push(option);

			}
			//Add to relationPackage
			relationPackage['optionList'] = optionList


			//Options to delete
			var deleteList = [];
			var uncheckedOptions = $("form").children('.uncheckedOptionDiv');
			console.log('uncheckedOptions: ' + uncheckedOptions.length);
			for(var j = 0; j < uncheckedOptions.length; j++){
				option = uncheckedOptions[j];	

				// Delete Command
				var deleteCmd = getObjTemplate('deleteCmd');

				console.log('deleteCmd ratio_id: ' +option.dataset.ratioid );
				deleteCmd['ratio_id'] = option.dataset.ratioid;
				deleteCmd['comp_id'] = document.getElementById('objSelector').value;

				deleteList.push(deleteCmd);
			}
			relationPackage['deleteList'] = deleteList;

			data = JSON.stringify(relationPackage);
			//Get section B data, wrap in Command, then POST to server


			//Specify what to do
			if(action == 'create'){
				console.log('creating option');
				client_JSON_dataPush('create', 'options', data)
			}
			else if (action == 'update'){
				console.log('')
				client_JSON_dataPush('update', 'options', data)
			}
			else if (action == 'delete'){
				client_JSON_dataPush('delete', 'options', data)
			}
		}
		// Company and Ratio
		else if (reciever == 'company'){
			console.log('reciever == company');

			// Get company field data, wrap in Command, then POST
			if(action == 'create'){
				var company_name = document.getElementById('name').value;

				console.log('creating company with name: ' + company_name);


				var data = JSON.stringify(company_name);
				client_JSON_dataPush('create', 'company', data);
			}
			else if (action == 'update'){
				var comp_id = document.getElementById('objSelector').value;
				var company_name = document.getElementById('name').value;
				var companyUpdateWrapper = getObjTemplate('companyUpdateWrapper');

				console.log('updating company #' + comp_id + 'with name: ' + company_name);

				companyUpdateWrapper['comp_id'] = comp_id;
				companyUpdateWrapper['comp_name'] = company_name;

				data = JSON.stringify(companyUpdateWrapper);

				client_JSON_dataPush('update', 'company', data);

			}
			else if (action == 'delete'){
				var comp_id = document.getElementById('objSelector').value;

				console.log('deleting company #' + comp_id);

				data = JSON.stringify(comp_id);

				client_JSON_dataPush('delete', 'company', data);
			}

		}
		else if (reciever == 'ratio'){
			// Get ratio field data, wrap in Command, then POST
			

			if(action == 'update'){
					var ratio = getObjTemplate('ratio');
					var ratio_name = document.getElementById('name').value;
					var ratio_value = document.getElementById('value').value;
					var ratio_id = document.getElementById('objSelector').value;

					console.log('updating ratio #' + ratio_id + 'with name: ' + ratio_name + 'and value: ' + ratio_value);

					ratio['name'] = ratio_name;
					ratio['value'] = ratio_value;
					ratio['id'] = ratio_id;

					data = JSON.stringify(ratio);

					console.log('* updating ratio *');
					client_JSON_dataPush('update', 'ratio', data);	
			}
			else if(action == 'create'){
					var ratio = getObjTemplate('ratio');
					var ratio_name = document.getElementById('name').value;
					var ratio_value = document.getElementById('value').value;

					console.log('ratio name: ' + ratio_name + ' and value: ' + ratio_value);

					ratio['name'] = ratio_name;
					ratio['value'] = ratio_value;

					data = JSON.stringify(ratio);

					console.log('* creating ratio *');
					client_JSON_dataPush('create', 'ratio', data);
			}

		}
			
		
		
	}

	function exposeObject(obj){
		console.log('------obj------');
		for(var prop in obj){
			console.log(prop + ':' + obj.prop);
		}
		console.log('------obj------');
	}


	//Object factory
	function getObjTemplate(obj){
		switch(obj){

			case 'ratioObj':
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
				break;
		
			case 'relationPackage':

				var relationPackage = {
						comp_id: null,
						optionList: [],
						deleteList:[]
				}

				return relationPackage;
			case 'option':
				var option = {
					ratio_id:null,
					x_small:null,
					y_small:null,
					x_med:null,
					y_med:null,
					x_large:null,
					y_large:null,
					comp_id:null
				}
				return option;
			case 'deleteCmd':
				var deleteCmd = {
					ratio_id:null,
					comp_id:null
				}
				return deleteCmd;
			case 'companyUpdateWrapper':
				var wrapper = {
					comp_id:null,
					comp_name:null
				}
				return wrapper;

			case 'ratio':
				var ratio = {
					id:null,
					name:null,
					value:null
				}
				return ratio;
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
			<div class='checkedOptionDiv' id='{{this.div_id}}' data-ratioid='{{this.ratio_id}}' >
					<input type='checkbox' value='{{this.ratio_id}}' class='checkedOption' id="{{this.ratio_checkboxID}}"  name='{{this.ratio_name}}' checked>{{this.ratio_name}}<br>
				{{#each option}}
					<p>Small</p>
					<input type='text' id='x_small' value={{x_small}}>
				    <input type='text' id='y_small' value={{y_small}}> <br>
				    <br>
				    <p>Med</p>
				    <input type='text' id='x_med' value={{x_med}}>
				    <input type='text' id='y_med' value={{y_med}}> <br>
				    <br>
				    <p>Large</p>
				    <input type='text' id='x_large' value={{x_large}}>
				    <input type='text' id='y_large' value={{y_large}}> <br>
				{{/each}}
			</div>

		{{/each}}
		{{#each uncheckedRatios}}
			<div class='uncheckedOptionDiv' id='{{this.div_id}}' data-ratioid='{{this.ratio_id}}'>
				<input type='checkbox' value='{{this.ratio_id}}' class='uncheckedOption' id='{{this.ratio_checkboxID}}' name='{{this.ratio_name}}' >{{this.ratio_name}}<br>

			</div>
		{{/each}}

	</form>



</script>

<script id='option_update_template' type=<'text/x-handlebars-template'>
	<div class='checkedOptionDiv' id='{{this.div_id}}' data-ratioid='{{this.ratio_id}}' >
		<input type='checkbox' value='{{this.ratio_id}}' class='checkedOption' id="{{this.ratio_checkboxID}}"  name='{{this.ratio_name}}' checked>{{this.ratio_name}}<br>
		{{#each option}}
			<p>Small</p>
			<input type='text' id='x_small' value={{x_small}}>
		    <input type='text' id='y_small' value={{y_small}}> <br>
		    <br>
		    <p>Med</p>
		    <input type='text' id='x_med' value={{x_med}}>
		    <input type='text' id='y_med' value={{y_med}}> <br>
		    <br>
		    <p>Large</p>
		    <input type='text' id='x_large' value={{x_large}}>
		    <input type='text' id='y_large' value={{y_large}}> <br>
		{{/each}}
	</div>
</script>


<head>

</head>

<body>

	<a href='<?echo base_url()?>'>Back to Start Page</a>

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
