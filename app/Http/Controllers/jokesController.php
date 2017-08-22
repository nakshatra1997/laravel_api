<?php

namespace App\Http\Controllers;
use App\Joke;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class jokesController extends Controller
{
    //
    public function index()
    {
        $jokes=Joke::all(); // NOT A GOOD METHOD
        return Response::json(['data'=>$this->transformCollection($jokes)],200);

    }
    public function show($id){
       // $joke = Joke::find($id);
          $joke=Joke::with(array('User'=>function($query)
          {
              $query->select('id','name');
          }))->find($id);
        if(!$joke){
            return Response::json([
                'error' => [
                    'message' => 'Joke does not exist'
                ]
            ], 404);
        }
         //get previous joke id
        $previous=Joke::where('id','<',$joke->id)->max('id');
        //get next joke id
        $next=Joke::where('id','>',$joke->id)->min('id');
        return Response::json([
            'previous_joke_id'=>$previous,
            'next_joke_id'=>$next,
            'data' => $this->transform($joke)
        ], 200);
    }
    private function transformCollection($jokes)
    {
        return array_map([$this,'transform'],$jokes->toArray());
    }
    private function transform($joke)
    {
        return[
            'joke_id'=>$joke['id'],
            'joke'=>$joke['joke'],
            'submitted_by'=>$joke['user']['name']
        ];
    }
public function store(Request $request) //ERROR WILL BE THERE COZ OF CSRF TOKEN ,MAKE CHANGES TO KERNEL .PHP
{
    if(!$request->joke or !$request->user_id)
    {
        return Response::json([
            'error'=>['message'=>'Please provide both joke and user_id']
        ],422);
    }
    $joke=Joke::create($request->all());
    return Response::json([
        'message'=>'joke created successfully',
        'data'=>$this->transform($joke)
    ]);
}
public function update(Request $request ,$id)
{
    if(!$request->joke or !$request->user_id)
    {
        return Response::json([
            'error'=>[
                'message'=>'please provide both body and user_id'
            ]
        ],422);
    }
    $joke=Joke::find($id);
    $joke->joke=$request->body;
    $joke->user_id=$request->user_id;
    $joke->save();
    return Response::json(['message'=>'joke updated successfully']);
}
public function destroy($id)
{
    Joke::destroy($id);
}
}
