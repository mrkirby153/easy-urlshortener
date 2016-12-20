<?php

namespace App\Http\Controllers;

use App\Click;
use App\ShortenedUrl;
use Illuminate\Http\Request;

class UrlController extends Controller {

    // A list of reserved URLs that can't be used without breaking things
    // All of these are the names of routes used by this application
    private $reserved = [
        "admin",
        "login",
        "logout",
        "password",
        "register",
        "url"
    ];


    public function index() {
        return view('main');
    }


    public function create(Request $request) {
        $url = $request->url;
        $custom = $request->custom_alias;

        $this->validate($request, [
            'url' => 'required|URL',
            'custom_alias' => 'unique:shortened_urls,id|alpha_num|max:15'
        ], [
            'url.required' => 'Please specify a URL',
            'url.u_r_l' => 'Please enter a valid URL',
            'custom_alias.unique' => 'This alias is already taken',
            'custom_alias.alpha_num' => 'Aliases can only have the characters A-Z and 0-9',
            'custom_alias.max' => 'Aliases can only be 15 characters maximum'
        ]);

        $shortenedUrl = new ShortenedUrl();
        if ($custom != '') {
            if(in_array($custom, $this->reserved)){
                return response()->json(['custom_alias'=>'This alias is already taken'], 422);
            }
            $shortenedUrl->id = $custom;
        } else {
            $id = $this->generateId(config('shortener.url_id_size'), $shortenedUrl);
            if ($id == "E:MAX_TRIES_EXCEEDED") {
                return response()->json(['url' => 'Could not generate a shortened URL. If this issue persists, we have run out of IDs'], 422);
            }
            $shortenedUrl->id = $id;
        }
        if(in_array($shortenedUrl->id, $this->reserved)){
            return response()->json(['url'=>'An unknown error occurred when generating a shortened URL. Please try again'], 422);
        }
        $shortenedUrl->long_url = $url;
        $shortenedUrl->owner = (\Auth::guest()) ? -1 : \Auth::id();
        $shortenedUrl->title = $this->get_title($url);
        $shortenedUrl->save();
        return response()->json(['success' => $request->root() . '/' . $shortenedUrl->id]);
    }

    public function all(Request $request) {
        if (\Auth::guest())
            return response()->json([]);
        $raw_urls = ShortenedUrl::whereOwner(\Auth::id())->orderBy('created_at', 'desc')->get();
        $urls = array();
        foreach ($raw_urls as $u) {
            $u->short_url = $request->root() . '/' . $u->id;
            $u->click_count = $u->clicks()->count();
            $urls[] = $u;
        }
        return $urls;
    }

    public function delete(Request $request) {
        $url = ShortenedUrl::whereId($request->url)->first();
        if ($url == null) {
            return response()->json(['error' => 'That URL does not exist'], 422);
        }
        if ($url->owner != \Auth::id() && !\Auth::user()->admin) {
            return response()->json(['error' => 'You do not have permission to delete this URL'], 422);
        }
        if ($url->clicks()->count() < 1500)
            foreach ($url->clicks as $click) {
                $click->delete();
            }
        else {
            \Log::info("Deleting where id = $url->id");
            \DB::table('clicks')->where('url', '=', $url->id)->delete();
        }
        $url->delete();
        return response()->json(['success' => 'URL deleted!']);
    }

    public function click($id) {
        $preview = ends_with($id, '+');
        if ($preview)
            $id = str_replace('+', '', $id);

        $url = ShortenedUrl::whereId($id)->first();
        if ($url == null) {
            return response(view('notfound'), 404);
        }
        if ($preview)
            return view('preview', compact('url'));
        $click = new Click();
        $click->id = $this->generateId(config('shortener.click_id_size'), $click);
        $click->url = $url->id;
        $click->user_agent = request()->server('HTTP_USER_AGENT');
        $click->save();
        return response()->redirectTo($url->long_url);
    }

    public function previewClick(Request $request) {
        return $this->click($request->id);
    }

    private function generateId($size, $model) {
        $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $url = "";
        $tries = 0;
        do {
            for ($i = 0; $i < $size; $i++) {
                $url .= $chars[rand(0, strlen($chars) - 1)];
            }
            $tries += 1;
        } while ($model->whereId($url)->first() != null && $tries < 1000);
        if ($tries >= 1000) {
            return "E:MAX_TRIES_EXCEEDED";
        }
        return $url;
    }

    private function get_title($url){
        $str = file_get_contents($url);
        if(strlen($str)>0){
            $str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
            preg_match("/\\<title\\>(.*)\\<\\/title\\>/i",$str,$title); // ignore case
            return $title[1];
        }
    }
}
