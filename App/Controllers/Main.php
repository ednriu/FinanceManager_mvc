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
		$incomeAdded = Incomes::addIncome(5, "2020-04-05", 4, "komencik");
		View::renderTemplate('Report/Main.html',['incomeAdded'=>$incomeAdded]);
	}
}
