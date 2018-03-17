<?php

declare(strict_types=1);

namespace Project\Code;

use Slim\Http\Request;
use Slim\Http\Response;

class Actions
{
    public function index(Request $request, Response $response): Response
    {
        $response->getBody()->write('Hello there');

        return $response;
    }

    public function info(Request $request, Response $response): Response
    {
        ob_start();
        phpinfo();
        $info = ob_get_clean();

        $response->getBody()->write($info);

        return $response;
    }
}
