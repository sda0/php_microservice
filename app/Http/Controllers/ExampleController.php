<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //
    public function total()
    {
        $wer = Redis::get('wer');
        Redis::set('wer','wer');
        return response()->json(['name' => 'Abigail', 'state' => 'CA', 'wer'=>$wer]);
    }
}
