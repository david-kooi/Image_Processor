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


		$sessionData = $this->session->all_userdata();

       }

	public function index()
	{
		log_message('info', 'master_controller: In index()');


		//Load Start Page
		$this->load->view('start_page');
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
			log_message('info', 'objId: '.$objId);
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
			case 'companyRatioList':
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
				$optionsList = $this->DB_functs->getCompanyOption($objId);
				$ratioList = $this->DB_functs->getRatioList($objId);

				$optionBundle = array('optionsList'=>$optionsList,
								      'ratioList'=>$ratioList);

				$response = $this->generateResponse($requestHeader, $optionBundle);

				echo json_encode($response);
			case 'deleteOption':
				//$result = $this->DB_functs->deleteOption($objId, $obj2Id);
				break;
			default:
				log_message('error','ERROR: master_controller: clientRequest:'.$requestHeader.' not recognized');
		}
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