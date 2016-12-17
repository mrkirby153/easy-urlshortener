<?php

namespace App\Http\Controllers;

use App\Click;
use App\ShortenedUrl;
use Illuminate\Http\Request;

class UrlController extends Controller {


    public function index() {
        return view('main');
    }


    public function create(Request $request) {
        $url = $request->url;
        $custom = $request->custom_alias;

        $this->validate($request, [
            'url' => 'required|URL',
            'custom_alias' => 'unique:shortened_urls,id|alpha_num'
        ], [
            'url.required' => 'Please specify a URL',
            'url.u_r_l' => 'Please enter a valid URL',
            'custom_alias.unique' => 'This alias is already taken',
            'custom_alias.alpha_num' => 'Aliases can only have the characters A-Z and 0-9'
        ]);

        $shortenedUrl = new ShortenedUrl();
        if ($custom != '') {
            $shortenedUrl->id = $custom;
        } else {
            $id = $this->generateId(5);
            if ($id == "E:MAX_TRIES_EXCEEDED") {
                return response()->json(['url' => 'Could not generate a shortened URL. If this issue persists, we have run out of IDs'], 422);
            }
            $shortenedUrl->id = $id;
        }
        $shortenedUrl->long_url = $url;
        $shortenedUrl->owner = (\Auth::guest()) ? -1 : \Auth::id();
        $shortenedUrl->save();
        return response()->json(['success' => $shortenedUrl->id]);
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
        if ($url->owner != \Auth::id()) {
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
        $url = ShortenedUrl::findOrFail($id);
        $click = new Click();
        $click->id = $this->generateId(30);
        $click->url = $url->id;
        $click->user_agent = request()->server('HTTP_USER_AGENT');
        $click->save();
        return response()->redirectTo($url->long_url);
    }

    private function generateId($size) {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $url = "";
        $tries = 0;
        do {
            for ($i = 0; $i < $size; $i++) {
                $url .= $chars[rand(0, strlen($chars) - 1)];
            }
            $tries += 1;
        } while (ShortenedUrl::whereId($url)->first() != null && $tries < 1000);
        if ($tries >= 1000) {
            return "E:MAX_TRIES_EXCEEDED";
        }
        return $url;
    }
}
