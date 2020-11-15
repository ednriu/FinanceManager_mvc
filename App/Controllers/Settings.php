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

    //Income Categories Settings - redirecting
    public function incomeCategoriesSettingsAction()
    {
		$incomeCategories = Categories::getIncomeCategoriesForNewIncome($_SESSION['user_id']);
        View::renderTemplate('Settings/application_settings.html',['option'=>1, 'categories'=>$incomeCategories]);
    }

    //Expence Categories Settings - redirecting
    public function expenceCategoriesSettingsAction()
    {
		$expenceCategories = Categories::getExpenceCategoriesForNewExpence($_SESSION['user_id']);
        View::renderTemplate('Settings/application_settings.html',['option'=>2, 'categories'=>$expenceCategories]);
    }
	
	//PayMethod Categories Settings - redirecting
	public function payMethodSettingsAction()
    {
		$payMethodCategories = Categories::getPayMethodCategoriesForNewPayMethod($_SESSION['user_id']);
        View::renderTemplate('Settings/application_settings.html',['option'=>3, 'categories'=>$payMethodCategories]);
    }	

	
	//remove income Category
	public function removeCategoryAction()
    {

		if(isset($_POST['category']) && isset($_POST['categoryType'])) {
			$categoryToBeRemoved=$_POST['category'];
			switch ($_POST['categoryType']) {
				case "incomes":
					$incomeCategories = Categories::removeIncomeCategory($_SESSION['user_id'], $categoryToBeRemoved);
					if ($incomeCategories) 
					{
						$successfullyRemoved=true;
						$message="Usunięto wybraną kategorię.";
					};
					if (!$incomeCategories) 
					{
						$successfullyRemoved=false;
						$message="Nie usunięto wybranej kategorii z powodu błędu.";
					};	
					break;
				
				case "expences":
					$expenceCategories = Categories::removeExpenceCategory($_SESSION['user_id'], $categoryToBeRemoved);
					if ($expenceCategories) 
					{
						$successfullyRemoved=true;
						$message="Usunięto wybraną kategorię.";
					};
					if (!$expenceCategories) 
					{
						$successfullyRemoved=false;
						$message="Nie usunięto wybranej kategorii z powodu błędu.";
					};	
					break;
				
				case "payMethods":
					$payMethodCategories = Categories::removePayMethodCategory($_SESSION['user_id'], $categoryToBeRemoved);
					if ($payMethodCategories) 
					{
						$successfullyRemoved=true;
						$message="Usunięto wybraną kategorię.";
					};
					if (!$payMethodCategories) 
					{
						$successfullyRemoved=false;
						$message="Nie usunięto wybranej kategorii z powodu błędu.";
					};	
					break;				
			}
		  } else {
			return false;
		  };

		
			echo json_encode(array("successfullyRemoved"=>$successfullyRemoved,"message"=>$message));		

    
	}
	
	//add income category
	public function addCategoryAction()
	{
		if(isset($_POST['categoryType']) && isset($_POST['categoryName']) && isset($_POST['max'])) {
				$categoryToBeAdded=(ucwords(strtolower($_POST['categoryName'])));
				$maxLimit=$_POST['max'];
				switch ($_POST['categoryType']) {
					case "incomes":
						$incomeCategories = Categories::addNewIncomeCategory($_SESSION['user_id'], $categoryToBeAdded, $maxLimit);
						if ($incomeCategories) {
							$isCategoryDoubled=false;
							$message="Dodano nową kategorię.";
						}
						if (!$incomeCategories) {
							$isCategoryDoubled=true;
							$message="Istnieje już taka kategoria.";
						}						
						break;
					case "expences":
						$expenceCategories = Categories::addNewExpenceCategory($_SESSION['user_id'], $categoryToBeAdded, $maxLimit);
						if ($expenceCategories) {
							$isCategoryDoubled=false;
							$message="Dodano nową kategorię.";
						}
						if (!$expenceCategories) {
							$isCategoryDoubled=true;
							$message="Istnieje już taka kategoria.";
						}
						break;
					case "paymethod":
						//płatności
						break;
				}
				echo json_encode(array("isCategoryDoubled"=>$isCategoryDoubled,"message"=>$message));		
			} else {
			echo "Błąd Połączenia.";

		  };	
	}	
	
	//add income category
	public function updateCategoryAction()
	{
		if(isset($_POST['categoryType']) && isset($_POST['newCategoryName']) && isset($_POST['oldCategoryName']) && isset($_POST['max'])) {			
				$oldCategoryName=$_POST['oldCategoryName'];
				$newCategoryName=$_POST['newCategoryName'];
				$maxLimit=$_POST['max'];
				switch ($_POST['categoryType']) {
					case "incomes":
						$incomeCategories = Categories::updateIncomeCategory($oldCategoryName,$newCategoryName,$maxLimit,$_SESSION['user_id']);
						if ($incomeCategories) {
							$isCategoryDoubled=false;
							$message="Zaktualizowano dane1.";
						}
						if (!$incomeCategories) {
							$isCategoryDoubled=true;
							$message="Istnieje już taka kategoria.";
						}
						echo json_encode(array("isCategoryDoubled"=>$isCategoryDoubled,"message"=>$message));
						break;
					case "expences":
						$expenceCategories = Categories::updateExpenceCategory($oldCategoryName,$newCategoryName,$maxLimit,$_SESSION['user_id']);
						if ($expenceCategories) {
							$isCategoryDoubled=false;
							$message="Zaktualizowano dane2.";
						}
						if (!$incomeCategories) {
							$isCategoryDoubled=true;
							$message="Istnieje już taka kategoria.";
						}
						echo json_encode(array("isCategoryDoubled"=>$isCategoryDoubled,"message"=>$message));
						break;
					case "paymethod":
						break;
				}
		  } else {
			echo "Błąd Połączenia.";

		  };
		
	}
	
	//replace income categories ids
	public function replaceIncomeCategoriesIds()
	{

		if(isset($_POST['firstCategoryName']) && isset($_POST['secondCategoryName'])) {
			$firstCategoryName = $_POST['firstCategoryName'];
			$secondCategoryName = $_POST['secondCategoryName'];
			$firstCategoryId = Categories::getIncomeCategoryId($firstCategoryName,$_SESSION['user_id']);
			$secondCategoryId = Categories::getIncomeCategoryId($secondCategoryName,$_SESSION['user_id']);
			$assignFirstId = Categories::setIncomeCategoryId($firstCategoryName,$_SESSION['user_id'], 0);
			$assignSecondId = Categories::setIncomeCategoryId($secondCategoryName,$_SESSION['user_id'], $firstCategoryId);
			$assignFirstId = Categories::setIncomeCategoryId($firstCategoryName,$_SESSION['user_id'], $secondCategoryId);
			echo true;
		}

	}
	
	

	



}