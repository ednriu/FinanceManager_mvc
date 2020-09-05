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
        View::renderTemplate('login/new.html');
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
			$this->redirect('/Login/success');
		} else {
			View::renderTemplate('Login/new.html',[
			'login'=>$_POST['login']],);
		}
    }

    /**
     * Show the signup success page
     *
     * @return void
     */
    public function successAction()
    {
        View::renderTemplate('Login/success.html');
    }
}
