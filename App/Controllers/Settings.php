<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Categories;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class Settings extends \Core\Controller
{

    /**
     * Show the signup page
     *
     * @return void
     */
    public function incomeCategoriesSettingsAction()
    {
		$incomeCategories = Categories::getIncomeCategoriesForNewIncome($_SESSION['user_id']);
        View::renderTemplate('Settings/application_settings.html',['option'=>1, 'incomeCategories'=>$incomeCategories]);
    }
	
	    public function expenceCategoriesSettingsAction()
    {
        View::renderTemplate('Settings/application_settings.html',['option'=>2]);
    }
	
	    public function payMethodSettingsAction()
    {
        View::renderTemplate('Settings/application_settings.html',['option'=>3]);
    }


}