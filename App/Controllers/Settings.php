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
		if(isset($_POST['categoryName'])) {
			$categoryToBeAdded=$_POST['categoryName'];
			var_dump($categoryToBeAdded);
			echo("123");
		  } else {
			echo "Noooooooob";
		  }

		$maxLimit=$_POST['max'];
		$incomeCategories = Categories::addIncomeCategory($_SESSION['user_id'], $categoryToBeAdded, $maxLimit);
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