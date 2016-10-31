<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('EncryptPassw'))
{
    /**
    * Esse método criptografa a senha
    * @package helpers
    * @subpackage Password Services
    * @param string $passw Senha
    * @return string
    */
    function EncryptPassw($passw)
    {
        if(is_null($passw))
        return null;

        $passw_encrypted = password_hash($passw, PASSWORD_BCRYPT);
        if($passw_encrypted)
        return $passw_encrypted;
        else
        return $passw;
    }
}

if ( ! function_exists('DecryptPassw'))
{
    /**
    * Esse método verifica se as senhas são iguais
    * @package helpers
    * @subpackage Password Services
    * @param string $passw Senha do usuário sem criptografia
    * @param string $passw_encrypted Senha do usuário criptografada
    * @return boolean
    */
    function DecryptPassw($passw, $passw_encrypted)
    {

        if(is_null($passw) || is_null($passw_encrypted))
        return false;

        $verify = password_verify($passw,$passw_encrypted);
        return $verify;
    }
}
