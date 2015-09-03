<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class master_controller extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct()
       {
            parent::__construct();

            $this->load->model('image_upload_functs');
            $this->load->model('DB_functs');
            $this->load->model('image_processing');

		$sessionData = $this->session->all_userdata();

       }

	public function index()
	{
		log_message('info', 'master_controller: In index()');


		//Load Start Page
		$this->load->view('main_page');
		log_message('info', 'master_controller: start_page loaded');

		//Load Start Data (Company and Ratio Lists)

	}



	public function clientRequest(){
		$requestHeader = $this->input->post('requestHeader');
		log_message('debug','master_controller: clientRequest: '.$requestHeader);

		//Get objId if that was passed with the request
		$objId = null;
		$obj2Id = null;
		$split = explode('_', $requestHeader);
		if(count($split) == 2){
			log_message('info', 'clientRequest: Request has 1 ID');
			$requestHeader = $split[0];
			$objId = $split[1];

			log_message('info', 'new request: '.$requestHeader);
		}
		else if(count($split) == 3){
			log_message('info', 'clientRequest: Request has 2 IDs');
			$requestHeader = $split[0];
			$objId = $split[1];
			$obj2Id = $split[2];

		}else{
			log_message('info', 'clientRequest: No IDs');
		}
		// if(count($split) == 2){
		// 	$objId = $split[1];
		// 	log_message('info', 'request send with objId: '.$objId);
		// }

		log_message('info', 'objId: '.$objId);
		log_message('info', 'objId2: '.$obj2Id);


		switch($requestHeader){
			case 'companyList':
				//Get companyList and wrap in a response
				$companyList = $this->DB_functs->getCompanyList();
				$response = $this->generateResponse($requestHeader, $companyList);

				echo json_encode($response);
				break;
			case 'ratioList':
				//Get ratioList and wrap in a response
				$ratioList = $this->DB_functs->getRatioList();
				$response = $this->generateResponse($requestHeader, $ratioList);

				echo json_encode($response);
				break;
			case 'emptyCompany':
				//Get company object and wrap in a response
				$emptyCompany = $this->Object_Templates->getCompanyObject();
				$response = $this->generateResponse($requestHeader, $emptyCompany);

				echo json_encode($response);
				break;
			case 'emptyRatio':
				//Get ratio object and wrap in a response
				$emptyRatio = $this->Object_Templates->getRatioObject();
				$response = $this->generateResponse($requestHeader, $emptyRatio);

				echo json_encode($response);
				break;
			case 'companyFullRatioList':
				$ratioList = $this->DB_functs->getCompanyRatioList($objId);
				$response = $this->generateResponse($requestHeader, $ratioList);

				echo json_encode($response);
				break;
			case 'updateCompany':
				$company = $this->DB_functs->getCompanyById($objId);
				$response = $this->generateResponse($requestHeader, $company);

				echo json_encode($response);
				break;
			case 'updateRatio':
				$ratio = $this->DB_functs->getRatioById($objId);
				$response = $this->generateResponse($requestHeader, $ratio);

				echo json_encode($response);

				break;
			case 'companyOptions':
				$optionsList = $this->DB_functs->getCompanyOptions($objId);
				$ratioList = $this->DB_functs->getRatioList();
				$isEmpty = false; // Are these options and ratios empty?
				//option response must have options and ratio list
				$optionBundle = $this->Object_Templates->getOptionResponse($optionsList, $ratioList, $isEmpty);

				$response = $this->generateResponse($requestHeader, $optionBundle);

				echo json_encode($response);
				break;
			case 'deleteOption':
				$result = $this->DB_functs->deleteOption($objId, $obj2Id);
				$response = $this->generateResponse($requestHeader, $result);

				log_message('debug', 'deleteResponse: '.$response['data']);
				log_message('debug', 'deleteResponse: '.$response['header']);

				echo json_encode($response);
				break;

			case 'emptyOptionWithRatio':
				log_message('info', 'clientRequest: getting emptyOption');
				log_message('info', 'clientRequest: for ratio #'.$objId);
				log_message('info', 'clientRequest: with company # '.$obj2Id);

				$option = $this->Object_Templates->getOptionObject();
				$optionList = $this->DB_functs->getCompanyOptions($obj2Id);


				// Add empty option
				// Option has ratio specified
				$option['ratio_id'] = $objId;
				$optionList[] = $option;

				$ratioList = [];
				$ratioList = $this->DB_functs->getRatioList();

				//Attach empty tag to the ratio to be created
				//$ratioList = $this->attachEmptyTag($ratioList, $objId);


				//option response must have options and ratio list
				$optionBundle = $this->Object_Templates->getOptionResponse($optionList, $ratioList);


				$response = $this->generateResponse($requestHeader, $optionBundle);

				echo json_encode($response);
				break;

				case 'processImages':
					log_message("info", 'requestHeader: '.$requestHeader);

					$result = $this->image_processing->processFiles($objId);
					
					$response = $this->generateResponse($requestHeader, $result);

					echo json_encode($response);
					break;

			default:
				log_message('error','ERROR: master_controller: clientRequest:'.$requestHeader.' not recognized');
		}
	}	

	public function clientPush(){
		$header = $this->input->post('pushHeader');
		$process = $this->input->post('process');
		$data = $this->input->post('data');

		log_message('info', 'clientPush: '.$header);
		log_message('info', 'data: '.$data);


		switch($header){

			case 'options':
				log_message('info', 'clientPush: options recieved');
				$this->DB_functs->processOptions($process, $data);
				break; 
			case 'company':
				log_message('info', 'clientPush: company recieved');
				$this->DB_functs->processCompany($process, $data);
				break;
			case 'ratio':
				log_message('info', 'clientPush: ratio recieved');
				$this->DB_functs->processRatio($process, $data);
				break;
		}

	}

	public function attachEmptyTag($ratioList, $tagId){
		$newList = [];
		foreach ($ratioList as $ratio) {
			log_message('debug', 'ratio: ' + $ratio['id']);

			if($tagId == $ratio['id']){
				log_message('debug', 'tag match');
				$ratio['emptyOption'] = True;
			}

			$newList[] = $ratio;

		}

		log_message('debug', 'newList Length: '.count($newList));
		return $newList;
	}

	public function generateResponse($requestHeader, $data){
		$response = $this->Object_Templates->getResponseObject();
		$response['header'] = $requestHeader;
		$response['data'] = $data;

		return $response;
	}

	public function uploadImages(){
		log_message('info', 'master_controller: In uploadImages()');

		$photoNames = $this->image_upload_functs->uploadImages();

		//Save number of photos uploaded
		$sessionData['numPhotos'] = count($photoNames);
		log_message('debug', 'master_controller: Num Photos Uploaded: '.$sessionData['numPhotos']);

		//Return photoNames to client side
		echo json_encode($photoNames);

		//TODO
		//$this->image_upload_functs->checkUpload($photos);
	}

	public function toAdminPage(){
		$this->load->view('admin_page');
		log_message('info', 'master_controller: admin_page loaded');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */