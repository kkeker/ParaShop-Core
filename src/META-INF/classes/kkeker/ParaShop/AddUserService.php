<?php
/**
 * Created by PhpStorm.
 * User: kkeker
 * Date: 01.01.2017
 * Time: 20:27
 */

namespace kkeker\ParaShop;

use CouchbaseCluster;
use CouchbaseN1qlQuery;
use Respect\Validation\Validator as v;

/**
 * @Stateless
 * @Processing("exception")
 */
class AddUserService
{
    protected $login;
    protected $password;
    protected $firstName;
    protected $email;

    /**
     * @Requires(type="RespectValidation", constraint="v::not(v::email()->setName('Login'))->check($login)")
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

    /**
     * @Requires(type="RespectValidation", constraint="v::email()->setName('EMail')->check($email)")
     */
    protected function setMail($email)
    {
        $this->email = $email;
    }

    /**
     * @Requires(type="RespectValidation", constraint="v::notEmpty()->setName('First Name')->check($firstName)")
     */
    protected function setName($firstName)
    {
        $this->firstName = $firstName;
    }

    protected function create()
    {
        $settings = new Settings();
        $cluster = new CouchbaseCluster($settings->couchUrl);
        $bucket = $cluster->openBucket($settings->couchUsersBucker);

        $query = CouchbaseN1qlQuery::fromString("select `login` from `users` where `login` = '$this->login'");
        $result = $bucket->query($query);

        if ($result->status == 'success') {
            if ($result->metrics['resultCount'] == 0) {
                if (empty($result->rows[0]->login)) {

                    $new_user = array(
                        'login' => $this->login,
                        'mail' => $this->email,
                        'firtsname' => $this->firstName,
                        'password' => $this->password,
                    );

                    $bucket->upsert($this->login, $new_user);

                    array_pop($new_user);
                    return $new_user;
                }
            }
        }
        throw new \Exception('Пользователь с таким Логином уже существует', 406);
    }

    public function add($credentials)
    {
        $this->setLogin($credentials->login);
        $this->setPassword($credentials->password);
        $this->setMail($credentials->email);
        $this->setName($credentials->firstName);
        return $this->create();
    }
}