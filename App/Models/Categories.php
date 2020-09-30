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
	
    /**
     * Save the user model with the current property values
     *
     * @return boolean  True if the user was saved, false otherwise
     */

	//poniższa funkcja idzie do poprawy na podstawie analogii do wpływów
	public static function getExpenseCategories()
    {
		try
		{
			$sql = 'SELECT * FROM `expence_categories` WHERE 1';
			$db = static::getDB();
			$stmt = $db->query($sql);
			$results=$stmt->fetchAll(PDO::FETCH_ASSOC);
			return $results;
		} catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
	
	public static function getIncomeCategories($userId)
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
	
	public function getNotEmptyIncomeCategories($userId)
	{
		try
		{
			$sql = 'SELECT income_categories.name FROM `income_categories`,`incomes` WHERE incomes.user_Id=:userId AND incomes.category_id=income_categories.category_id';
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
	
}
