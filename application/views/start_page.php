<!DOCTYPE html>
<html lang="en">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script>

function uploadMultiple(){

      /* Configuration */
      var formId = 'uploadForm';
      var inputId = 'userFiles';
      var postURL =  "index.php/master_controller/uploadImages";

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

</script>

<head>

	<p> Image Server <p>

  <a href='<?base_url()?>index.php/master_controller/toAdminPage'>Administration</a>

	<form method="post" enctype="multipart/form-data" id="uploadForm">
	    <input type="file" name="files[]" id="userFiles" multiple>
	    <submit class="btn btn-sm btn-default" onclick="uploadMultiple()">Upload Files</submit>
  </form>


  <div id='uploadList'>

  </div>


</head>