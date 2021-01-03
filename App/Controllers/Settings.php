<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Categories;
use \App\Models\User;
use \App\Models\Operations;

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

	//Password Change - redirecting
	public function changePasswordRenderAction()
    {
        View::renderTemplate('Settings/user_settings.html',['option'=>1]);
    }
	
	//User Data Change - redirecting
	public function changeUserDataRenderAction()
    {
		$personalDate = new User();
        View::renderTemplate('Settings/user_settings.html',['option'=>2, 'personalDate'=>$personalDate->getUserDataInfo($_SESSION['user_id'])]);
    }
	
	//remove Category
	public function removeCategoryAction()
    {
		if(isset($_POST['category']) && isset($_POST['categoryType'])) {
			$categoryToBeRemoved=$_POST['category'];
			
			switch ($_POST['categoryType']) {
				case "incomes":
					$categoryID = Categories::getIncomeCategoryId($categoryToBeRemoved,$_SESSION['user_id']);
					$notCategorizedCategoryId = Categories::getIncomeNotCategorizedCategoryId($_SESSION['user_id']);
					$incomes = Operations::setNewCategoryIdForIncomes($categoryID, $notCategorizedCategoryId);
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
					$categoryID = Categories::getExpenceCategoryId($categoryToBeRemoved,$_SESSION['user_id']);
					$notCategorizedCategoryId = Categories::getExpenceNotCategorizedCategoryId($_SESSION['user_id']);
					$expences = Operations::setNewCategoryIdForExpences($categoryID, $notCategorizedCategoryId);
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
					$categoryID = Categories::getPayMethodCategoryId($categoryToBeRemoved,$_SESSION['user_id']);
					$notCategorizedCategoryId = Categories::getPayMethodNotCategorizedCategoryId($_SESSION['user_id']);
					$expences = Operations::setNewPayMethodCategoryIdForExpences($categoryID, $notCategorizedCategoryId);
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
	
	//add category
	public function addCategoryAction()
	{
		if(isset($_POST['categoryType']) && isset($_POST['categoryName']) && isset($_POST['max'])) {
				$categoryToBeAdded=(ucwords(strtolower($_POST['categoryName'])));
				$maxLimit=$_POST['max'];
				
				//if the $maxLimit is not number the error and message is returned.
				if(!is_numeric($maxLimit)) {
					$isCategoryError=true;
					$message="Wartość limitu musi być liczbą.";
					echo json_encode(array("isCategoryError"=>$isCategoryError,"message"=>$message));
					return 0;
				};
				
				switch ($_POST['categoryType']) {
					//INCOMES
					case "incomes":
						$incomeCategories = Categories::addNewIncomeCategory($_SESSION['user_id'], $categoryToBeAdded, $maxLimit);
						if ($incomeCategories) {
							$isCategoryError=false;
							$message="Dodano nową kategorię.";
						}
						if (!$incomeCategories) {
							$isCategoryError=true;
							$message="Istnieje już taka kategoria.";
						}						
						break;
					//EXPENCES
					case "expences":
						$expenceCategories = Categories::addNewExpenceCategory($_SESSION['user_id'], $categoryToBeAdded, $maxLimit);
						if ($expenceCategories) {
							$isCategoryError=false;
							$message="Dodano nową kategorię.";
						}
						if (!$expenceCategories) {
							$isCategoryError=true;
							$message="Istnieje już taka kategoria.";
						}
						break;
					//PAYMETHODS
					case "payMethods":
						$payMethodCategories = Categories::addNewPayMethodCategory($_SESSION['user_id'], $categoryToBeAdded, $maxLimit);
						if ($payMethodCategories) {
							$isCategoryError=false;
							$message="Dodano nową metodę płatności.";
						}
						if (!$payMethodCategories) {
							$isCategoryError=true;
							$message="Istnieje już taka metoda płatności.";
						}
						break;
						
				}
				echo json_encode(array("isCategoryError"=>$isCategoryError,"message"=>$message));		
			} else {
			echo "Błąd Połączenia.";

		  };	
	}	
	
	//update category
	public function updateCategoryAction()
	{
		
		if(isset($_POST['categoryType']) && isset($_POST['newCategoryName']) && isset($_POST['oldCategoryName']) && isset($_POST['max'])) {			
				$oldCategoryName=$_POST['oldCategoryName'];
				$newCategoryName=$_POST['newCategoryName'];
				$maxLimit=$_POST['max']; 
				
				//if the $maxLimit is not number the error and message is returned.
				if(!is_numeric($maxLimit)) {
					$isCategoryError=true;
					$message="Wartość limitu musi być liczbą.";
					echo json_encode(array("isCategoryError"=>$isCategoryError,"message"=>$message));
					return 0;
				};
				
				switch ($_POST['categoryType']) {
					//INCOMES
					case "incomes":
						$incomeCategories = Categories::updateIncomeCategory($oldCategoryName,$newCategoryName,$maxLimit,$_SESSION['user_id']);
						if ($incomeCategories) {
							$isCategoryError=false;
							$message="Zaktualizowano dane.";
						}
						if (!$incomeCategories) {
							$isCategoryError=true;
							$message="Istnieje już taka kategoria.";
						}
						echo json_encode(array("isCategoryError"=>$isCategoryError,"message"=>$message));
						break;
					//EXPENCES
					case "expences": 
						$expenceCategories = Categories::updateExpenceCategory($oldCategoryName,$newCategoryName,$maxLimit,$_SESSION['user_id']);
						if ($expenceCategories) {
							$isCategoryError=false;
							$message="Zaktualizowano dane.";
						}
						if (!$expenceCategories) {
							$isCategoryError=true;
							$message="Istnieje już taka kategoria.";
						}
						echo json_encode(array("isCategoryError"=>$isCategoryError,"message"=>$message)); 
						break;
					//PAYMETHODS
					case "payMethods":
						$payMethodCategories = Categories::updatePayMethodCategory($oldCategoryName,$newCategoryName,$maxLimit,$_SESSION['user_id']);						
						if ($payMethodCategories) {
							$isCategoryError=false;
							$message="Zaktualizowano dane.";
						}
						if (!$payMethodCategories) {
							$isCategoryError=true;
							$message="Istnieje już taka kategoria.";
						}
						echo json_encode(array("isCategoryError"=>$isCategoryError,"message"=>$message)); 
						break;
				}
		  } else {
			echo "Błąd Połączenia.";

		  };
		  
		
	}
	
	//replace income categories ids
	public function replaceCategoriesIds()
	{
		$firstCategoryName = $_POST['firstCategoryName'];
		$secondCategoryName = $_POST['secondCategoryName'];
		
		if(isset($_POST['categoryType']) && isset($_POST['firstCategoryName']) && isset($_POST['secondCategoryName'])) {
			switch ($_POST['categoryType']) {
					case "incomes":
						$firstCategoryId = Categories::getIncomeCategoryId($firstCategoryName,$_SESSION['user_id']);
						$secondCategoryId = Categories::getIncomeCategoryId($secondCategoryName,$_SESSION['user_id']);
						echo $secondCategoryName;
						$assignFirstId = Categories::setIncomeCategoryId($firstCategoryName,$_SESSION['user_id'], 0);
						$assignSecondId = Categories::setIncomeCategoryId($secondCategoryName,$_SESSION['user_id'], $firstCategoryId);
						$assignFirstId = Categories::setIncomeCategoryId($firstCategoryName,$_SESSION['user_id'], $secondCategoryId);						
						echo true;
						break;
					case "expences":
						$firstCategoryId = Categories::getExpenceCategoryId($firstCategoryName,$_SESSION['user_id']);
						$secondCategoryId = Categories::getExpenceCategoryId($secondCategoryName,$_SESSION['user_id']);
						$assignFirstId = Categories::setExpenceCategoryId($firstCategoryName,$_SESSION['user_id'], 0);
						$assignSecondId = Categories::setExpenceCategoryId($secondCategoryName,$_SESSION['user_id'], $firstCategoryId);
						$assignFirstId = Categories::setExpenceCategoryId($firstCategoryName,$_SESSION['user_id'], $secondCategoryId);
						echo true;
						break;					
					case "payMethods":
						$firstCategoryId = Categories::getPayMethodCategoryId($firstCategoryName,$_SESSION['user_id']);
						$secondCategoryId = Categories::getPayMethodCategoryId($secondCategoryName,$_SESSION['user_id']);
						$assignFirstId = Categories::setPayMethodCategoryId($firstCategoryName,$_SESSION['user_id'], 0);
						$assignSecondId = Categories::setPayMethodCategoryId($secondCategoryName,$_SESSION['user_id'], $firstCategoryId);
						$assignFirstId = Categories::setPayMethodCategoryId($firstCategoryName,$_SESSION['user_id'], $secondCategoryId);
						echo true;
						break;		
			};
		}
	}
	
	//change password	
	public function changePassword()
	{
			$userData = new User();
			if ($userData->updatePassword($_POST['newPassword1'], $_POST['newPassword2'], $_SESSION['user_id']))
			{
				echo json_encode(array("passwordWasChanged"=>true,"message"=>"Zmieniono hasło na nowe."));
			};
			if ($userData->updatePassword($_POST['newPassword1'], $_POST['newPassword2'], $_SESSION['user_id'])==false)
			{
				$errorText = $userData->errors["password_error"];
				echo json_encode(array("passwordWasChanged"=>false,"message"=>$errorText));
			};		
	}
	
	//change user personal data	
	public function changeUserPersonalData()
	{
			$userData = new User();
			
			if ($userData->updateUserDataInfo($_POST['name'], $_POST['email'], $_SESSION['user_id']))
			{
				$_SESSION['name'] = $_POST['name'];
				echo json_encode(array("dataWasChanged"=>true,"message"=>"Dane użytkownika zostały zmienione."));
			};
			if ($userData->updateUserDataInfo($_POST['name'], $_POST['email'], $_SESSION['user_id'])==false)
			{
				$errorText = $userData->errors["email_error"];
				echo json_encode(array("dataWasChanged"=>false,"message"=>$errorText));
			};		
	}
	
	
	
}
