<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * Show the app layout page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function app()
    {
        return view('layouts.app');
    }
    /**
     * Show login page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Show register page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function register()
    {
        return view('auth.register');
    }

    /**
     * Show icons page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function menu1()
    {
        return view('menu1');
    }

    /**
     * Show logout page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function logout()
    {
        // Logout logic would go here
        return redirect('/');
    }
}