<?php

namespace App\Exceptions;

use App\Http\Requests\Request;
use Exception;

class InternalException extends Exception
{
    protected $msgForUser;

    public function __construct(string $message, string $msgForUser = '系统内部错误', int $code = 500)
    {
        parent::__construct($message, $code);
        $this->msgForUser = $msgForUser;
    }

    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['msg' => $this->message, 'code' => $this->code]);
        }

        return view('pages.error',['msg' => $this->msgForUser]);
    }
}
