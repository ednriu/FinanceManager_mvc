<?php

namespace App\Models;

use PDO;

/**
 * Categories model
 *
 * PHP version 7.0
 */
class Incomes extends \Core\Model
{
	
	public $incomeErrors = [];

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
	
	public static function getIncomes($userId)
    {		

			try
			{
				$sql = 'SELECT * FROM incomes, income_categories WHERE incomes.category_id=income_categories.category_id AND incomes.user_id=:numer';				
				$db = static::getDB();
				$stmt = $db->prepare($sql);
				$stmt->bindValue(':numer', $userId, PDO::PARAM_INT);
				$stmt->execute();
				$results=$stmt->fetchAll(PDO::FETCH_ASSOC);
			return $results;
			} catch (PDOException $e) {
				echo $e->getMessage();
			}
		return false;
	}
	
}



