<!DOCTYPE html>


    <script src="<?base_url()?>js/bootswatch.js"></script>
    <script src='<?php echo base_url();?>js/jquery-1.11.2.js'></script>
    <script src='<?php echo base_url();?>js/handlebars-v3.0.3.js'></script>


<script>
  
   $(document).ready(function(){

      //Load company list when document is ready
      sendAjax('companyList');


    });

  function uploadMultiple(){

        /* Configuration */
        var formId = 'uploadForm';
        var inputId = 'userFiles';
        var postURL =  "<?echo base_url()?>master_controller/uploadImages";

        console.log('In uploadMultiple()');
        console.log('Posting to:' + postURL);

        /* Document Elements */
        var form = document.getElementById(formId);
        var fileSelect = document.getElementById(inputId);
      
        var files = fileSelect.files;

        var formData = new FormData();

        if (files.length == 0) {
          console.log('No files selected. Aborting upload.');
          return -1;
        };

        console.log('Adding files to formData:' + files);
        console.log('Num Files: ' + files.length);

        // Loop through each of the selected files.
        for (var i = 0; i < files.length; i++) {
          var file = files[i];

          console.log('File: ' + file);
          // Check the file type.
          if (!file.type.match('image.*')) {
            continue;
          }

          // Add the file to the request.
          formData.append('photos[]', file, file.name);
        }


        // Set up the request.
        var xhr = new XMLHttpRequest();


        // Set up a handler for when the request finishes.
        xhr.onreadystatechange = function(){
            if (xhr.readyState < 4)              // while waiting response from server
              console.log('Uploading Files...');
            else if (xhr.readyState === 4) { 
              console.log('Response Recieved') 
              if (xhr.status >= 200 && xhr.status < 399)  // http status between 200 to 299 are all successful
                
                console.log('Images Uploaded: ' + xhr.responseText);

                //Create a list of image names
                imgs = JSON.parse(xhr.responseText);

                console.log('Imgz: ' + imgs);

                //Populate Upload List
                updateUploadList(imgs);
              }else{
                console.log('Did not recieve responseText...xhr status: ' + xhr.status);
              }
        };
        

        xhr.open('POST', postURL, true);
        xhr.send(formData);
      }

  function updateUploadList(imageNames){
    console.log('Updating upload list');

    var uploadList = document.getElementById('uploadList');

    //TODO: Create a thumbnail for each file  
    for(var i = 0; i < imageNames.length; i++){
      console.log("Updating: "+imageNames[i]);

      var imgName = imageNames[i];
      var dataWrap = {imgName:imgName};

      var source = $('#imageNotification_template').html();
      var template = Handlebars.compile(source);
      var html = template(dataWrap);

      $('#imgList').append(html);


      // //Attach to uploadList
      // uploadList.appendChild(imgName);
      // console.log("Child Appended");

      }
      
      $('#uploadedFilesHeader').attr('hidden', false);
  }

  function loadCompanyDropdown(companyList){
    console.log('loading company dropdown');

    for(company of companyList){
      console.log('company: ' + company['name']);

      // Create the new option
      var opt = document.createElement('option');
      opt.innerHTML = company['name'];
      opt.setAttribute('value', company['id']);

      document.getElementById('companySelect').appendChild(opt);

    }

    requestRatioDropdown();

  }

  function loadRatioDropdown(data){
    console.log('Loading ratio dropdown');

      for(option of data['optionsList']){
        for(ratio of data['ratioList']){
          //Skip ratio if it is not in the companies options
          if(ratio['id'] != option['ratio_id']){
            continue;
          }
          console.log('ratio: ' + ratio['name']);

          var opt = document.createElement('option');
          opt.innerHTML = ratio['name'];
          opt.value = option['id'];

          document.getElementById('optionSelect').appendChild(opt);

        }
      }
  }

  //Ratiolist is dependent on the company selected
  function requestRatioDropdown(){
    clearCurrentRatioDropdown();

    var comp_id = document.getElementById('companySelect').value;
    console.log('Requesting options for company #' + comp_id);
    // Get ratio list from DB
    sendAjax('companyOptions_'+comp_id);
  }

  function clearCurrentRatioDropdown(){
    console.log('clearing dropdown');
    var kids = $('#optionSelect').children().remove();
  }

  function ajaxResponseHandler(data){
    console.log('-------ajaxResponseHandler-------');
    console.log('data: ' + data);

    dataObj = JSON.parse(data);


    switch(dataObj['header']){
      case 'companyList':
        console.log('companyList recieved');

        loadCompanyDropdown(dataObj['data']);

        break;

      case 'companyOptions':
        console.log('companyOptions recieved');
        loadRatioDropdown(dataObj['data']);
        break;
      case 'processImages':
        console.log('processImages result recieved');
        evaluateImageProcess(dataObj['data']);
        break;

    }
  }

    function evaluateImageProcess(status){
      if(status){
        console.log('Image Processing Sucess');
      }else{
        console.log('Image Processing Failed. Check php logs.')
      }
    }

    function sendAjax(request){
      console.log('--- processObj_JSON_list ---');
      console.log('request: ' + request);

      $.ajax({
        url:"<?echo base_url()?>master_controller/clientRequest",
        data:{requestHeader: request},
        method:'POST'
      }).done(function(data){
        ajaxResponseHandler(data);
      });
  }

  function processImages(){
    console.log('processImages');

    var option_id = document.getElementById('optionSelect').value;

    sendAjax('processImages_' +option_id);
  }

