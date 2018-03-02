<?php

class Validator
{
    /**
     * To validate name
     * @param $person_name - the name of the person
     * @return int - boolean value
     */
    static function _nameOfPerson($person_name){
        $reg = "/^[a-z ,.'-]+$/i";
        return preg_match($reg,$person_name);
    }

    /**
     * To validate email
     * @param $value - email id
     * @return int - boolean value
     */
    static function _emailMatch($value){
        $reg = '/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/';
        return preg_match($reg,$value);
    }

    /**
     * To check strength of password (more than 4 ,should contain atleast one capital letter and one number)
     * @param $value - the password
     * @return int - boolean value
     */
    static function _passwordStrength($value){
        $reg = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{4,8}$/';
        return preg_match($reg,$value);
    }


}