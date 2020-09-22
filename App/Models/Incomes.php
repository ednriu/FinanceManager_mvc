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

   

   public static function saveIncome($userId, $ammount, $date, $categoryId, $comment)
    {
        $sql = 'INSERT INTO `incomes`(`user_id`,`ammount`, `date`, `category_id`, `comment`) VALUES (:userId,:ammount,:date,:categoryId,:comment)';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
		$stmt->bindValue(':ammount', $ammount, PDO::PARAM_STR);
		$stmt->bindValue(':date', $date, PDO::PARAM_STR);
		$stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_STR);
		$stmt->bindValue(':comment', $comment, PDO::PARAM_STR);		
		return $stmt->execute();
	}
	


}
