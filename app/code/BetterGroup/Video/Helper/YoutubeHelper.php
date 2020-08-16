<?php
 
namespace BetterGroup\Video\Helper;

class YoutubeHelper
{
	protected $api_key = 'AIzaSyCg4LxFx4THm3SZOlKMIxEn4ioDMuryN30';
	
	public function getIdFromUrl($url)
	{
		$pattern =
		'%^# Match any youtube URL
		(?:https?://)?  # Optional scheme. Either http or https
		(?:www\.)?      # Optional www subdomain
		(?:             # Group host alternatives
		  youtu\.be/    # Either youtu.be,
		| youtube\.com  # or youtube.com
		  (?:           # Group path alternatives
			/embed/     # Either /embed/
		  | /v/         # or /v/
		  | .*v=        # or /watch\?v=
		  )             # End path alternatives.
		)               # End host alternatives.
		([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
		($|&).*         # if additional parameters are also in query string after video id.
		$%x'
		;
		$result = preg_match($pattern, $url, $matches);
		if (false !== $result) {
			if (isset($matches[1])) {
				return $matches[1];
			}
		}
		return false;
	}
	public function getVideoDataByUrl($video_url=""){
		try {
			$videoId  = $this->getIdFromUrl($video_url);
			
			if(!$videoId)
				return false;	
  
  			//$api_url = "http://www.youtube.com/oembed?url=". $video_url ."&format=json";
		  	$api_url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet%2CcontentDetails&id=' . $videoId . '&key=' . $this->api_key;
			
			$curl = curl_init($api_url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$return = curl_exec($curl);
			curl_close($curl);
			
			return json_decode($return, true);		  	

		} 
		catch (Exception $e) {
			//Log error using magento logger
		}
		return false;
	}

	public function getEncodedFileFromHttp($url="",$dest_path="")
	{
		$context = null;
		if ($file = $this->doesRemoteFileExist($url)) {
			/*
			$image = file_get_contents($filePath, false, $context);
			$imageData = base64_encode($image);
			$file_info = new \finfo(FILEINFO_MIME_TYPE);
			$mimeType = $file_info->buffer($image);
			//$this->imageMimeType = $mimeType;
			return $imageData;*/
			$url= $this->formatURL($url);
            try{
            	$dest_folder = pathinfo($dest_path,PATHINFO_DIRNAME);
            	if ( !file_exists( $dest_folder ) && !is_dir( $dest_folder ) ) {
	                mkdir( $dest_folder,0755, true);       
	            } 
                file_put_contents($dest_path, fopen($url, 'r'));
                //if(File::isFile($dest_path))
                    //return $dest_path;
                return true;
            }
            catch(\Exception $e){

            }  

		}
		return false;
	}

	public function doesRemoteFileExist($url){
		/*
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		// don't download content
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$fileExists = curl_exec($ch);
		if ($fileExists!==false) {
			$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ($retcode == '200') {
				unset($retcode);
				return $fileExists;
			}
			return false;
		} else {
			unset($fileExists);
			return false;
		}*/

        $url= $this->formatURL($url);
        $headers = @get_headers($url); 
		if($headers && strpos( $headers[0], '200')) {
			return true;
		}
		else{
			return false;         	
		}

	}
	public function formatURL($url){
		return str_replace(' ', '%20', $url);
	}
	public function debugLog($data=''){
        $file='myLog'.date('d-m-Y').'All.log';
        file_put_contents($file, $data."\r\n", FILE_APPEND | LOCK_EX);
    }	
		
}