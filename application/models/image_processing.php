<?

class image_processing extends CI_Model{

	public function __construct(){
		$this->load->model('Object_Templates');
		$this->load->model('DB_functs');

	}

	public function processImage($optionId){

		log_message('info', 'image_processing: processImage');

		//Get Option
		$option = $this->DB_functs->getOptionById($optionId);
		//Get ratio
		$ratio = $this->DB_functs->getRatioById($option->ratio_id);

		log_message('info', 'processing image with option: '.$option->id);
		log_message('info', 'and ratio: '.$ratio['name']);

	}
	
	

	function processFiles($optionId){
		log_message('info', 'image_processing: processImage');

		//Get Option
		$option = $this->DB_functs->getOptionById($optionId);
		//Get ratio
		$ratio = $this->DB_functs->getRatioById($option->ratio_id);
		//Get company
		$company = $this->DB_functs->getCompanyById($option->comp_id);

		log_message('info', 'processing image with option: '.$option->id);
		log_message('info', 'and ratio: '.$ratio['name']);
		log_message('info', 'for company: '.$company['name']);


		//Set function variables
		$targetRatio = $ratio['value'];

		$smallWidth = $option->x_small;
		$medWidth = $option->x_med;
		$largeWidth = $option->x_large;

		$smallPath = 'small';
		$medPath = 'med';
		$largePath = 'large';

		$archive = $company['name'];


		
		log_message('debug','modelImage: processFiles');

		log_message('debug','ratio: '.$targetRatio);

		$listFiles = scandir(TMP_FLD);

		log_message('debug','# images: '.count($listFiles));

		try{
			$source = TMP_FLD;
			foreach ($listFiles as $imageFile) {
				if(strpos($imageFile, '.') == 0){continue;}

				log_message('debug','processingImage');
			
				$image = new Imagick($source.$imageFile);
				
				$geo = $image->getImageGeometry();

				//define as oldWidth and oldHeight
				settype($originSizeX, "integer");
				settype($originSizeY, "integer");
				settype($targetRatio, "double");
				$originSizeX=$geo['width'];
				$originSizeY=$geo['height'];

				//get original ratio by dividing the original width by the original height
				$originRatio = ($originSizeX / $originSizeY);
				//Define Target Ratio
				//$targetRatio = 1.33;
				
				//If original ratio is less than targetRatio then scale image by width
				//elseIF original ratio is greater than targetRatio then scale image by height
				if($originRatio < $targetRatio) {
					$targetWidth = ($originSizeY * $targetRatio);
					$canvas = new Imagick();
					$canvas->newImage($targetWidth ,$originSizeY, new ImagickPixel('white'));
					$canvas->setImageFormat('jpg');
					$canvas->compositeImage($image, imagick::COMPOSITE_OVER, (($targetWidth-$originSizeX)/2), 0);
					//echo $imageFile . "- New Width= " . $targetWidth . "<br>";
				} elseif($originRatio > $targetRatio){ 
					$targetHeight = ($originSizeX * (1/$targetRatio));
					$canvas = new Imagick();
					$canvas->newImage($originSizeX ,$targetHeight, new ImagickPixel('white'));
					$canvas->setImageFormat('jpg');
					$canvas->compositeImage($image, imagick::COMPOSITE_OVER, 0, (($targetHeight-$originSizeY)/2));
					//echo $imageFile . "- New Height= " . $targetHeight . "<br>";
				}else{
		                        $canvas = new Imagick();
		                        $canvas->newImage($originSizeX ,$originSizeY, new ImagickPixel('white'));
		                        $canvas->setImageFormat('jpg');
		                        $canvas->compositeImage($image, imagick::COMPOSITE_OVER, 0, 0);
		                        
		        }

			unset ($image);
				
				//getFormat, if not .jpg setformat to jpg
				if($canvas->getImageFormat() != 'JPEG')
				{
					$canvas->setImageFormat("jpeg");
					
					//if filename has .png suffix, strip png and add jpg
					if(stripos($imageFile, '.png') !== false)
					{
						$imageFile = str_ireplace('.png', '.jpg', $imageFile);
					}
					//remove .tif suffix and replace with .jpg
					if(stripos($imageFile, '.tif') !== false)
					{
						$imageFile = str_ireplace('.tif', '.jpg', $imageFile);
					}
				}
				
				//set height to 0 to keep aspect ratio
				$height = 0;
				
			
				//Make sure output folder exists
				if(!file_exists(OUTPUT_FOLDER)){
					mkdir(OUTPUT_FOLDER);
				}
				//Make sure company path exists
				$outputCompany_path = OUTPUT_FOLDER.'/'.$archive;
				if(!file_exists($outputCompany_path)){
					mkdir($outputCompany_path);
				}
				//Make sure archive folder exists
				if(!file_exists(ARCHIVE)){
					mkdir(ARCHIVE);
				}
				//Make sure archive company folder exists
				$archiveCompany_path = ARCHIVE.'/'.$archive;
				if(!file_exists($archiveCompany_path)){
					mkdir($archiveCompany_path);
				}



				$width = $largeWidth;	
				$quality = 90;	
				$destination_folder = $outputCompany_path.'/'.$largePath.'/';
				$newName = '-retina';
				$this->exportSize($canvas, $width, $height, $destination_folder, $imageFile, $quality,$newName);
			
				$width = $medWidth;
				$quality = 80;
				$destination_folder = $outputCompany_path.'/'.$medPath.'/';
				$newName = '-med';
				$this->exportSize($canvas, $width, $height, $destination_folder, $imageFile, $quality,$newName);

				$width = $smallWidth;
				$quality = 70;
				$destination_folder = $outputCompany_path.'/'.$smallPath.'/';
				$newName = '-small';
				$this->exportSize($canvas, $width, $height, $destination_folder, $imageFile, $quality,$newName);

				$imageSource_path = $source.$imageFile;
				$archiveImage_path = $archiveCompany_path.'/'.$imageFile;
				$this->archive($imageSource_path,$archiveImage_path);



			}
		} catch(Exception $e){
			log_message('info', 'Exception thrown: '.$e->message());
			return False;
		}
		return True;
	}

	function exportSize($canvas, $width, $height, $destination_folder, $imageFile, $quality,$newName)
	{
		log_message('debug','expostSize: '.$width);

		$output_folder = 'output_folder/';

		//Checks to make sure image is bigger than destination size
		if ($canvas->getImageWidth() > $width){
			log_message('debug','scaling width: '.$width."height: ".$height);
			$canvas->scaleImage($width,$height); // Providing 0 forces thumbnailImage to maintain aspect ratio
		}	
		$outputtype = $canvas->getFormat();
		


		if(!file_exists($destination_folder)){
			log_message('debug','destination_folder = '.$destination_folder);
			mkdir($destination_folder);
		}

		$canvas->setImageCompressionQuality($quality);

		$extension_pos = strrpos($imageFile, '.'); // find position of the last dot, so where the extension starts
	    $newExtension = substr($imageFile, 0, $extension_pos) . $newName . substr($imageFile, $extension_pos);
	    $canvas->writeImage($destination_folder.$newExtension);

	}

	function archive($source,$archive){
		if(!file_exists($source)){
			mkdir($source);
		}
		copy($source, $archive);
		unlink($source);
	}

		 

}
