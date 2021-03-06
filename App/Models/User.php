<?php

namespace App\Models;

use PDO;

/**
 * User model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{

    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

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
    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users (login, password, name, email)
                    VALUES (:login, :password, :name, :email)';
                                              
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            
			$stmt->bindValue(':login', $this->login, PDO::PARAM_STR);
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $password_hash, PDO::PARAM_STR);
                                          
            return $stmt->execute();
        }

        return false;
    }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validate()
    {
        //Login
		if ($this->loginExists($this->login)) {
            $this->errors["error_login"] = 'podany login istnieje, spróbuj inny';
        }
		
		if (strlen($this->login)<6) {
            $this->errors["error_login"] = 'login powinien zawierać conajmniej 6 znaków';
        }
		
		if ($this->login == '') {
            $this->errors["error_login"] = 'nie podano loginu';
        }
		
		// Name
        if ($this->name == '') {
            $this->errors["error_name"] = 'nie podano imienia';
        }

        // email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors["error_email"] = 'błędny format adresu email';
        }
        if ($this->emailExists($this->email)) {
            $this->errors["error_email"] = 'adres email istnieje w naszej bazie danych';
        }

        // Password
        if ($this->password != $this->password_confirmation) {
            $this->errors["error_password"] = 'Brak weryfiikacji hasła';
        }

        if (strlen($this->password) < 6) {
            $this->errors["error_password"] = 'hasło powinno zawierać conajmniej 6 znaków';
        }

        if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
            $this->errors["error_password"] = 'hasło powinno zawierać conajmniej jedną literę';
        }

        if (preg_match('/.*\d+.*/i', $this->password) == 0) {
            $this->errors["error_password"] = 'hasło powinno zawierać conajmniej jedną cyfrę';
        }
		
		//Recaptcha
		$sekret = "6LdEmLAZAAAAABGVjHf3l3LGJBaVxHZeN2olnPw4";		
		$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);		
		$odpowiedz = json_decode($sprawdz);		
		if ($odpowiedz->success==false)
		{
			$this->errors["error_recaptcha"] = 'Recaptcha not verified';
		}
    }

   /**
     * Validates Password Only
     *
     * @return void
     */
    public function validatePasswordOnly()
    {		
	
		// Password
        if ($this->newPassword != $this->passwordConfirmation) {
            $this->errors["password_error"] = 'Brak weryfiikacji hasła';
			return false;
        }
	
        if (strlen($this->newPassword) < 6) {
            $this->errors["password_error"] = 'hasło powinno zawierać conajmniej 6 znaków';
			return false;
        }

        if (preg_match('/.*[a-z]+.*/i', $this->newPassword) == 0) {
            $this->errors["password_error"] = 'hasło powinno zawierać conajmniej jedną literę';
			return false;
        }

        if (preg_match('/.*\d+.*/i', $this->newPassword) == 0) {
            $this->errors["password_error"] = 'hasło powinno zawierać conajmniej jedną cyfrę';
			return false;
        }
		return true;
    }
	
	   /**
     * Validates Email Only
     *
     * @return void
     */
    public function validateEmailOnly()    {	
	    // email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors["email_error"] = 'błędny format adresu email';
			return false;
        }
		return true;
    }



    /**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     *
     * @return boolean  True if a record already exists with the specified email, false otherwise
     */
	 
 
    protected function emailExists($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch() !== false;
    }
	
	protected function loginExists($login)
    {
        $sql = 'SELECT * FROM users WHERE login = :login';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':login', $login, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch() !== false;
    }
	
	    
	public static function findByLogin($login)
    {
        $sql = 'SELECT * FROM users WHERE login = :login';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':login', $login, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }
	
	public static function authenticate($login, $password)
	{
		$user = static::findByLogin($login);
		 if ($user) {
            if (password_verify($password, $user->password)) {
                return $user;
            }
        }
		return false;
	}
	
	//updates password for $userID.
	public function updatePassword($newPassword, $passwordConfirmation, $userId)
	{	
		$this->newPassword = $newPassword;
		$this->passwordConfirmation = $passwordConfirmation;

        if ($this->validatePasswordOnly()) {		
			$sql = 'UPDATE `users` SET `password`=:newPassword WHERE `user_id`=:userId';
			$db = static::getDB();
			$password_hash = password_hash($newPassword, PASSWORD_DEFAULT);
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':newPassword', $password_hash, PDO::PARAM_STR);
			$stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
			return $stmt->execute();			
		};
		return false;
	}
	
	//gets user name and email for $userId
	public function getUserDataInfo($userId)
	{		
			$sql = 'SELECT `name`, `email` FROM `users` WHERE `user_id`=:userId';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
			$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
	}
	
	//updates user's name and email for $userID.
	public function updateUserDataInfo($name, $email, $userId)
	{
		$this->email = $email;
        if ($this->validateEmailOnly()) {		
			$sql = 'UPDATE `users` SET `name`=:name,`email`=:email  WHERE `user_id`=45';
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':name', $name, PDO::PARAM_STR);
			$stmt->bindParam(':email', $email, PDO::PARAM_STR);
			return $stmt->execute();			
		};
		return false;
	}

}
