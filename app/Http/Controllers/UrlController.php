<?php

namespace App\Http\Controllers;

use App\ShortenedUrl;
use Illuminate\Http\Request;

class UrlController extends Controller
{


    public function index(){
        return view('main');
    }


    public function create(Request $request){
        $url = $request->url;
        $custom = $request->custom_alias;

        $this->validate($request, [
            'url'=>'required|URL',
            'custom_alias'=>'unique:shortened_urls,id'
        ], [
            'url.required'=>'Please specify a URL',
            'url.u_r_l'=>'Please enter a valid URL',
            'custom_alias.unique'=>'This alias is already taken'
        ]);

        $shortenedUrl = new ShortenedUrl();
        if($custom != ''){
            $shortenedUrl->id = $custom;
        } else {
            $id = $this->generateId();
            if($id == "E:MAX_TRIES_EXCEEDED"){
                return response()->json(['url'=>'Could not generate a shortened URL. If this issue persists, we have run out of IDs'], 422);
            }
            $shortenedUrl->id = $id;
        }
        $shortenedUrl->long_url = $url;
        $shortenedUrl->owner = (\Auth::guest()) ? -1 : \Auth::id();
        $shortenedUrl->save();
        return response()->json(['success'=>$shortenedUrl->id]);
    }

    public function all(Request $request){
        if(\Auth::guest())
            return response()->json([]);
        $raw_urls = ShortenedUrl::whereOwner(\Auth::id())->orderBy('created_at', 'desc')->get();
        $urls = array();
        foreach($raw_urls as $u){
            $u->short_url = $request->root().'/'.$u->id;
            $u->clicks = $u->clicks()->count();
            $urls[] = $u;
        }
        return $urls;
    }

    public function delete(Request $request){
        $url = ShortenedUrl::whereId($request->url)->first();
        if($url == null){
            return response()->json(['error'=>'That URL does not exist'], 422);
        }
        if($url->owner != \Auth::id()){
            return response()->json(['error'=>'You do not have permission to delete this URL'], 422);
        }
        foreach($url->clicks as $click){
            $click->delete();
        }
        $url->delete();
        return response()->json(['success'=>'URL deleted!']);
    }

    private function generateId(){
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $url = "";
        $tries = 0;
        do {
            for ($i = 0; $i < 5; $i++) {
                $url .= $chars[rand(0, strlen($chars) - 1)];
            }
            $tries += 1;
        } while (ShortenedUrl::whereId($url)->first() != null && $tries < 1000);
        if($tries >= 1000){
            return "E:MAX_TRIES_EXCEEDED";
        }
        return $url;
    }
}
