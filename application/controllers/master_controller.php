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
		$data = $this->input->post('data');
		log_message('debug','master_controller: clientRequest: Request Command: '.$data);
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