<?php

namespace App\Http\Controllers;
use App\Joke;
use Illuminate\Http\Request;

class jokesController extends Controller
{
    //
    public function index()
    {
        $jokes=Joke::all(); // NOT A GOOD METHOD
        return $jokes;
    }
}
