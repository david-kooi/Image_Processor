<?

class DB_functs extends CI_Model{

	public function __construct(){
		$this->load->model('Object_Templates')
	}

	public function getCompanyList(){
		$response = $this->Object_Templates->getResponseObject();

		$companies = 

		$response['header'] = 'companyList';
		$response['data'] = $companies;

		return $response;
	}



}