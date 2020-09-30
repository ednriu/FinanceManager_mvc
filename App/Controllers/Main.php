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

	public static function showMainReportAction($feedback='')
	{

		$incomes = Incomes::getIncomes($_SESSION['user_id']);
		if ($incomes)
		{
			$suma = Incomes::getSumOfAmmount($incomes);
			
			$kategoriePrzychodowNiezerowych = new Categories();
			$kategoriePrzychodowNiezerowych->getNotEmptyIncomeCategories(5);
			
			
			View::renderTemplate('Report/Main.html', ['incomes'=>$incomes, 'sumOfIncomes'=>$suma, 'feedback'=>$feedback]);
		}
		else
		{
			$error = "błąd"; //to nie może być tak, ponieważ w przypadku braku rekordów w bazie danych jest wyrzucany błąd.
		};
	}

	public function addIncomeFormAction()
    {
		$incomeForm = true;		
		$incomeCategories = Categories::getIncomeCategories($_POST['user_id']);
        View::renderTemplate('Report/Main.html',['incomeForm'=>$incomeForm, 'items'=>$incomeCategories],);
    }
	
	public function addExpenseFormAction()
    {
		$expenseForm = true;
		$expenseCategories = Categories::getExpenseCategories($_SESSION['user_id']);
        View::renderTemplate('Report/Main.html',['expenseForm'=>$expenseForm, 'items'=>$expenseCategories]);
    }
	
	public function submitIncomeAction()
	{
			$incomeToBeAdded = new Incomes($_POST);
			if (isset($_POST['kategoriaIncomeInput']))
			{
				$kategoriaIncomeInput = $_POST['kategoriaIncomeInput'];
			} else {
				$kategoriaIncomeInput = 0;
			};
			
			$incomeCategories = Categories::getIncomeCategories($_SESSION['user_id']);
			
			if($incomeToBeAdded -> saveIncome($_SESSION['user_id'], $_POST['incomeAmmount'], $_POST['incomeDatePicker'],$kategoriaIncomeInput, $_POST['commentInput']))
			{
				$feedback = "Dodano Wydatek";
				//View::renderTemplate('Report/Main.html',['feedback'=>$feedback]);
				parent::redirect('/Main/showMainReport/');
				Main::showMainReport('Dodano Nowy Wpływ');
			} else {
				$incomeForm = true;
				View::renderTemplate('Report/Main.html',['incomeForm'=>$incomeForm, 'incomeToBeAdded'=>$incomeToBeAdded, 'items'=>$incomeCategories]);
			}
	}
}
