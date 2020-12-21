<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Models\Categories;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class Signup extends \Core\Controller
{

    /**
     * Show the signup page
     *
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Signup/New.html');
    }

    /**
     * Sign up a new user
     *
     * @return void
     */
    public function createAction()
    {
        $user = new User($_POST);

        if ($user->save()) {
			$signedUserId = User::findByLogin($user->login);
			$newCategories = new Categories();
			$newCategories->createIncomeCategoriesForNewUser($signedUserId->user_id);
			$newCategories->createExpenceCategoriesForNewUser($signedUserId->user_id);
			$newCategories->createPayMethodCategoriesForNewUser($signedUserId->user_id);
            $this->redirect('/Signup/Success');
        } else {

            View::renderTemplate('Signup/New.html', [
                'user' => $user
            ]);

        }
    }

    /**
     * Show the signup success page
     *
     * @return void
     */
    public function successAction()
    {
        View::renderTemplate('Signup/Success.html');
    }
}
