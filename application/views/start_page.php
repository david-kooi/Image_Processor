<!DOCTYPE html>
<html lang="en">

<script>

function uploadMultiple(){

	<? log_message('info', 'start_page: in uploadMultiple()'); ?>

      /* Configuration */
      var formId = 'uploadForm';
      var inputId = 'userFiles';
      var postURL =  "master_controller/uploadImages";

      console.log('In uploadMultiple()');
      console.log('Posting to:' + postURL);

      /* Document Elements */
      var form = document.getElementById(formId);
      var fileSelect = document.getElementById(inputId);
    
      var files = fileSelect.files;

      var formData = new FormData();

      console.log('Adding files to formData:');
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
      xhr.open('POST', postURL, true);

      // Set up a handler for when the request finishes.
      xhr.onload = function () {
        if (xhr.status === 200) {         

          <? log_message('debug', 'start_page: Upload Sucess'); ?>
          console.log("Image Upload: Success");
          fileNames = xhr.responseText;
          console.log(xhr.responseText);

        } else {
          <? log_message('debug', 'start_page: Upload Error'); ?>
          alert('An error occurred. Check Console Log');
        }
      

        //Create an array of file names
       // var imgs = JSON.parse(fileNames);

        //Create imgs on page
        //updateThumbList(imgs);


      };

      xhr.send(formData);
      //updateUploadList(files);


    }
</script>

<head>

	<p> Image Server <p>

	<form method="post" enctype="multipart/form-data" id="uploadForm">
	    <input type="file" name="files[]" id="userFiles" multiple>
	    <button class="btn btn-sm btn-default" onclick="uploadMultiple()">Upload Files</button>
    </form>


</head>