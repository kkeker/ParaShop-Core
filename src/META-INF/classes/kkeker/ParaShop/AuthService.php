<?php
/**
 * Created by PhpStorm.
 * User: kkeker
 * Date: 28.12.2016
 * Time: 22:57
 */

namespace kkeker\ParaShop;

use CouchbaseCluster;
use CouchbaseN1qlQuery;
use Respect\Validation\Validator as v;

/**
 * @Stateless
 * @Processing("exception")
 */
class AuthService
{
    protected $username;
    protected $password;

    /**
     * @Requires(type="RespectValidation", constraint="v::not(v::email()->setName('Username'))->check($username)")
     */
    protected function setUsername($username)
    {
        $this->username = $username;
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

        $query = CouchbaseN1qlQuery::fromString("select `login`,`password` from `users` where `login` = '$this->username'");
        $result = $bucket->query($query);

        if ($result->status == 'success') {
            if ($result->metrics['resultCount'] > 0) {
                if ($result->rows[0]->login == $this->username && $result->rows[0]->password == $this->password) {
                    return $this->username;
                }
            }
        }
        throw new \Exception('Неверное сочетание Логина и Пароля', 401);
    }

    public function login($credentials)
    {
        $this->setUsername($credentials->username);
        $this->setPassword($credentials->password);
        return $this->auth();
    }
}