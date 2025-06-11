<?php

namespace App\Controllers;

use GuzzleHttp\Exception\GuzzleException;

class ErrorHandler extends BaseController
{
    private GuzzleException $exception;

    public function __construct(GuzzleException $exception)
    {
        $this->exception = $exception;
    }

    public function errorPage(): string
    {
        switch ($this->exception->getCode())
        {
            case 400:
                $header_data = [
                    'title' => '400'
                ];
                return
                    view('event/common/header', $header_data).
                    view('event/common/menu').
                    view('errors/abstract_suite/400', ['exception' => $this->exception]).
                    view('event/common/footer')
                    ;
            case 403:
                $header_data = [
                    'title' => '403'
                ];
                return
                    view('event/common/header', $header_data).
                    view('event/common/menu').
                    view('errors/abstract_suite/403', ['exception' => $this->exception]).
                    view('event/common/footer')
                    ;
            default:
                return
                    view('errors/abstract_suite/unknown', ['exception' => $this->exception])
                    ;
        }
    }
}