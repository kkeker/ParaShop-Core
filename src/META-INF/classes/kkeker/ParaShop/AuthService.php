<?php
/**
 * Created by PhpStorm.
 * User: kkeker
 * Date: 28.12.2016
 * Time: 22:57
 */

namespace kkeker\ParaShop;

use CouchbaseCluster;
use Respect\Validation\Validator as v;

/**
 * @Stateless
 * @Processing("exception")
 */
class AuthService
{
    protected $login;
    protected $password;

    /**
     * @Requires(type="RespectValidation", constraint="v::not(v::email()->setName('Username'))->check($login)")
     */
    protected function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @Requires(type="RespectValidation", constraint="v::notEmpty()->setName('Password')->check($password)")
     */
    protected function setPassword($password)
    {
        $this->password = $password;
    }

    protected function auth()
    {
        $settings = new Settings();
        $cluster = new CouchbaseCluster($settings->couchUrl);
        $bucket = $cluster->openBucket($settings->couchUsersBucker);

        $result = $bucket->get($this->login);

        if (is_null($result->error)) {
            if ($result->value->login == $this->login && $result->value->password == $this->password) {

                $result->value->lastLogon = time();
                $bucket->replace($this->login, $result->value);

                return array (
                    'login' => $this->login,
                    'lastLogon' => $result->value->lastLogon,
                );
            }
        }
        throw new \Exception('Неверное сочетание Логина и Пароля', 401);
    }

    public function login($credentials)
    {
        $this->setLogin($credentials->login);
        $this->setPassword($credentials->password);

        return $this->auth();
    }
}