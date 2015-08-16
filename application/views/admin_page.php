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
		getCompany_JSON_list();

	});

	

	/*
		Handles generation of section A
	*/
	function sectionA_Handler(){
		console.log('----SectionA_Handler-----')

		var action = document.getElementById('actionDropDown').value;
		var reciever = document.getElementById('recieverDropDown').value;

		console.log('action: ' + action);
		console.log('reciever: ' + reciever);

		// reciever case
		// Load company list so user can select what company to apply relation to
		if (reciever == 'relation'){
			console.log('relation case');


			companyList = getCompany_JSON_list();
			LOAD_select_comp_or_ratio_template(companyList);
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
				//objList = getCompany_JSON_list();
			}
			else if(reciever == 'ratio'){
				//objList = getRatio_JSON_list();
			}
			LOAD_select_comp_or_ratio_template(objList);
		}
	
	}

	/*
		Data Requests
	*/
	function getCompany_JSON_list(){
		$.ajax({
			url:"<?echo base_url()?>master_controller/clientRequest",
			data:{data:'I am data'},
			method:'POST'
		}).done(function(data){
			console.log('Recieved Data: ' + data);
		});
	}

	function sectionB_create_with_empty_fields(){
		console.log('Creating fields as EMPTY');

		var reciever = document.getElementById('recieverDropDown').value;

		fieldData = null;
		//Get empty fields
		if (reciever == 'company'){
			//Get empty company fields
			fieldData = get_empty_field_data(reciever);
		}
		else if (reciever == 'ratio'){
			//Get empty ratio fields
			fieldData = get_empty_field_data(reciever);
		}
		LOAD_input_fields(fieldData);
	}
	/*
		Handles fields
	*/
	function sectionB_Handler(){
		var action = document.getElementById('actionDropDown').value;
		var reciever = document.getElementById('recieverDropDown').value;
		if ($('objSelector').length == 0){
			console.log('CODE ERROR: sectionB_Handler must be called ONLY by objSelector <select>')
			return;
		}
		var obj = document.getElementById('objSelector').value;

		// reciever case:
		// Load reciever template
		if (reciever == 'relation'){
			relationData = getRelationData(obj);
			LOAD_relation_template(relationData);
		}
		// delete Case:
		// - Nothing to be loaded...delete handled by adminSubmit
		else if (action == 'delete'){
			//Do Nothing
		}
		// update case:
		else if(action == 'update'){
			fieldData = get_populated_field_data(action, reciever, obj);
			LOAD_input_fields(fieldData);
		}

	}

	function get_empty_field_data(action, reciever){
		console.log('Getting empty_field_data');
		var fieldData = null;

		if (reciever == 'company'){
			//Get blank fields
			//fieldData == 
		}
		else if (reciever == 'ratio'){
			//Get blank fields
			//fieldData == 
		}
		return fieldData;
	}

	// Get field data of company or ratio
	// If: action == 'create' return blank fields
	// Else: return fields populated with database fields
	function get_populated_field_data(action, reciever, obj){
		console.log('Getting populated_field_data');

		var fieldData = null;

		if (reciever == 'company'){
			//fieldData = 
		}
		else if (reciever == 'ratio'){
			//fieldData == 
		}

		return fieldData;
	}	

	function getRelationData(company){
		console.log('Getting relationData');

		//Get data from server side

		return null;

	}

	/*
		Loads dropdown for create/edit companies and ratios
	*/
	function LOAD_select_comp_or_ratio_template(data){
		console.log('Loading objSelector');

		var source = $("#select_comp_or_ratio_template").html();
		var s_c_r_template = Handlebars.compile(source);

		var html = c_e_s_template(data);

		//Append to template div
		$('.sectionA').append(html);

	}

	function LOAD_input_fields(data){
		console.log('LOADING input_fields');

		// adminSubmit now valid
		$('#adminSubmit').fadeIn();

		var source = $('#input_fields_template').html();
		var i_f_template = Handlebars.compile(source);

		var html = i_f_template(data);

		$('.sectionB').append(html);

	}

	function LOAD_relation_template(data){
		console.log('LOADING relation_template');

		// adminSubmit now valid
		$('#adminSubmit').fadeIn();

		var source = $('#relation_template').html();
		var r_template = Handlebars.compile(source);

		var html = r_template(data);
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

	<select id='objSelector' onclick='sectionB_Handler()'>
	{{#each objects}}
		<option value={{this.id}}>{{this.name}}</option>
	{{/each}}
	</select>

</script>

<script id="input_fields_template" type="text/x-handlebars-template">

	<form id='objFields'>
		{{#each fields}}
			<input type='text' name='{{this.name}}'>
		{{/each}}
	</form>

</script>

<script id='relation_template' type='text/x-handlebars-template'>


	<form id='relationCheckForm'>
		{{#each ratios}}
			<input type='checkbox' name={{this.name}}>
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