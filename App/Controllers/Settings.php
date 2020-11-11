<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Categories;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class Settings extends \Core\Controller
{

    /**
     * Show the signup page
     *
     * @return void
     */
    public function incomeCategoriesSettingsAction()
    {
		$incomeCategories = Categories::getIncomeCategoriesForNewIncome($_SESSION['user_id']);
        View::renderTemplate('Settings/application_settings.html',['option'=>1, 'incomeCategories'=>$incomeCategories]);
    }
	
	//remove income Category
	public function removeIncomeCategoryAction()
    {

		if(isset($_POST['category'])) {
			$categoryToBeRemoved=$_POST['category'];
		  } else {
			echo "Noooooooob";
		  }	
		
		$incomeCategories = Categories::removeIncomeCategory($_SESSION['user_id'], $categoryToBeRemoved);
    
	}
	
	//add income category
	public function addIncomeCategoryAction()
	{
		if(isset($_POST['categoryName']) && isset($_POST['max'])) {
			
				$categoryToBeAdded=$_POST['categoryName'];
				$maxLimit=$_POST['max'];
				$incomeCategories = Categories::addNewIncomeCategory($_SESSION['user_id'], $categoryToBeAdded, $maxLimit);

					if ($incomeCategories) {
						$isCategoryDoubled=false;
						$message="Dodano nową kategorię.";
						echo json_encode(array("isCategoryDoubled"=>$isCategoryDoubled,"message"=>$message));
					}
					if (!$incomeCategories) {
						$isCategoryDoubled=true;
						$message="Istnieje już taka kategoria.";
						echo json_encode(array("isCategoryDoubled"=>$isCategoryDoubled,"message"=>$message));
					}
					
		
		  } else {
			echo "Błąd Połączenia.";

		  };	
		
	}	
	
	    public function expenceCategoriesSettingsAction()
    {
        View::renderTemplate('Settings/application_settings.html',['option'=>2]);
    }
	
	    public function payMethodSettingsAction()
    {
        View::renderTemplate('Settings/application_settings.html',['option'=>3]);
    }


}