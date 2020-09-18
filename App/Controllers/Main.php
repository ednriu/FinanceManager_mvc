<?php

namespace App\Controllers;

use \Core\View;

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
    public function addIncomeCategoriesAction()
    {
        
    }
	

	    public function addIncomeAction()
    {
		$incomeForm = true;
        View::renderTemplate('Report/Main.html',['incomeForm'=>$incomeForm]);
    }
	
		public function addExpenseAction()
    {
		$expenseForm = true;
        View::renderTemplate('Report/Main.html',['expenseForm'=>$expenseForm]);
    }
}
