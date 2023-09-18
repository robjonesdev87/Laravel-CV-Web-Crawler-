<?php

namespace App\Http\Controllers;

use App\Helpers;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return string
     */
    public function index()
    {
        try {
            return view('home',[]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function flushRedis()
    {
        Redis::flushDB();
        return redirect()->back()->with('success', "Redis Flushed");
    }

}
