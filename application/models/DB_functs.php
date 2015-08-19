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

	public function getRatioList(){
		$query = $this->db->query("SELECT * FROM `Ratios` WHERE `id` <> 1");

		$ratioList = [];
		$ratio = $this->Object_Templates->getRatioObject();
		foreach ($query->result() as $row){
			log_message('debug', 'ratio: '.$row->name);

			$ratio['name'] = $row->name;
			$ratio['value'] = $row->value;
			$ratio['fk_comp'] = $row->fk_comp;

			$ratioList[] = $ratio;

		}
		$response = $this->Object_Templates->getResponseObject();
		$response['header'] = 'ratioList';
		$response['data'] = $ratioList;

		return $response; 
	}

	public function filterCompanies(){

	}

}