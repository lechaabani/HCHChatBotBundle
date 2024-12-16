<?php

namespace HCH\ChatBotBundle\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SessionMiddleware implements HttpKernelInterface
{
    private HttpKernelInterface $app;

    public function __construct(HttpKernelInterface $app)
    {
        $this->app = $app;
    }

    public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
    {
        if ($type === self::MAIN_REQUEST && !$request->hasSession()) {
            $request->setSession($request->getSession());
        }

        return $this->app->handle($request, $type, $catch);
    }
} 