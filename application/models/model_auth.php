<?php
class Model_Auth extends Model{

    public $result = '';


    public function authorize($login,$password)
    {
        if($login=='admin'&&$password=='123'){
            session_start();
            $_SESSION['login'] = $login;
            return true;
        }
        else{
           return false;
        }
    }



}