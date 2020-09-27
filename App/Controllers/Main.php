<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Categories;
use \App\Models\Incomes;

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

	public static function showMainReportAction()
	{
		$incomes = Incomes::getIncomes($_SESSION['user_id']);
		if ($incomes)
		{
			View::renderTemplate('Report/Main.html', ['incomes'=>$incomes]);
		}
		else
		{
			echo 'Nie pobrano przychodów';
		};
	}

	public function addIncomeFormAction()
    {
		$incomeForm = true;		
		$incomeCategories = Categories::getIncomeCategories();
        View::renderTemplate('Report/Main.html',['incomeForm'=>$incomeForm, 'items'=>$incomeCategories],);
    }
	
	public function addExpenseFormAction()
    {
		$expenseForm = true;
		$expenseCategories = Categories::getExpenseCategories();
        View::renderTemplate('Report/Main.html',['expenseForm'=>$expenseForm, 'items'=>$expenseCategories]);
    }
	
	public function submitIncomeAction()
	{
			$incomeToBeAdded = new Incomes($_POST);
			if (isset($_POST['kategoriaIncomeInput']))
			{
				$kategoriaIncomeInput = $_POST['kategoriaIncomeInput'];
				$kategoriaIncomeInput = $_POST['kategoriaIncomeInput'];
			} else {
				$kategoriaIncomeInput = 0;
			}
			if($incomeToBeAdded -> saveIncome($_SESSION['user_id'], $_POST['incomeAmmount'], $_POST['incomeDatePicker'],$kategoriaIncomeInput, $_POST['commentInput']))
			{
				$feedback = "Dodano Wydatek";
				View::renderTemplate('Report/Main.html',['feedback'=>$feedback]);
			} else {
				$incomeForm = true;
				View::renderTemplate('Report/Main.html',['incomeForm'=>$incomeForm, 'incomeToBeAdded'=>$income]);
			}

		

	}
}
