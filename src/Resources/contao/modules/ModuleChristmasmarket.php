<?php

/**
 * WBGym
 * 
 * Copyright (C) 2015 Webteam Weinberg-Gymnasium Kleinmachnow
 * 
 * @package 	WGBym
 * @version 	0.3.0
 * @author 		Johannes Cram <j-cram@gmx.de>
 * @license 	http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Namespace
 */
namespace WBGym;

class ModuleChristmasmarket extends \Module
{
protected $strTemplate = 'wb_christmasmarket';

	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### WBGym Weihnachtsmarkt ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}
		
		return parent::generate();
		
	}

protected function compile(){
	  $this->import('Database');
	  
	$this->Template->stations = $this->loadStations();

	 /*Formular für Standanmeldung ====================================*/
	  
	  if(FE_USER_LOGGED_IN) {
	  $this->import('FrontendUser','User');
	  
	  //If User is Teacher
	  if(in_array(WBGym::getGroupIdFor('teachers'),$this->User->groups)){
		  $this->Template->access = true;
		  $this->Template->isTeacher = true;
		  $courses = $this->Database->prepare("SELECT * FROM tl_courses ORDER BY grade,formSelector")->execute();
		  	
			while($arrCourse = $courses->fetchAssoc()) {
				$arrCourses[] = $arrCourse;
			}
			$this->Template->arrCourses = $arrCourses;
	  }
	 
	 //If User is Class Speaker
	 else {
			$thisCourse = $this->Database->prepare("SELECT * FROM tl_courses WHERE classsp1 = ? or classsp2 = ?")->execute($this->User->id,$this->User->id)->fetchAssoc();
			
			if(!empty($thisCourse) && ($thisCourse['classsp1'] == $this->User->id || $thisCourse['classsp2'] == $this->User->id)){
				$this->Template->access = true;
				$this->Template->isClassSpeaker = true;
				$this->Template->strCourse = WBGym::course($thisCourse['id']);
				$this->Template->intCourse = $thisCourse['id'];
			}
	  }
	  $this->Template->email = $this->User->email;
	  
	  
	  //BEGIN Actions for wish form =======================
	
		if($_POST['FORM_SUBMIT'] == 'whm_form') {
				if (!$_POST['standName']) {
					$this->Template->formError = $GLOBALS['TL_LANG']['WHM']['name_empty'];
					$this->Template->email = $_POST['email'];
					$this->Template->description = $_POST['description'];
					return;
				}
				if (!$_POST['email']) {
					$this->Template->formError = $GLOBALS['TL_LANG']['WHM']['email_empty'];
					$this->Template->standName = $_POST['standName'];
					$this->Template->description = $_POST['description'];
					return;
				}
				if (!$_POST['wishnum']) {
					$this->Template->formError = $GLOBALS['TL_LANG']['WHM']['wishnum_empty'];
					$this->Template->standName = $_POST['standName'];
					$this->Template->email = $_POST['email'];
					$this->Template->description = $_POST['description'];
					return;
				}
		
		if($_POST['course']){
			$courseId = $_POST['course'];
		} else{ 
			$courseId = $thisCourse['id'];
		}
		
		$arrStations = $this->loadStations();
		
		if($arrStations) {
		foreach ($arrStations as $station){
				if($station['wishnum'] == $_POST['wishnum'] && $station['course'] == $courseId){
					$this->Template->formError = $GLOBALS['TL_LANG']['WHM']['wishnum_already_exists'];
					return;
				}
			}
		}
		
		$this->Database->prepare("INSERT INTO tl_christmasmarket (name,tstamp,description,email,course,wishnum,approved) VALUES (?,?,?,?,?,?,0)")->execute($_POST['standName'],time(),$_POST['description'],$_POST['email'],$courseId,$_POST['wishnum']);
		$this->Template->message = $GLOBALS['TL_LANG']['WHM']['success'];
		
		$this->Template->stations = $this->loadStations();
		
	//END actions for wish form =========================
		}
		
	//BEGIN actions for edit form ========================
	
	if($_POST['FORM_SUBMIT'] == 'whm_edit_form'){
		
		if(!$_POST['standName']){
			$this->Template->formError = $GLOBALS['TL_LANG']['WHM']['name_empty'];
			return;
		}
		$standId = $_POST['station_id'];
		$this->Database->prepare("UPDATE tl_christmasmarket SET name=?, description=?, tstamp=? WHERE id=$standId ")->execute($_POST['standName'], $_POST['description'],time());
		$this->Template->message = $GLOBALS['TL_LANG']['WHM']['success'];
		
		$this->Template->stations = $this->loadStations();
	}
	//END actions for edit form =========================
		
}
}
	  
	protected function loadStations(){
			  $stations = $this->Database->prepare("SELECT * FROM tl_christmasmarket JOIN tl_courses ON tl_christmasmarket.course = tl_courses.id ORDER BY tl_courses.grade, tl_courses.formSelector,tl_courses.graduation asc,tl_christmasmarket.wishnum asc")
										->execute();
	  while($arrStation = $stations->fetchAssoc()) {
		$arrStation['course_str'] = WBGym::course($arrStation['course']);  
		$arrStations[] = $arrStation;
	  }
	  return $arrStations;
	}
}
?>