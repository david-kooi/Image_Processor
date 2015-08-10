<?

class image_upload_functs extends CI_Model{

	
	public function __construct()
   {
        parent::__construct();

        log_message('info', 'In: image_upload_functs');


   }

	function uploadImages(){
		log_message('info','In: uploadImage');

		//Stores file names
		$_PHOTOS = array();

		$numPhotos = count($_FILES['photos']['name']);
		log_message('debug','image_functs: uploadImages: numPhotos: '.$numPhotos);

		/* Data format of $_FILES */

		// Process each photo
		for($i = 0; $i < $numPhotos; $i++){
		
			$photo = array('name'=>'',
						   'type'=>'',
						   'tmp_name'=>'',
						   'error'=>'',
						   'size'=>''
						   );

			/* 
				- Prepare photo for upload
				- Individual photos are extracted from $_FILES and a singular $photo is made
			*/      
			foreach ($photo as $key=>$value) {
		
				if(isset($_FILES['photos'][$key])){
					$photo[$key] = $_FILES['photos'][$key][$i];
				}
			}

			/*
				- Add $photo to $_FILES. 
				- do_upload() calls $_FILES['userfile']. 
			*/
			$_FILES['userfile'] = $photo;
		
			$fileName = $this->ciImageUpload();


			//Keep track of fileNames
			$_PHOTOS[$i] = $fileName;
		}	

		return $_PHOTOS;

	}

	function ciImageUpload(){
			log_message('info', 'In: ciImageUpload()');

			if(!file_exists(TMP_FLD)){
				log_message('debug', TMP_FLD.' Created');
				mkdir(TMP_FLD);
			}

			//CI_Upload preferences
			$config['upload_path'] = TMP_FLD;
			$config['allowed_types'] = 'gif|jpg|jpeg|png';
			$config['max_size']	= '200000';
			$config['max_width']  = '20000';
			$config['max_height']  = '20000';


			$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload()){
				$error = array('error' => $this->upload->display_errors());
				log_message('info', 'Image upload unsuccessful');
				return $error['error'];
			}
			else{ //Return image data to client side (TODO: Return data array)
				$data = $this->upload->data();
				log_message('info', 'Image upload '.$data['file_name'].' successful');
				return $data['file_name'];

			}
	}	


}