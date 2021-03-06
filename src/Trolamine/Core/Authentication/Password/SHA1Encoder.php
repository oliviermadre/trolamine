<?php
namespace Trolamine\Core\Authentication\Password;

class SHA1Encoder implements PasswordEncoder
{

    /**
     *
     * @param  string $password
     * @return string
     */
    public function encodePassword($password, $salt = null)
    {
        return sha1($password);
    }
}
