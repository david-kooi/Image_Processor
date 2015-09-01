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

	public function getCompanyOptions($objId){
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


	public function processOptions($process, $packedData){

		$unpackedData = json_decode($packedData, True);

		//log_message('debug',var_dump($unpackedData));

		$compId = $unpackedData['comp_id'];
		$optionList = $unpackedData['optionList'];
		$deleteList = $unpackedData['deleteList'];


		switch($process){

			case 'delete':
				// Delete unchecked options
				$this->deleteOptions($deleteList);
				break;

			case 'create':
				//Add checked options
				$this->insertOptions($optionList);
				break;

			case 'update':
				$this->updateOptions($optionList);
				break;	
		}
		
	}


	public function updateOptions($optionList){
		foreach ($optionList as $option) {
			$this->updateOption($option);
		}
	}

	public function updateOption($option){

		$ratio_id=$option['ratio_id'];
        $comp_id=$option['comp_id'];
        $x_small=$option['x_small'];
        $y_small=$option['y_small'];
        $x_med=$option['x_med'];
        $y_med=$option['y_med'];
        $x_large=$option['x_large'];
        $y_large=$option['y_large'];

        log_message('info', 'updating option...');
		log_message('info', 'ratio_id: '.$ratio_id);
		log_message('info', 'comp_id: '.$comp_id);

		$query = $this->db->query("UPDATE `Options` SET ratio_id='$ratio_id', 
												 		comp_id='$comp_id', 
												 		x_small='$x_small', 
												 		y_small='$y_small', 
												 		x_med='$x_med', 
												 		y_med='$y_med', 
												 		x_large='$x_large', 
												 		y_large='$y_large'
												 	WHERE `comp_id`=$comp_id AND `ratio_id`=$ratio_id" );
	}

	public function insertOptions($optionList){
		foreach ($optionList as $option) {
			$result = $this->checkExistanceOfOption($option);
			if(!$result){
				$this->insertOption($option);
			}
		}
	}

	public function checkExistanceOfOption($option){
		log_message('info', 'checking option existance');
		$compId = $option['comp_id'];
		$ratioId = $option['ratio_id'];

		$query = $this->db->query("SELECT * FROM `Options` WHERE `comp_id` = '$compId' AND `ratio_id` = '$ratioId'");

		$count = count($query->result());
		log_message('debug', 'query count:'.$count);

		if($count > 0){
			log_message('info', 'Option Exists. Will not insert.');
			return True;
		}else{
			log_message('info', 'Option does not exist. Will insert.');
			return False;
		}
	}

	public function insertOption($option){


        $ratio_id=$option['ratio_id'];
        $comp_id=$option['comp_id'];
        $x_small=$option['x_small'];
        $y_small=$option['y_small'];
        $x_med=$option['x_med'];
        $y_med=$option['y_med'];
        $x_large=$option['x_large'];
        $y_large=$option['y_large'];


		$this->db->query("INSERT INTO `Options` (`ratio_id`, 
												 `comp_id`, 
												 `x_small`, 
												 `y_small`, 
												 `x_med`, 
												 `y_med`, 
												 `x_large`, 
												 `y_large`) VALUES 
																  ('$ratio_id',
																  	'$comp_id',
																  	'$x_small',
																  	'$y_small',
																  	'$x_med',
																  	'$y_med',
																  	'$x_large',
																  	'$y_large')
																  	");
	}

	public function deleteOptions($deleteList){

		foreach($deleteList as $option){
			$compId = $option['comp_id'];
			$ratioId = $option['ratio_id'];

			log_message('info', 'deleting option with comp_id: '.$compId.'and ratio_id: '.$ratioId);
			$this->deleteOption($compId, $ratioId);
		}


	}

	public function processCompany($process, $packedData){
		$unpackedData = json_decode($packedData, True);

		switch($process){

			case 'create':
				$comp_name = $unpackedData;
				log_message('info', 'creating new company with name: '.$comp_name);

				$this->createCompany($comp_name);
				break;

			case 'update':
				$comp_name = $unpackedData['comp_name'];
				$comp_id = $unpackedData['comp_id'];

				log_message('info', 'updating company #'.$comp_id.'with name: ' + $comp_name);

				$this->updateCompany($comp_id, $comp_name);
				break;

			case 'delete':
				$comp_id = $unpackedData['comp_id'];

				log_message('info', 'deleting company #'.$comp_id);
				$this->deleteCompany($comp_id);
				break;
		}
	}

	public function processRatio($process, $packedData){

		$unpackedData = json_decode($packedData, True);
		$ratio_name = $unpackedData['name'];
		$ratio_value = $unpackedData['value'];
		$ratio_id = $unpackedData['id'];

		switch($process){	

			case 'create':
				log_message('info', 'processRatio: create ratio');
				$this->createRatio($ratio_name, $ratio_value);
				break;

			case 'update':
				log_message('info', 'processRatio: update ratio');
				$this->updateRatio($ratio_id, $ratio_name, $ratio_value);
				break;

			case 'delete':
				log_message('info', 'processRatio: delete ratio');
				$this->deleteRatio($ratio_id);
				break;
		}

	}

	public function createRatio($name, $value){
		$this->db->query("INSERT INTO `Ratios` (name, value) VALUES ('$name', '$values')");
	}

	public function updateRatio($id, $name, $value){
		$this->db->query("UPDATE `Ratios` SET name='$name', value='$value' WHERE id='$id'");
	}

	public function deleteRatio($id){
		$this->db->query("DELETE FROM `Ratios` WHERE id='$id' ");
	}

	public function deleteCompany($comp_id){
		$this->db->query("DELETE FROM `Companies` WHERE id='$comp_id'");
	}

	public function updateCompany($comp_id, $comp_name){

		$this->db->query("UPDATE `Companies` SET name='$comp_name' WHERE id='$comp_id'");

	}

	public function createCompany($comp_name){
		$this->db->query("INSERT INTO `Companies` (name) VALUES ('$comp_name')");
	}

}
