<!DOCTYPE html>
<html lang="en">

<script>
	
	function loadTemplates(){

		//Create 


	}

	//Only load create/edit functions
	//String option: 'create' (or) 'delete'
	//String type: name of the object to create or edit
	function loadCE_Template(option, type){
		tmpl = document.getElementById('userAction')

		//Create title
		title = tmpl.content.getElementById('title');
		title.innerHTML = option + type;

		//Populate field form 
		fieldForm = tmpl.content.getElementById('fieldForm');
		if(type == 'company'){
			loadCompanyFields(tmpl);
		}else if(type = 'ratio'){
			 loadRatioFields(tmpl);
		}else if type ='relation'){
			 loadRelationOptions(tmpl);
		}else{
			msg='loadCE_Template::type=' + type + '\ntype not recognized.'
			alert('ERROR\n' + msg)
		}




	}

	function loadCompanyFields(option, tmpl){
		companyFields = tmpl.content.getElementById('fieldForm');
		//Pull empty company from DB and create fields

		//If edit populate with existing DB entry
		if(option == 'edit'){

		}
	}

	function loadRatioFields(option, tmpl){
		ratioFields = tmpl.content.getElementById('ratioFields');
		//Pull empty field from DB and create fields

		if(option == 'edit'){


		}

	}

	//Load company and ratio dropdowns
	function loadRelationOptions(tmpl){
		cList = tmpl.content.getElementById('companyList');
		//Pull all companies from DB and add to cList

		rList = tmpl.content.getElementById('ratioList');
		//Pull all ratios from DB and add to rList

	}


	//Only load delete functions
	/*
	String option: delete
	String type: name of the object to delete
	*/
	function loadDeleteTemplate(option, type){
		template = document.getElementById('userAction')

		//Create Title
		title = template.content.getElementById('title');
		title.innerHTML = option + type

	}

	function actionSubmit(){


	}

	function deleteSubmit(){

	}

</script>

<head>

</head>

<body>

	<select id='action' onchange='loadTempates()'>
		<option value='create'>Create</option>
		<option value='edit'>Edit</option>
		<option value='delete'>Delete</option>
	</select>


	<select id='reciever' onchange='loadTempates()'>
		<option value'company'>Company</option>
		<option value'ratio'>Ratio</option>
		<option value'relation'>Relation</option>
	</select>


	<template id='userAction'>

		<p id='title'> </p>

		<!--Where user creates or edits fields (CE) -->
		<form id='fieldForm' hidden>

		</form>


		<!--User can delete an object from the list-->
		<select id='companyList' hidden>

		</select>
		<select id='ratioList' multiple hidden>

		</select>

		<button onclick='' hidden> </button>

	</template>


</body>