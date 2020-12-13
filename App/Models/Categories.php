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

	//get payMethod categories - it is without "nieskategoryzowane" category
	public static function getPayMethodCategoriesForNewPayMethod($userId)
    {
		try
		{
			$sql = 'SELECT * FROM `pay_method_categories` WHERE user_Id=:userId AND pay_method_name<>"Nieskategoryzowane"';
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
	private static function addExpenceCategoryForUserId($userId, $expenceName, $maxLimit)
	{
		$sql = 'INSERT INTO expence_categories (name, user_Id, max)
                    VALUES (:name, :userId, :maxLimit)';
                                              
            $db = static::getDB();
            $stmt = $db->prepare($sql);			
			$stmt->bindValue(':name', $expenceName, PDO::PARAM_STR);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
			$stmt->bindValue(':maxLimit', $maxLimit, PDO::PARAM_INT);			
            return $stmt->execute();
	}
	
	//Adds Expences Category for specified userId
	private static function addPayMethodCategoryForUserId($userId, $payMethodName)
	{
		$sql = 'INSERT INTO pay_method_categories (name, user_Id)
                    VALUES (:name, :userId)';
                                              
            $db = static::getDB();
            $stmt = $db->prepare($sql);			
			$stmt->bindValue(':name', $payMethodName, PDO::PARAM_STR);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);			
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
		try
		{
			$sql = 'DELETE FROM `income_categories` WHERE user_id=:userId AND name=:categoryName';                                              
            $db = static::getDB();
            $stmt = $db->prepare($sql);			
			$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);  			
			return $stmt->execute() !== false;
		} catch (PDOException $e) 
		{
			echo $e->getMessage();
			return false;
		}
		return false;
	}
	
	//Removes Expence Category for Selected user ID
	public static function removeExpenceCategory($userId, $categoryName)
	{
		try
		{
			$sql = 'DELETE FROM `expence_categories` WHERE user_id=:userId AND name=:categoryName';                                              
            $db = static::getDB();
            $stmt = $db->prepare($sql);			
			$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);  			
			return $stmt->execute() !== false;
		} catch (PDOException $e) 
		{
			echo $e->getMessage();
			return false;
		}
		return false;
	}
	
	//Removes pay Method Category for Selected user ID
	public static function removePayMethodCategory($userId, $categoryName)
	{
		try
		{
			$sql = 'DELETE FROM `pay_method_categories` WHERE user_id=:userId AND name=:categoryName';                                              
            $db = static::getDB();
            $stmt = $db->prepare($sql);			
			$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);  			
			return $stmt->execute() !== false;
		} catch (PDOException $e) 
		{
			echo $e->getMessage();
			return false;
		}
		return false;
	}
	


	//Check whether income category exists or not
	private static function incomeCategoryExists($categoryName, $userId)
    {
        try 
		{
			$sql = 'SELECT * FROM `income_categories` WHERE name=:categoryName AND user_id=:userId';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);		
			$stmt->execute();
			return $stmt->fetch() !== false;
		} catch (PDOException $e) 
		{
			echo $e->getMessage();
			return false;
		}
		return false;
    }
	
	//Check whether expence category exists or not
	private static function expenceCategoryExists($categoryName, $userId)
    {
        try 
		{
			$sql = 'SELECT * FROM `expence_categories` WHERE name=:categoryName AND user_id=:userId';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);		
			$stmt->execute();
			return $stmt->fetch() !== false;
		} catch (PDOException $e) 
		{
			echo $e->getMessage();
			return false;
		}
		return false;
    }
	
	//Check whether pay Method category exists or not
	private static function payMethodCategoryExists($categoryName, $userId)
    {
        try 
		{
			$sql = 'SELECT * FROM `pay_method_categories` WHERE name=:categoryName AND user_id=:userId';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);		
			$stmt->execute();
			return $stmt->fetch() !== false;
		} catch (PDOException $e) 
		{
			echo $e->getMessage();
			return false;
		}
		return false;
    }
	

	//Adds Income Category for Selected user ID
	public static function addNewIncomeCategory($userId, $categoryName, $maxLimit)
	{
			$istnieje = Categories:: incomeCategoryExists($categoryName, $userId);
			if($istnieje)
			{
				return false;
			} else {
				$addNewCategories = Categories::addIncomeCategoryForUserId($userId, $categoryName, $maxLimit);				
				return true;
			}			
	}
	
	//Adds Expence Category for Selected user ID
	public static function addNewExpenceCategory($userId, $categoryName, $maxLimit)
	{
			$istnieje = Categories:: expenceCategoryExists($categoryName, $userId);
			if($istnieje)
			{
				return false;
			} else {
				$addNewCategories = Categories::addExpenceCategoryForUserId($userId, $categoryName, $maxLimit);				
				return true;
			}			
	}
	
	//Adds new Pay Method Category for Selected user ID
	public static function addNewPayMethodCategory($userId, $categoryName)
	{
			$istnieje = Categories:: payMethodCategoryExists($categoryName, $userId);
			if($istnieje)
			{
				return false;
			} else {
				$addNewCategories = Categories::addPayMethodCategoryForUserId($userId, $categoryName);				
				return true;
			}			
	}
	
	
	//Update Income Category, returns false when error
	public static function updateIncomeCategory($oldCategoryName,$newCategoryName,$maxLimit,$userId)
    {
		//$istnieje = Categories:: incomeCategoryExists($newCategoryName, $userId);
		if($oldCategoryName!=$newCategoryName){
			$istnieje = Categories:: incomeCategoryExists($newCategoryName, $userId);
			if($istnieje) return false;
		};
		
		try
		{
			$sql = 'UPDATE `income_categories` SET `name`=:newCategoryName,`max`=:maxLimit WHERE `user_id`=:userId AND `name`=:oldCategoryName';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':oldCategoryName', $oldCategoryName, PDO::PARAM_STR);
			$stmt->bindValue(':newCategoryName', $newCategoryName, PDO::PARAM_STR);
			$stmt->bindValue(':maxLimit', $maxLimit, PDO::PARAM_STR);
			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);		
			return $stmt->execute();
		} catch (PDOException $e) 
			{
				echo $e->getMessage();
			}
		return false;
    }
	
	//Update Expence Category, returns false when error
	public static function updateExpenceCategory($oldCategoryName,$newCategoryName,$maxLimit,$userId)
    {
		//$istnieje = Categories:: expenceCategoryExists($newCategoryName, $userId);
		if($oldCategoryName!=$newCategoryName){
			$istnieje = Categories:: expenceCategoryExists($newCategoryName, $userId);
			if($istnieje) return false;
		};
		
		try
		{
			$sql = 'UPDATE `expence_categories` SET `name`=:newCategoryName,`max`=:maxLimit WHERE `user_id`=:userId AND `name`=:oldCategoryName';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':oldCategoryName', $oldCategoryName, PDO::PARAM_STR);
			$stmt->bindValue(':newCategoryName', $newCategoryName, PDO::PARAM_STR);
			$stmt->bindValue(':maxLimit', $maxLimit, PDO::PARAM_STR);
			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);		
			return $stmt->execute();
		} catch (PDOException $e) 
			{
				echo $e->getMessage();
			}
		return false;
    }
	
	//Update payMethod Category, returns false when error
	public static function updatePayMethodCategory($oldCategoryName,$newCategoryName,$maxLimit,$userId)
    {
		if($oldCategoryName!=$newCategoryName){
			$istnieje = Categories:: payMethodCategoryExists($newCategoryName, $userId);
			//$istnieje = false;
			if($istnieje) return false;
		};
		
		try
		{
			$sql = 'UPDATE `pay_method_categories` SET `name`=:newCategoryName WHERE `user_id`=:userId AND `name`=:oldCategoryName';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':oldCategoryName', $oldCategoryName, PDO::PARAM_STR);
			$stmt->bindValue(':newCategoryName', $newCategoryName, PDO::PARAM_STR);
			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);		
			return $stmt->execute();
		} catch (PDOException $e) 
			{
				echo $e->getMessage();
			}
		return false;
    }
	
	// gets category_id for selected category Name (incomes)
	public static function getIncomeCategoryId($categoryName,$userId) {		
		try
		{
			$sql = 'SELECT `category_id` FROM `income_categories` WHERE `name`=:categoryName AND `user_id`=:userId';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
			$stmt->execute();
			$results=$stmt->fetchColumn();
			return $results;
		} catch (PDOException $e) {
            echo $e->getMessage();
			return false;
        }
		return false;
	}
	
		// sets category_id for selected category Name (incomes). If error returns false.
	public static function setIncomeCategoryId($categoryName,$userId,$newId) {		

		try
		{
			$sql = 'UPDATE `income_categories` SET `category_id`=:newId WHERE `user_id`=:userId AND `name`=:categoryName';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
			$stmt->bindValue(':newId', $newId, PDO::PARAM_INT);
			$stmt->execute();
			return true;
		} catch (PDOException $e) {
            echo $e->getMessage();
			return false;
        }
		return false;
	}
	
	// gets category_id for selected category Name (expences)
	public static function getExpenceCategoryId($categoryName,$userId) {		
		try
		{
			$sql = 'SELECT `category_id` FROM `expence_categories` WHERE `name`=:categoryName AND `user_id`=:userId';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
			$stmt->execute();
			$results=$stmt->fetchColumn();
			return $results;
		} catch (PDOException $e) {
            echo $e->getMessage();
			return false;
        }
		return false;
	}
	
	// sets category_id for selected category Name (expences). If error returns false.
	public static function setExpenceCategoryId($categoryName,$userId, $newId) {
		try
		{
			$sql = 'UPDATE `expence_categories` SET `category_id`=:newId WHERE `user_id`=:userId AND `name`=:categoryName';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
			$stmt->bindValue(':newId', $newId, PDO::PARAM_INT);
			$stmt->execute();
			return true;
		} catch (PDOException $e) {
            echo $e->getMessage();
			return false;
        }
		return false;
	}
	
	// gets category_id for selected category Name (payMethod)
	public static function getPayMethodCategoryId($categoryName,$userId) {		
		try
		{
			$sql = 'SELECT `category_id` FROM `pay_method_categories` WHERE `name`=:categoryName AND `user_id`=:userId';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
			$stmt->execute();
			$results=$stmt->fetchColumn();
			return $results;
		} catch (PDOException $e) {
            echo $e->getMessage();
			return false;
        }
		return false;
	}
	
	// sets category_id for selected category Name (payMethod). If error returns false.
	public static function setPayMethodCategoryId($categoryName,$userId, $newId) {
		try
		{
			$sql = 'UPDATE `pay_method_categories` SET `category_id`=:newId WHERE `user_id`=:userId AND `name`=:categoryName';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
			$stmt->bindValue(':newId', $newId, PDO::PARAM_INT);
			$stmt->execute();
			return true;
		} catch (PDOException $e) {
            echo $e->getMessage();
			return false;
        }
		return false;
	}
	


	
}
