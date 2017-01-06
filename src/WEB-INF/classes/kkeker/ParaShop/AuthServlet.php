<?php
/**
 * Created by PhpStorm.
 * User: kkeker
 * Date: 28.12.2016
 * Time: 22:57
 */

namespace kkeker\ParaShop;

use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * @Route(name="login", urlPattern={"/login.do", "/login.do*"}, initParams={})
 */
class AuthServlet extends HttpServlet
{
    /**
     * @EnterpriseBean(name="AuthService")
     */
    protected $authService;

    /**
     * @param HttpServletRequestInterface $servletRequest
     * @param HttpServletResponseInterface $servletResponse
     */
    public function doPost(
        HttpServletRequestInterface $servletRequest,
        HttpServletResponseInterface $servletResponse
    )
    {
        $username = $this->authService->login($this->data);
        $session = $servletRequest->getSession(true);
        $session->start();

        return array(
            'id' => $session->getId(),
            'username' => $username
        );
    }
}