<?php

/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';
ini_set("memory_limit", "6024M");
ini_set("max_execution_time", "0");

class exportController extends Zend_Controller_Action {

	public function indexAction() {

	}
	
	public function siteMapAction(){
		
		$this->_helper->viewRenderer->setNoRender();
		$id = $this->_request->getParam('id');
		$siteMapObj = new SiteMap;
		$xml = $siteMapObj->getSiteMap($idKey); 
		if(!$xml){
			$this->view->requestURI = $this->_request->getRequestUri(); 
			return $this->render('404error');
		}
		else{
			header("Content-type: application/xml");
			echo $xml;
		}
	}
	
	
	public function pelagiosAction() {
		
		OpenContext_SocialTracking::update_referring_link('export-pelagios', $this->_request->getRequestUri(), @$_SERVER['HTTP_USER_AGENT'], @$_SERVER['HTTP_REFERER']);
		$atomPelagios = new AtomToPelagios;
		
		if(isset($_GET["new"])){
			$atomPelagios->clearCache = true;
		}
		else{
			$atomPelagios->clearCache = false;
		}
		
		$atomPelagios->getData();
		$this->view->data = $atomPelagios->data;
		unset($atomPelagios);
	}
	
	public function pelagiosVoidAction() {
		
		OpenContext_SocialTracking::update_referring_link('export-pelagios-void', $this->_request->getRequestUri(), @$_SERVER['HTTP_USER_AGENT'], @$_SERVER['HTTP_REFERER']);
		
	}
	
	
	
	//iterate through and export all of the projects, subjects, media, and diary items
	public function xmlDumpAction() {
		mb_internal_encoding( 'UTF-8' );
		$this->_helper->viewRenderer->setNoRender();
		$output = array();
		$AllDumpObj = new AllDump;
		$outputDirs = $AllDumpObj->exportAll();
		$output["count"] = $AllDumpObj->exportCount;
		$output["directories"] = $outputDirs;
		header('Content-Type: application/json; charset=utf8');
		echo Zend_Json::encode($output);
	}
	
	
	
	//iterate through and export all of the projects, subjects, media, and diary items
	public function xmlDumpCheckAction() {
		mb_internal_encoding( 'UTF-8' );
		$this->_helper->viewRenderer->setNoRender();
		
		$AllDumpObj = new AllDump;
		$output = array("error" => "need a projectUUID parameter");
		
		if(isset($_GET["projectUUID"])){
			$projectUUID = $_GET["projectUUID"];
			$output = $AllDumpObj->GITsynch($projectUUID);
			
		}
		
		header('Content-Type: application/json; charset=utf8');
		echo Zend_Json::encode($output);
	}
	
	
	//iterate through and export all of the projects, subjects, media, and diary items
	public function dbDumpAction() {
		mb_internal_encoding( 'UTF-8' );
		$this->_helper->viewRenderer->setNoRender();
		
		$projects = array('99BDB878-6411-44F8-2D7B-A99384A6CA21',
								'1B426F7C-99EC-4322-4069-E8DBD927CCF1',
								'02594C48-7497-40D7-11AE-AB942DC513B8',
								'D297CD29-50CA-4B2C-4A07-498ADF3AF487'
								);
		/*
		$projects = array('99BDB878-6411-44F8-2D7B-A99384A6CA21',
								
								);
		*/
		$projects = array('3F6DCD13-A476-488E-ED10-47D25513FCB2');
		
		$exportObj = new DBexport_OCexport;
		$exportObj->limitingProjArray = $projects;
		//$exportObj->testing = true;
		$counts = $exportObj->makeSaveSQL();
		$output = array("projects" => $projects,
							 "files" => $exportObj->files,
							 "counts" => $counts 
							 );
		
		header('Content-Type: application/json; charset=utf8');
		echo Zend_Json::encode($output);
	}
	
	
	public function subjectsCompressAction() {
		
		ini_set("memory_limit", "6024M");
		ini_set("max_execution_time", "0");
		
		mb_internal_encoding( 'UTF-8' );
		$this->_helper->viewRenderer->setNoRender();
		
		$exportObj = new DBexport_OCexport;
		$output = $exportObj->compressSubjects();
		header('Content-Type: application/json; charset=utf8');
		echo Zend_Json::encode($output);
	}
	
	public function mediaCompressAction() {
		
		ini_set("memory_limit", "6024M");
		ini_set("max_execution_time", "0");
		
		mb_internal_encoding( 'UTF-8' );
		$this->_helper->viewRenderer->setNoRender();
		
		$exportObj = new DBexport_OCexport;
		$output = $exportObj->compressMedia();
		header('Content-Type: application/json; charset=utf8');
		echo Zend_Json::encode($output);
	}
	
	public function documentsCompressAction() {
		
		ini_set("memory_limit", "6024M");
		ini_set("max_execution_time", "0");
		
		mb_internal_encoding( 'UTF-8' );
		$this->_helper->viewRenderer->setNoRender();
		
		$exportObj = new DBexport_OCexport;
		$output = $exportObj->compressDocuments();
		header('Content-Type: application/json; charset=utf8');
		echo Zend_Json::encode($output);
	}
	
	public function testRepoAction() {
		
		ini_set("memory_limit", "6024M");
		ini_set("max_execution_time", "0");
		
		mb_internal_encoding( 'UTF-8' );
		$this->_helper->viewRenderer->setNoRender();
		
		$uuid = "9388554E-D9B8-4BA2-C82A-081E39117648";
		$projectUUID = "1B426F7C-99EC-4322-4069-E8DBD927CCF1";
		$itemType = "subjects";
		
		$reposObj = new Repository  ;
		$xml = file_get_contents("http://opencontext/subjects/".$uuid.".xml");
		$reposObj->addUpdateItemData($xml, $uuid, $projectUUID, $itemType);
		
		$output = $reposObj->getItemData($uuid);
		
		
		header('Content-Type: application/xml; charset=utf8');
		echo $output;
	}
	
}//end class






