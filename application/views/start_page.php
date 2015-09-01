<!DOCTYPE html>
<html lang="en">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
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

      var imgName = document.createElement('p');
      imgName.innerHTML = imageNames[i];
      
      //Attach to uploadList
      uploadList.appendChild(imgName);
      console.log("Child Appended");

      }
      
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


<head>

	<p> Image Server <p>

  <a href='<?base_url()?>master_controller/toAdminPage'>Administration</a>

	<form method="post" enctype="multipart/form-data" id="uploadForm">
	    <input type="file" name="files[]" id="userFiles" multiple>
	    <submit class="btn btn-sm btn-default" onclick="uploadMultiple()">Upload Files</submit>
  </form>

  <br><br>

  <div id='uploadList'>

  </div>

  <div id='selectionArea'>

    <select id='companySelect' onchange='requestRatioDropdown()'>

    </select>

    <select id='optionSelect'>

    </select>

    <button onclick='processImages()'>Process</button>

  </div> 




</head>