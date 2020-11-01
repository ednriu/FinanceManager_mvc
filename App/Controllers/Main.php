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

	public static function showReportAllRangeAction($feedback=' - wszystkie dane', $startDate=null, $endDate=null)
	{
		$incomes = Operations::getIncomesData($_SESSION['user_id'],$startDate, $endDate);
		$expences = Operations::getExpencesData($_SESSION['user_id'],$startDate, $endDate);

		if ($incomes OR $expences)
		{
			$sumOfIncomesAmmount = Operations::getSumOfAmmount($incomes); //need to add date range
			$notEmptyIncomeCategories= Categories::getNotEmptyIncomeCategories($_SESSION['user_id']);
			$notEmptyIncomeCategoriesWithoutRepeats = array_unique($notEmptyIncomeCategories);
			$incomeGraphData = Operations::getIncomesGraphDate($notEmptyIncomeCategoriesWithoutRepeats, $sumOfIncomesAmmount, $_SESSION['user_id'], $startDate, $endDate);
			
			$sumOfExpencesAmmount = Operations::getSumOfAmmount($expences); //need to add date range
			$notEmptyExpenceCategories= Categories::getNotEmptyExpenceCategories($_SESSION['user_id']);
			$notEmptyExpenceCategoriesWithoutRepeats = array_unique($notEmptyExpenceCategories);
			$expenceGraphData = Operations::getExpencesGraphDate($notEmptyExpenceCategoriesWithoutRepeats, $sumOfExpencesAmmount, $_SESSION['user_id'], $startDate, $endDate);
			
			View::renderTemplate('Report/report_main.html', ['incomes'=>$incomes, 'expences'=>$expences, 'sumOfIncomesAmmount'=>$sumOfIncomesAmmount, 'sumOfExpencesAmmount'=>$sumOfExpencesAmmount, 'feedback'=>$feedback, 'incomeGraphDate'=>$incomeGraphData, 'expenceGraphDate'=>$expenceGraphData]);
		}
		else
		{
			View::renderTemplate('Report/report_main.html', ['incomes'=>null, 'sumOfIncomesAmmount'=>0, 'sumOfExpencesAmmount'=>0, 'feedback'=>"Brak danych do wyświetlenia", 'graphDate'=>0]);
		};
	}
	
	//Shows operations only from current month
	public function showThisMonthReportAction()
	{
		Main::showReportAllRangeAction(" z Bieżącego Miesiąca", date('Y-m-01'), date('Y-m-t'));
	}
	
	//Shows operations only from previous month
	public function showPreviousMonthReportAction()
	{
		$startDate = Main::getOneMonthBefore(date('Y-m-01'));
		$endDate = Main::getLastDayOfOneMonthBefore(date('Y-m-t'));
		Main::showReportAllRangeAction(" z Poprzedniego Miesiąca", $startDate, $endDate);
	}
	
	//Shows operations only from previous month
	public function showSelectedPeriodReportAction()
	{
		$startDate = $_POST['startDate'];
		$endDate = $_POST['endDate'];
		$message = " od ".$startDate." do ".$endDate;
		Main::showReportAllRangeAction($message, $startDate, $endDate);
	}
	
	//Shows gets one month before
	private function getLastDayOfOneMonthBefore($date){
		$day = intval(date("t", strtotime("$date")));//get the last day of the month
		$month_date = date("y-m-d",strtotime("$date -$day days"));//get the day 1 month before
		return $month_date;
	}
	
	//Shows gets one month before
	private function getOneMonthBefore($date){
		$month = 1;
		$month_date = date("y-m-d",strtotime("$date -$month months"));//get the day 1 month before
		return $month_date;
	}

	//Shows Add Incomes Form
	public function addIncomeFormAction()
    {
		$incomeForm = true;	//variable causes showing income Form	
		$incomeCategories = Categories::getIncomeCategoriesForNewIncome($_SESSION['user_id']);
        View::renderTemplate('Report/report_main.html',['incomeFormVisible'=>$incomeForm, 'incomeCategories'=>$incomeCategories],);
    }
	
	//Shows Add Expence Form
	public function addExpenceFormAction()
    {
		$expenceForm = true;
		$expenceCategories = Categories::getExpenceCategoriesForNewExpence($_SESSION['user_id']);
        View::renderTemplate('Report/report_main.html',['expenceFormVisible'=>$expenceForm, 'expenceCategories'=>$expenceCategories]);
    }	
	
	
	//Returns $postedCategory if exists. 
	//Returns 0 if $postedCategory does not exists
	private function getSelectedIncomeCategory()
	{
			if (isset($_POST['kategoriaIncomeInput']))
			{
				return $_POST['kategoriaIncomeInput'];
			} else {
				return 0;
			};
	}

	//Returns $postedCategory if exists. 
	//Returns 0 if $postedCategory does not exists
	private function getSelectedExpenceCategory()
	{
			if (isset($_POST['kategoriaExpenceInput']))
			{
				return $_POST['kategoriaExpenceInput'];
			} else {
				return 0;
			};
	}
	
	//Returns $posted PayMethod if exists. 
	//Returns 0 if $postedCategory does not exists
	private function getPayMethod()
	{
			if (isset($_POST['payMethod']))
			{
				return $_POST['payMethod'];
			} else {
				return 0;
			};
	}
	
	//Submit Add Incomes Form
	public function submitIncomeAction()
	{
			$incomeToBeAdded = new Operations($_POST);
			$selectedCategoryId = Main::getSelectedIncomeCategory();			
			if($incomeToBeAdded -> saveIncome($_SESSION['user_id'], $_POST['incomeAmmount'], $_POST['incomeDatePicker'],$selectedCategoryId, $_POST['commentInput']))
			{
				$feedback = "Dodano Wpływ";
				parent::redirect('/Main/showReportAllRange'); //potrzeba dodać do URL parametr raportu
			} else {
				$incomeFormVisible = true;
				$incomeCategoriesForUser = Categories::getIncomeCategories($_SESSION['user_id']);
				View::renderTemplate('Report/report_main.html',['incomeFormVisible'=>$incomeFormVisible, 'incomeToBeAdded'=>$incomeToBeAdded, 'incomeCategories'=>$incomeCategoriesForUser, 'selectedCategoryId'=>$selectedCategoryId]);
			}
	}
	
	//Submit Add Expences Form
	public function submitExpenceAction()
	{
			$expenceToBeAdded = new Operations($_POST);
			$selectedCategoryId = Main::getSelectedExpenceCategory();	
			$payMethod = Main::getPayMethod();
			if($expenceToBeAdded -> saveExpence($_SESSION['user_id'], $_POST['expenceAmmount'], $_POST['expenceDatePicker'], $selectedCategoryId, $_POST['commentInput'], $payMethod))
			{
				$feedback = "Dodano Wydatek";
				parent::redirect('/Main/showReportAllRange'); 
			} else {
				$expenceFormVisible = true;
				$expenceCategoriesForUser = Categories::getExpenceCategories($_SESSION['user_id']);
				View::renderTemplate('Report/report_main.html',['expenceFormVisible'=>$expenceFormVisible, 'expenceToBeAdded'=>$expenceToBeAdded, 'expenceCategories'=>$expenceCategoriesForUser, 'selectedCategoryId'=>$selectedCategoryId]);
			}
	}
	
}
