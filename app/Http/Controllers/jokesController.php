<?php

namespace App\Http\Controllers;
use App\Joke;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class jokesController extends Controller
{
    //
    public function index()
    {
        $jokes=Joke::all(); // NOT A GOOD METHOD
        return Response::json(['data'=>$jokes],200);

    }
    public function show($id){
        $joke = Joke::find($id);

        if(!$joke){
            return Response::json([
                'error' => [
                    'message' => 'Joke does not exist'
                ]
            ], 404);
        }

        return Response::json([
            'data' => $joke
        ], 200);
    }
}
