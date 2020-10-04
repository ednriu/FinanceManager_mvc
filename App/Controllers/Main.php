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

	public static function showMainReportAction($feedback='')
	{
		$incomes = Operations::getOperationsData($_SESSION['user_id'],'2020-09-14', '2020-10-14');
		if ($incomes)
		{
			$sumOfIncomesAmmount = Operations::getSumOfAmmount($incomes); //need to add date range
			$notEmptyIncomeCategories= Categories::getNotEmptyIncomeCategories($_SESSION['user_id']); //need to add date range
			$notEmptyIncomeCategoriesWithoutRepeats = array_unique($notEmptyIncomeCategories);
			$incomeGraphData = Operations::getGraphDate($notEmptyIncomeCategoriesWithoutRepeats, $sumOfIncomesAmmount, $_SESSION['user_id']);

			View::renderTemplate('Report/Main.html', ['incomes'=>$incomes, 'feedback'=>$feedback, 'graphDate'=>$incomeGraphData]);
		}
		else
		{
			$error = "błąd"; //to nie może być tak, ponieważ w przypadku braku rekordów w bazie danych jest wyrzucany błąd.
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
				parent::redirect('/Main/showMainReport'); //potrzeba dodać do URL parametr raportu
			} else {
				$incomeFormVisible = true;
				$incomeCategoriesForUser = Categories::getIncomeCategories($_SESSION['user_id']);
				View::renderTemplate('Report/Main.html',['incomeFormVisible'=>$incomeFormVisible, 'incomeToBeAdded'=>$incomeToBeAdded, 'incomeCategories'=>$incomeCategoriesForUser, 'selectedCategoryId'=>$selectedCategoryId]);
			}
	}
}
