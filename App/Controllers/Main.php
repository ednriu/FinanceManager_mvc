<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Categories;

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
		$incomeCategories = Categories::getIncomeCategories();
        View::renderTemplate('Report/Main.html',['incomeForm'=>$incomeForm, 'items'=>$incomeCategories],);
    }
	
		public function addExpenseAction()
    {
		$expenseForm = true;
		$expenseCategories = Categories::getExpenseCategories();
        View::renderTemplate('Report/Main.html',['expenseForm'=>$expenseForm, 'items'=>$expenseCategories]);
    }
}
