<!DOCTYPE html>
<html lang="en">

<script src='<?php echo base_url();?>js/jquery-1.11.2.js'></script>
<script src='<?php echo base_url();?>js/handlebars-v3.0.3.js'></script>

<script>
	function loadTemplates(){

		console.log('loadTemplates');

		var source = $("#create-edit-selector-template").html();
		var c_e_s_template = Handlebars.compile(source);
		var context = { title:'Is Title',
						objects:[
								{'id':10, 'name':'Name1'},
								{'id':11, 'name':'Name2'},
								{'id':12, 'name':'Name3'}
								]
					  }
		var html = c_e_s_template(context);
		console.log(html);

		//Append to template div
		$('.user_action').append(html);

	}


</script>


<script id="create-edit-selector-template" type="text/x-handlebars-template">
	<title>{{title}}</title>

	<select id=objSelector>
	{{#each objects}}
		<option value={{this.id}}>{{this.name}}</option>
	{{/each}}
	</select>

</script>

<script id='create-edit-field-tempalte' type='text/x-handlebars-template'>
	<form id='objFields'>
		{{#each obj.fields}}

	</form>

</script>

<head>

</head>

<body>

	<div id='selections'>
		<select id='action' onchange='loadTemplates()'>
			<option value='create'>Create</option>
			<option value='edit'>Edit</option>
			<option value='delete'>Delete</option>
		</select>


		<select id='reciever' onchange='loadTemplates()'>
			<option value'company'>Company</option>
			<option value'ratio'>Ratio</option>
			<option value'relation'>Relation</option>
		</select>
	</div>

	<div class='user_action' id='user_action'>


	</div>

</body>