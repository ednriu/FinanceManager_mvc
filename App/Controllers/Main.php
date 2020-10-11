<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Categories;
use \App\Models\Operations;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Main extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */

	public static function showReportAllRangeAction($feedback='')
	{
		$incomes = Operations::getIncomesData($_SESSION['user_id'],null, null);
		$expences = Operations::getExpencesData($_SESSION['user_id'],null, null);
		var_dump($expences);

		if ($incomes OR $expences)
		{
			$sumOfIncomesAmmount = Operations::getSumOfAmmount($incomes); //need to add date range
			$notEmptyIncomeCategories= Categories::getNotEmptyIncomeCategories($_SESSION['user_id']);
			$notEmptyIncomeCategoriesWithoutRepeats = array_unique($notEmptyIncomeCategories);
			$incomeGraphData = Operations::getIncomesGraphDate($notEmptyIncomeCategoriesWithoutRepeats, $sumOfIncomesAmmount, $_SESSION['user_id']);
			
			$sumOfExpencesAmmount = Operations::getSumOfAmmount($expences); //need to add date range
			$notEmptyExpenceCategories= Categories::getNotEmptyExpenceCategories($_SESSION['user_id']);
			$notEmptyExpenceCategoriesWithoutRepeats = array_unique($notEmptyExpenceCategories);
			$expenceGraphData = Operations::getExpencesGraphDate($notEmptyExpenceCategoriesWithoutRepeats, $sumOfExpencesAmmount, $_SESSION['user_id']);
			
			View::renderTemplate('Report/Main.html', ['incomes'=>$incomes, 'expences'=>$expences, 'sumOfIncomesAmmount'=>$sumOfIncomesAmmount, 'sumOfExpencesAmmount'=>$sumOfExpencesAmmount, 'feedback'=>$feedback, 'incomeGraphDate'=>$incomeGraphData, 'expenceGraphDate'=>$expenceGraphData]);
		}
		else
		{
			View::renderTemplate('Report/Main.html', ['incomes'=>null, 'sumOfIncomesAmmount'=>0, 'feedback'=>"Jesteś nowym użytkownikiem", 'graphDate'=>0]);
		};
	}

	//Shows Add Incomes Form
	public function addIncomeFormAction()
    {
		$incomeForm = true;	//variable causes showing income Form	
		$incomeCategories = Categories::getIncomeCategories($_SESSION['user_id']);
        View::renderTemplate('Report/Main.html',['incomeFormVisible'=>$incomeForm, 'incomeCategories'=>$incomeCategories],);
    }
	
	//Shows Add Expence Form
	public function addExpenceFormAction()
    {
		$expenceForm = true;
		$expenceCategories = Categories::getExpenceCategories($_SESSION['user_id']);
        View::renderTemplate('Report/Main.html',['expenceFormVisible'=>$expenceForm, 'expenceCategories'=>$expenceCategories]);
    }	
	
	
	//Returns $postedCategory if exists. 
	//Returns 0 if $postedCategory does not exists
	private function getSelectedCategory($postedCategory)
	{
			if (isset($postedCategory))
			{
				return $postedCategory;
			} else {
				return 0;
			};
	}
	
	//Submit Add Incomes Form
	public function submitIncomeAction()
	{
			$incomeToBeAdded = new Operations($_POST);
			$selectedCategoryId = Main::getSelectedCategory($_POST['kategoriaIncomeInput']);
			
			if($incomeToBeAdded -> saveIncome($_SESSION['user_id'], $_POST['incomeAmmount'], $_POST['incomeDatePicker'],$selectedCategoryId, $_POST['commentInput']))
			{
				$feedback = "Dodano Wpływ";
				parent::redirect('/Main/showReportAllRange'); //potrzeba dodać do URL parametr raportu
			} else {
				$incomeFormVisible = true;
				$incomeCategoriesForUser = Categories::getIncomeCategories($_SESSION['user_id']);
				View::renderTemplate('Report/Main.html',['incomeFormVisible'=>$incomeFormVisible, 'incomeToBeAdded'=>$incomeToBeAdded, 'incomeCategories'=>$incomeCategoriesForUser, 'selectedCategoryId'=>$selectedCategoryId]);
			}
	}
	
	//Submit Add Expences Form
	public function submitExpenceAction()
	{
			$expenceToBeAdded = new Operations($_POST);
			$selectedCategoryId = Main::getSelectedCategory($_POST['kategoriaExpenceInput']);
			var_dump($_POST['payMethod']);
			var_dump($selectedCategoryId);			
			var_dump($_POST['kategoriaExpenceInput']);
			
			if($expenceToBeAdded -> saveExpence($_SESSION['user_id'], $_POST['expenceAmmount'], $_POST['expenceDatePicker'], $selectedCategoryId, $_POST['commentInput'], $_POST['payMethod']))
			{
				$feedback = "Dodano Wydatek";
				parent::redirect('/Main/showReportAllRange'); 
			} else {
				$expenceFormVisible = true;
				$expenceCategoriesForUser = Categories::getExpenceCategories($_SESSION['user_id']);
				View::renderTemplate('Report/Main.html',['expenceFormVisible'=>$expenceFormVisible, 'expenceToBeAdded'=>$expenceToBeAdded, 'expenceCategories'=>$expenceCategoriesForUser, 'selectedCategoryId'=>$selectedCategoryId]);
			}
	}
	
}
