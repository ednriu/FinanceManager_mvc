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
		$incomes = Operations::getOperationsData($_SESSION['user_id'],null, null);
		if ($incomes)
		{
			$sumOfIncomesAmmount = Operations::getSumOfAmmount($incomes); //need to add date range
			$notEmptyIncomeCategories= Categories::getNotEmptyIncomeCategories($_SESSION['user_id']);
			$notEmptyIncomeCategoriesWithoutRepeats = array_unique($notEmptyIncomeCategories);
			$incomeGraphData = Operations::getGraphDate($notEmptyIncomeCategoriesWithoutRepeats, $sumOfIncomesAmmount, $_SESSION['user_id']);
			View::renderTemplate('Report/Main.html', ['incomes'=>$incomes, 'sumOfIncomesAmmount'=>$sumOfIncomesAmmount, 'feedback'=>$feedback, 'graphDate'=>$incomeGraphData]);
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
	
	//Shows Add Expense Form
	public function addExpenseFormAction()
    {
		$expenseForm = true;
		$expenseCategories = Categories::getExpenseCategories($_SESSION['user_id']);
        View::renderTemplate('Report/Main.html',['expenseForm'=>$expenseForm, 'expenseCategories'=>$expenseCategories]);
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
				$feedback = "Dodano Wydatek";
				parent::redirect('/Main/showReportAllRange'); //potrzeba dodać do URL parametr raportu
			} else {
				$incomeFormVisible = true;
				$incomeCategoriesForUser = Categories::getIncomeCategories($_SESSION['user_id']);
				View::renderTemplate('Report/Main.html',['incomeFormVisible'=>$incomeFormVisible, 'incomeToBeAdded'=>$incomeToBeAdded, 'incomeCategories'=>$incomeCategoriesForUser, 'selectedCategoryId'=>$selectedCategoryId]);
			}
	}
	
}
