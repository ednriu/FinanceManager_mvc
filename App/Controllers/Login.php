<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class Login extends \Core\Controller
{

    /**
     * Show the signup page
     *
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Login/New.html');
    }

    /**
     * Sign up a new user
     *
     * @return void
     */
    public function createAction()
    {
        $user = User::authenticate($_POST['login'], $_POST['password']);
		
		if ($user) {
			$_SESSION['name'] = $user->name;
			$_SESSION['user_id'] = $user->user_id;
			$this->redirect('/Main/showMainReport');
		} else {
			View::renderTemplate('Login/New.html',[
			'login'=>$_POST['login']],);
		}
    }

    /**
     * Logout User
     *
     * @return void
     */
	
	public function logoutAction()
    {
		unset($_SESSION['name']);
		unset($_SESSION['user_id']);
        View::renderTemplate('Login/New.html');
    }
}
