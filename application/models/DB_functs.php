<?

class DB_functs extends CI_Model{

	public function __construct(){
		$this->load->model('Object_Templates');
	}

	public function getCompanyList(){
		log_message('info','DB_functs: getCompanylist');

		// The first entry is blank so skip it
		$query = $this->db->query("SELECT * FROM `Companies` WHERE `id` <> 1");

		//Create individual company objects
		$companyList = [];
		$company = $this->Object_Templates->getCompanyObject();
		foreach ($query->result() as $row){
			log_message('debug', 'Company: '.$row->name);

			$company['id'] = $row->id;
			$company['name'] = $row->name;

			$companyList[] = $company;
		}

		$response = $this->Object_Templates->getResponseObject();
		$response['header'] = 'companyList';
		$response['data'] = $companyList;

		return $response;
	}

	public function filterCompanies(){

	}

}