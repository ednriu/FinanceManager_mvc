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

	
	public static function getExpenseCategories()
    {
        $sql = 'SELECT * FROM `expence_categories` WHERE 1';

        $db = static::getDB();
        $stmt = $db->query($sql);
		$results=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
	
	public static function getIncomeCategories()
    {
        $sql = 'SELECT * FROM `income_categories` WHERE 1';

        $db = static::getDB();
        $stmt = $db->query($sql);
		$results=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results;  
	}

}