</script>

</script>


<script id='imageNotification_template' type='text/x-handlebars-template'>
  <a href="#" class="list-group-item list-group-item-success"><span class="badge alert-success pull-right">Uploaded</span>{{imgName}}</a>
</script>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Image Processor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="<?echo base_url()?>css/bootstrap.css" media="screen">
    <link rel="stylesheet" href="<?echo base_url()?>css/bootswatch.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="../bower_components/html5shiv/dist/html5shiv.js"></script>
      <script src="../bower_components/respond/dest/respond.min.js"></script>
    <![endif]-->
    <script>

     var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-23019901-1']);
      _gaq.push(['_setDomainName', "bootswatch.com"]);
        _gaq.push(['_setAllowLinker', true]);
      _gaq.push(['_trackPageview']);

     (function() {
       var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
       ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
       var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
     })();

    </script>
    
    
  </head>
  <body>
    <div class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a href="../" class="navbar-brand">Image Server</a>
          <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="navbar-collapse collapse" id="navbar-main">
          <ul class="nav navbar-nav">
        
          	<li>
              <a href="<?echo base_url()?>">Home</a>
            </li>
            
             <li>
              <a href="<?echo base_url()?>master_controller/toSearch">Search and Rename</a>
            </li>
            <li>
              <a href="<?echo base_url()?>master_controller/toNewsFeed">NewsFeed</a>
            </li>
            
          </ul>

          <ul id="navBarRight" class="nav navbar-nav navbar-right">
            <li><a id="LoginLink" href="<?echo base_url()?>index.php/processHandler/toLogin" target="_blank">Sign in</a></li>
            <li><a href="<?echo base_url()?>index.php/processHandler/toRegister" target="_blank">Register</a></li>
            <li><a href="<?echo base_url()?>index.php/processHandler/logout" target="_blank">Logout</a></li>
          </ul>

        </div>
      </div>
    </div>


<div class="container">

    <div class="page-header" id="banner">
      
        <div class="row">
    		<div class="col-lg-6 col-lg-offset-3 text-center top30">
             	 <h2>Upload Files Here</h2>
           </div>
        </div> 
	</div>
        
<div class="row"> 
	<div class="col-lg-6 col-lg-offset-3">  
          <!--<div class="panel panel-default"
        	<div class="panel-body"> -->

          <!-- Standard Form -->
          <form method="post" enctype="multipart/form-data" id="uploadForm">
            <div class="form-inline">
              <div class="form-group">
                <input type="file" name="files[]" id="userFiles" multiple>
              </div>
              <submit class="btn btn-sm btn-default" onclick="uploadMultiple()">Upload Files</button>
            </div>
          </form>

          <!-- Upload Finished -->
          <div class="js-upload-finished">
            <h3 id='uploadedFilesHeader' hidden>Uploaded files</h3>
            <div class="list-group" id="imgList">
             
            </div>
          </div>
       
    </div><!--col-12-->   
</div><!--upload-->          
          
        
     
<!--Options-->
    <!-- <div class="container"> -->
           
  <div style="background-color: #F4F4F4; height: 310px;">         
            <div class="row">
                <div class="col-xs-6 col-xs-offset-3 text-center">
                    <h1>Select Options</h1>
                    <hr class="section">
                </div>
            </div>
            
            <div class="row top17">
            	<div class="col-xs-3 col-xs-offset-3 text-center">
            		<img src="<?echo base_url()?>img/company.png">
            	</div>
            	
            	<div class="col-xs-3 text-center">
            		<img src="<?echo base_url()?>img/dimensions.png">
            	</div>
            	
            </div>
            
            <div class="row text-center">
                <div class="col-xs-3 col-xs-offset-3">                  	                                         
  					
  					<select id="companySelect" class="styled-select" onchange="requestRatioDropdown()">

				
	   				</select>
                  	
                </div>
                <div class="col-xs-3">

             <select id="optionSelect" class="styled-select">
					   

             </select>
            	</div>
                       	
		</div>	
                    
</div>
		<div class="row top15">
			<div class="col-lg-6 col-lg-offset-3 text-center">
					<submit class="btn btn-md btn-success" onclick="processImages()">Process Files</submit>
          <br>
			</div>  
      <a href="<?base_url()?>index.php/master_controller/toAdminPage">Admin Page</a>  
   	
    </div>
            
   
</div> <!-- container-->

    

      <footer>
        
      </footer>



  </body>
</html>
