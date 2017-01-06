<?php
/**
 * Created by PhpStorm.
 * User: kkeker
 * Date: 05.01.2017
 * Time: 13:46
 */

namespace kkeker\ParaShop;

use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * @Route(name="adduser", urlPattern={"/adduser.do", "/adduser.do*"}, initParams={})
 */
class AddUserServlet extends HttpServlet
{
    /**
     * @EnterpriseBean(name="AdduserService")
     */
    protected $addService;

    /**
     * @param HttpServletRequestInterface $servletRequest
     * @param HttpServletResponseInterface $servletResponse
     */
    public function doPost(
        HttpServletRequestInterface $servletRequest,
        HttpServletResponseInterface $servletResponse
    )
    {
        $result = $this->addService->add($this->data);
        return $result;
    }
}