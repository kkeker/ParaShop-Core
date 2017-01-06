<?php
/**
 * Created by PhpStorm.
 * User: kkeker
 * Date: 05.01.2017
 * Time: 20:39
 */

namespace kkeker\ParaShop;

use CouchbaseCluster;
use CouchbaseN1qlQuery;
use Respect\Validation\Validator as v;

/**
 * @Stateless
 * @Processing("exception")
 */
class EditUserSrvice
{
    protected $lastName;
    protected $sex;
    protected $skill;
    protected $country;
    protected $city;
    protected $about;
}