<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InvalidRequestException extends Exception
{
    public function __construct(string $message,int $code = 404)
    {
        parent::__construct($message, $code);
    }

    public function render(Request $request){
        if($request->expectsJson()){
            return response()->json(['msg'=>$this->message,'code'=> $this->code]);
        }

        return view('pages.error',['msg' => $this->message]);
    }
}
