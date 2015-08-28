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


		return $companyList;
	}

	public function getRatioList(){
		$query = $this->db->query("SELECT * FROM `Ratios` WHERE `id` <> 1");

		$ratioList = [];
		$ratio = $this->Object_Templates->getRatioObject();
		foreach ($query->result() as $row){
			log_message('debug', 'ratio: '.$row->name);

			$ratio['id'] = $row->id;
			$ratio['name'] = $row->name;
			$ratio['value'] = $row->value;
			$ratio['fk_comp'] = $row->fk_comp;

			$ratioList[] = $ratio;

		}


		return $ratioList; 
	}

	//Get all ratios that a company is tied to.
	/*
		Get full list and mark ratios that are tied to the company
	*/
	public function getCompanyRatioList($objId){
		$fullRatioList = $this->getRatioList();

		foreach($fullRatioList as $ratio){
			if($ratio['fk_comp'] == $objId){
				$ratio['checked'] = True;
			}
		}
		return $fullRatioList;

	}

	public function getCompanyById($objId){
		$query = $this->db->query("SELECT * FROM `Companies` WHERE `id` = ".$objId);
		$data = $query->result()[0];

		$company = $this->Object_Templates->getCompanyObject();
		$company['id'] = $data->id;
		$company['name'] = $data->name;

		return $company;
	}

	public function getRatioById($objId){
		$query = $this->db->query("SELECT * FROM `Ratios` WHERE `id` =".$objId);
		$data = $query->result()[0];

		$ratio = $this->Object_Templates->getRatioObject();
		
		$ratio['id'] = $data->id;
		$ratio['name'] = $data->name;
		$ratio['value'] = $data->value;
		$ratio['fk_comp'] = $data->fk_comp;

		return $ratio;
	}

	public function getCompanyOption($objId){
		$query = $this->db->query("SELECT * FROM `Options` WHERE `comp_id` =".$objId);

		$optionsList = array();
		$option = $this->Object_Templates->getOptionObject();
		foreach ($query->result() as $row){
			$option['id']=$row->id;
            $option['ratio_id']=$row->ratio_id;
            $option['comp_id']=$row->comp_id;
            $option['x_small']=$row->x_small;
            $option['y_small']=$row->y_small;
            $option['x_med']=$row->x_med;
            $option['y_med']=$row->y_med;
            $option['x_large']=$row->x_large;
            $option['y_large']=$row->y_large;
			
			$optionsList[] = $option;
		}
		return $optionsList;
	}

	public function deleteOption($compId, $ratioId){
		$result = $this->db->query("DELETE FROM Options WHERE comp_id='$compId' AND ratio_id='$ratioId' ");

		return $result;
	}


}
