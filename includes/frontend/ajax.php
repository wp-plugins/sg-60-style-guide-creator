<?php

class sg60Ajax {
	
	function __construct(){
		add_action( 'wp_enqueue_scripts', array( $this, 'sg60Scripts' ) );
		add_action( 'wp_ajax_download_logos', array( $this, 'logoDownloads' ) );
		add_action( 'wp_ajax_nopriv_download_logos', array( $this, 'logoDownloads' ) );
	}
	
	function sg60Scripts() {
		wp_enqueue_script( 
			'sg60_scripts', 
			SG60_PLUGINURL.'/includes/frontend/js/sg60_fedScripts.js', 
			array('jquery'), 
			null, 
			false 
		);
		wp_localize_script( 
			'sg60_scripts', 
			'sg60_ajax',
			array( 'url' => admin_url( 'admin-ajax.php' ) ) 
		);
	}
	
		
	public function logoDownloads() {
		$id = $_POST['id'];
		$logos = get_post_meta( $id, '_logos', true );
		
		$upload_dir = wp_upload_dir();
		
		$newLogos = array();
		foreach( $logos as $logo ) {
			$logoTemp = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $logo );
			
			$newLogos[] = $logoTemp;
		}
		
		echo json_encode( $this->create_zip($newLogos, $id, true) );
		
		die();
	}
	
	public function create_zip( $files = array(), $id, $overwrite = false ) {
		if(!$id ) { return false; }
		$upload_dir = wp_upload_dir();
		$destination = $upload_dir['basedir'].'/sg60/'.$id.'/logos.zip';
		
		//if the zip file already exists and overwrite is false, return false
		if( file_exists( $destination ) && !$overwrite ) { return false; }
		//vars
		$valid_files = array();
		$tempFiles = array();
		$tempFiles[] = is_writable( $destination );
		//if files were passed in...
		if(is_array($files)) {
			//cycle through each file
			foreach($files as $file) {
				//make sure the file exists
				$tempFiles[] = is_readable( $file );
				if(file_exists($file) && is_readable($file)) {
					$valid_files[] = $file;
				}
			}
		}
		return $tempFiles;
		//if we have good files...
		if(count($valid_files)) {
			//create the archive
			$zip = new ZipArchive();
			if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			//add the files
			foreach($valid_files as $file) {
				$zip->addFile($file,$file);
			}
			//close the zip -- done!
			$status = $zip->getStatusString();
			$close = $zip->close();
			$zip->close();

			//check to make sure the file exists
			$return = array( 'exists' => file_exists($destination), 'url' => $destination, 'error' => $status, 'close' => $close );
			return $return;
		}
		else {
			return 'no valid files';
		}
	}
}

?>