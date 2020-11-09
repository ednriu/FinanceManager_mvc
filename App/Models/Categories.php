<?php

namespace App\Models;

use PDO;

/**
 * Categories model
 *
 * PHP version 7.0
 */
class Categories extends \Core\Model
{


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
	
	//get all expence categories together with "nieskategoryzowane"
	public static function getAllExpenceCategories($userId)
    {
		try
		{
			$sql = 'SELECT * FROM `expence_categories` WHERE user_Id=:userId';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
			$stmt->execute();
			$results=$stmt->fetchAll(PDO::FETCH_ASSOC);
			return $results;
		} catch (PDOException $e) {
            echo $e->getMessage();
        }
	}
	
	//get expence categories for add axpence form - it is without "nieskategoryzowane" category
	public static function getExpenceCategoriesForNewExpence($userId)
    {
		try
		{
			$sql = 'SELECT * FROM `expence_categories` WHERE user_Id=:userId AND Name<>"Nieskategoryzowane"';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
			$stmt->execute();
			$results=$stmt->fetchAll(PDO::FETCH_ASSOC);
			return $results;
		} catch (PDOException $e) {
            echo $e->getMessage();
        }
	}
	
	//get income categories for add axpence form - it is without "nieskategoryzowane" category
	public static function getIncomeCategoriesForNewIncome($userId)
    {
		try
		{
			$sql = 'SELECT * FROM `income_categories` WHERE user_Id=:userId AND Name<>"Nieskategoryzowane"';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
			$stmt->execute();
			$results=$stmt->fetchAll(PDO::FETCH_ASSOC);
			return $results;
		} catch (PDOException $e) {
            echo $e->getMessage();
        }
	}	
	
	//get all income categories together with "nieskategoryzowane"
	public static function getAllIncomeCategories($userId)
    {
		try
		{
			$sql = 'SELECT * FROM `income_categories` WHERE user_Id=:userId';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
			$stmt->execute();
			$results=$stmt->fetchAll(PDO::FETCH_ASSOC);
			return $results;
		} catch (PDOException $e) {
            echo $e->getMessage();
        }
	}
	
	public static function getNotEmptyIncomeCategories($userId)
	{
		try
		{
			$sql = 'SELECT income_categories.name FROM `income_categories`,`incomes` WHERE incomes.user_Id=:userId AND incomes.category_id=income_categories.category_id';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
			$stmt->execute();
			$results=$stmt->fetchAll(PDO::FETCH_ASSOC);
			$result2 = array_column($results, 'name');
			return $result2;
		} catch (PDOException $e) {
            echo $e->getMessage();
        }
	}
	
		public static function getNotEmptyExpenceCategories($userId)
	{
		try
		{
			$sql = 'SELECT expence_categories.name FROM `expence_categories`,`expences` WHERE expences.user_Id=:userId AND expences.category_id=expence_categories.category_id';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
			$stmt->execute();
			$results=$stmt->fetchAll(PDO::FETCH_ASSOC);
			$result2 = array_column($results, 'name');
			return $result2;
		} catch (PDOException $e) {
            echo $e->getMessage();
        }
	}
	
	public static function my_array_unique($array, $keep_key_assoc = false)
	{
		$duplicate_keys = array();
		$tmp         = array();       

		foreach ($array as $key=>$val)
		{
			// convert objects to arrays, in_array() does not support objects
			if (is_object($val))
				$val = (array)$val;

			if (!in_array($val, $tmp))
				$tmp[] = $val;
			else
				$duplicate_keys[] = $key;
		}

		foreach ($duplicate_keys as $key)
			unset($array[$key]);

		return $keep_key_assoc ? $array : array_values($array);
	}
	
	//Adds Incomes Category for specified userId
	private static function addIncomeCategoryForUserId($userId, $incomeName, $maxLimit)
	{
		$sql = 'INSERT INTO income_categories (name, user_Id, max)
                    VALUES (:name, :userId, :maxLimit)';
                                              
            $db = static::getDB();
            $stmt = $db->prepare($sql);			
			$stmt->bindValue(':name', $incomeName, PDO::PARAM_STR);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT); 
			$stmt->bindValue(':maxLimit', $maxLimit, PDO::PARAM_INT);
            return $stmt->execute();
	}           
	

	
	//Adds Expences Category for specified userId
	private static function addExpenceCategoryForUserId($userId, $incomeName, $maxLimit)
	{
		$sql = 'INSERT INTO expence_categories (name, user_Id, max)
                    VALUES (:name, :userId, :maxLimit)';
                                              
            $db = static::getDB();
            $stmt = $db->prepare($sql);			
			$stmt->bindValue(':name', $incomeName, PDO::PARAM_STR);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
			$stmt->bindValue(':maxLimit', $maxLimit, PDO::PARAM_INT);			
            return $stmt->execute();
	}
	
	//Creates initial income categories for new user
	public function createIncomeCategoriesForNewUser($userId)
	{
		$initIncomeCategories = array(0=>"Nieskategoryzowane", 1=>"Odsetki", 2=>"Dodatkowa Praca", 3=>"Inwestycje", 4=>"Wypłata");
		foreach ($initIncomeCategories as $value)
		{
			$addNewCategories = Categories::addIncomeCategoryForUserId($userId, $value, 1000);
		}
	}
	
	//Creates initial expence categories for new user
	public function createExpenceCategoriesForNewUser($userId)
	{
		$initIncomeCategories = array(0=>"Nieskategoryzowane", 1=>"Jedzenie", 2=>"Samochód", 3=>"Rachunki", 4=>"Dom");
		foreach ($initIncomeCategories as $value)
		{
			$addNewCategories = Categories::addExpenceCategoryForUserId($userId, $value, 1000);
		}
	}
	
	//Removes Income Category for Selected user ID
	public static function removeIncomeCategory($userId, $categoryName)
	{
		$sql = 'DELETE FROM `income_categories` WHERE user_id=:userId AND name=:categoryName';
                                              
            $db = static::getDB();
            $stmt = $db->prepare($sql);			
			$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);  			
			$stmt->execute();
	}
	


	//Check whether income category exists or not
	public static function incomeCategoryExists($categoryName, $userId)
    {
        //$sql = 'SELECT * FROM `income_categories` WHERE name=:categoryName AND user_id=:userId';

        //$db = static::getDB();
        //$stmt = $db->prepare($sql);
        //$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
        //$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);		
        //$stmt->execute();
        //return $stmt->fetch() !== false;
		return 2;
    }
	

	//Adds Income Category for Selected user ID
	public static function addNewIncomeCategory($userId, $categoryName, $maxLimit)
	{
			$addNewCategories = Categories::addIncomeCategoryForUserId($userId, $categoryName, $maxLimit);
			return true;

	}
	

	
}
