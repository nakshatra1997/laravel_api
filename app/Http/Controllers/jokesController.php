<?php

namespace App\Http\Controllers;
use App\Joke;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class jokesController extends Controller
{
    //
    public function index(Request $request)
    {
        //$jokes=Joke::all(); // NOT A GOOD METHOD
//        $jokes=Joke::with(
//            array('User'=>function($query)
//            {
//                $query->select('id','name');
//            })
//        )->select('id','joke','user_id')->paginate(5);//here we are hard coding it should be dynamic
//        return Response::json($this->transformCollection($jokes),200);
        //NOW MAKING PAGINATION DYNAMIC
//        $limit = $request->input('limit')?$request->input('limit'):5;
//
//        $jokes = Joke::with(
//            array('User'=>function($query){
//                $query->select('id','name');
//            })
//        )->select('id', 'joke', 'user_id')->paginate($limit);
//
//        $jokes->appends(array(
//            'limit' => $limit
//        ));
//
//        return Response::json($this->transformCollection($jokes), 200);
        //NOW IMPLEMENTING SEARCH FUNCTIONALITY
        $search_term = $request->input('search');
        $limit = $request->input('limit')?$request->input('limit'):5;

        if ($search_term)
        {
            $jokes = Joke::orderBy('id', 'DESC')->where('joke', 'LIKE', "%$search_term%")->with(
                array('User'=>function($query){
                    $query->select('id','name');
                })
            )->select('id', 'joke', 'user_id')->paginate($limit);

            $jokes->appends(array(
                'search' => $search_term,
                'limit' => $limit
            ));
        }
        else
        {
            $jokes = Joke::orderBy('id', 'DESC')->with(
                array('User'=>function($query){
                    $query->select('id','name');
                })
            )->select('id', 'joke', 'user_id')->paginate($limit);

            $jokes->appends(array(
                'limit' => $limit
            ));
        }

        return Response::json($this->transformCollection($jokes), 200);

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
       // return array_map([$this,'transform'],$jokes->toArray());
        $jokesArray=$jokes->toArray();
        return[
            'total' => $jokesArray['total'],
            'per_page' => intval($jokesArray['per_page']),
            'current_page' => $jokesArray['current_page'],
            'last_page' => $jokesArray['last_page'],
            'next_page_url' => $jokesArray['next_page_url'],
            'prev_page_url' => $jokesArray['prev_page_url'],
            'from' => $jokesArray['from'],
            'to' =>$jokesArray['to'],
            'data' => array_map([$this, 'transform'], $jokesArray['data'])
        ];
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
