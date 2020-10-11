<?php

namespace App\Models;

use PDO;

/**
 * Categories model
 *
 * PHP version 7.0
 */
class Operations extends \Core\Model
{
	
	public $operationErrors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values
     *
     * @return void
     */
    public function __construct($data=[])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Savind incomes to DB
     *
     * 
     *
     * @return bool
     */   
	private function validate()
	{
		//Ammount
		if ($this->ammount == '') {
            $this->incomeErrors["error_ammount"] = 'Nie podano kwoty';			
        }
		//Date
				if ($this->datePicker == '') {
            $this->incomeErrors["error_date"] = 'Nie wybrano daty';			
        }
		//Category
				if ($this->category == '0') {
            $this->incomeErrors["error_category"] = 'Nie wybrano kategorii';			
        }
		//Comment
				if (strlen($this->comment) > 60) {
            $this->incomeErrors["error_comment"] = 'Nie podano kwoty';			
        }		
	}


   public function saveIncome($userId, $ammount, $datePicker, $categoryId, $comment)
		{
			$this->ammount = $ammount;
			$this->datePicker = $datePicker;
			$this->category = $categoryId;
			$this->comment = $comment;
									
			$this->validate();
			if (empty($this->incomeErrors))
			{
				try
				{
					$sql = 'INSERT INTO `incomes`(`user_id`,`ammount`, `date`, `category_id`, `comment`) VALUES (:userId,:ammount,:date,:categoryId,:comment)';
					$db = static::getDB();
					$stmt = $db->prepare($sql);
					$stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
					$stmt->bindValue(':ammount', $ammount, PDO::PARAM_STR);
					$stmt->bindValue(':date', $datePicker, PDO::PARAM_STR);
					$stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_STR);
					$stmt->bindValue(':comment', $comment, PDO::PARAM_STR);		
					return $stmt->execute();
				} catch (PDOException $e) {
					echo $e->getMessage();
				}
			}
			return false;
		}
	
	public function saveExpence($userId, $ammount, $datePicker, $categoryId, $comment, $payMethod)
		{
			$this->ammount = $ammount;
			$this->datePicker = $datePicker;
			$this->category = $categoryId;
			$this->comment = $comment;
			$this->payMethod = $payMethod;
									
			$this->validate();
			if (empty($this->expenceErrors))
			{
				try
				{
					$sql = 'INSERT INTO `expences`(`user_id`,`ammount`, `date`, `category_id`, `comment`, `pay_Method`) VALUES (:userId,:ammount,:date,:categoryId,:comment,:payMethod)';
					$db = static::getDB();
					$stmt = $db->prepare($sql);
					$stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
					$stmt->bindValue(':ammount', $ammount, PDO::PARAM_STR);
					$stmt->bindValue(':date', $datePicker, PDO::PARAM_STR);
					$stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_STR);
					$stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
					$stmt->bindValue(':payMethod', $payMethod, PDO::PARAM_STR);							
					return $stmt->execute();
				} catch (PDOException $e) {
					echo $e->getMessage();
				}
			}
			return false;
		}

    /**
     * Function
     *
     * @param array $incomes 
     *
     * @return sum of property 'ammount'
     */
	
	public static function getSumOfAmmount($incomes)
	{
		$sum = 0;

		foreach($incomes as $key=>$value)
		{
			if(isset($value['ammount']))   
			$sum += $value['ammount'];
		}
		return $sum;
	}
	
	private static function getCurrentDate()
	{
		return date('Y-m-d');
	}
	
	
	//get Incomes Data
	//returns Incomes Data
	//returns false if error
	public static function getIncomesData($userId, $startDate, $endDate)
    {
		if ($startDate==null) 
		{
			$startDate='2000-01-01';
		}
		if ($endDate==null) 
		{
			$endDate=Operations::getCurrentDate();
		}
			try
			{
				$sql = "SELECT * FROM incomes, income_categories WHERE incomes.category_id=income_categories.category_id AND incomes.user_id=:userId AND incomes.date BETWEEN :startDate AND :endDate";				
				$db = static::getDB();
				$stmt = $db->prepare($sql);
				$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
				$stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
				$stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);
				$stmt->execute();
				$results=$stmt->fetchAll(PDO::FETCH_ASSOC);
			return $results;
			} catch (PDOException $e) {
				echo $e->getMessage();
			}
		return false;
	}
	
	//get Expences Data
	//returns Expences Data
	//returns false if error
	public static function getExpencesData($userId, $startDate, $endDate)
    {
		if ($startDate==null) 
		{
			$startDate='2000-01-01';
		}
		if ($endDate==null) 
		{
			$endDate=Operations::getCurrentDate();
		}
			try
			{
				$sql = "SELECT * FROM expences, expence_categories WHERE expences.category_id=expence_categories.category_id AND expences.user_id=:userId AND expences.date BETWEEN :startDate AND :endDate";				
				$db = static::getDB();
				$stmt = $db->prepare($sql);
				$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
				$stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
				$stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);
				$stmt->execute();
				$results=$stmt->fetchAll(PDO::FETCH_ASSOC);
			return $results;
			} catch (PDOException $e) {
				echo $e->getMessage();
			}
		return false;
	}
	

	//*************************************
	//returns array for the Incomes data of Graph
	//*************************************
	public static function getIncomesGraphDate($unikalneKategoriePrzychodowNiezerowych, $sumaPrzychodow, $userId)
	{
		
		$daneWykresuPrzychodow = array();
		
			foreach ($unikalneKategoriePrzychodowNiezerowych as $k => $etykietaPrzychodow) {
				try
				{
					
					$sql = 'SELECT SUM(incomes.ammount) as total FROM incomes, income_categories WHERE incomes.user_id=:userId  AND income_categories.name=:category AND income_categories.category_id=incomes.category_id';
					$db = static::getDB();
					$stmt = $db->prepare($sql);
					$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
					$stmt->bindValue(':category', $etykietaPrzychodow, PDO::PARAM_STR);  //zmienić inwestycje na numer etykiety
					$stmt->execute();
					$results=$stmt->fetchAll(PDO::FETCH_ASSOC);
					$sumaPrzychodowDanejKategorii = $results;
					foreach($results as $key=>$value)
						{
							if(isset($value['total']))   
							$sum = $value['total'];
						}
					$przychodyWProcentach=round(($sum*100)/$sumaPrzychodow,2);
					$new_array=array("label"=>$etykietaPrzychodow, "y"=>$przychodyWProcentach);
					array_push($daneWykresuPrzychodow, $new_array);					
				} catch (PDOException $e) {
					echo $e->getMessage();
				}							
			}
		return $daneWykresuPrzychodow;
	}
	
	//*************************************
	//returns array for the Expences data of Graph
	//*************************************
	public static function getExpencesGraphDate($unikalneKategorieWydatkowNiezerowych, $sumaWydatkow, $userId)
	{
		
		$daneWykresuWydatkow = array();
		
			foreach ($unikalneKategorieWydatkowNiezerowych as $k => $etykietaWydatkow) {
				try
				{
					
					$sql = 'SELECT SUM(expences.ammount) as total FROM expences, expence_categories WHERE expences.user_id=:userId  AND expence_categories.name=:category AND expence_categories.category_id=expences.category_id';
					$db = static::getDB();
					$stmt = $db->prepare($sql);
					$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
					$stmt->bindValue(':category', $etykietaWydatkow, PDO::PARAM_STR);  //zmienić inwestycje na numer etykiety
					$stmt->execute();
					$results=$stmt->fetchAll(PDO::FETCH_ASSOC);
					$sumaWydatkowDanejKategorii = $results;
					foreach($results as $key=>$value)
						{
							if(isset($value['total']))   
							$sum = $value['total'];
						}
					$wydatkiWProcentach=round(($sum*100)/$sumaWydatkow,2);
					$new_array=array("label"=>$etykietaWydatkow, "y"=>$wydatkiWProcentach);
					array_push($daneWykresuWydatkow, $new_array);					
				} catch (PDOException $e) {
					echo $e->getMessage();
				}							
			}
		return $daneWykresuWydatkow;
	}
	
		
}



