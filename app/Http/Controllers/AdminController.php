<?php

namespace App\Http\Controllers;

use App\Click;
use App\ShortenedUrl;
use Illuminate\Http\Request;

class AdminController extends Controller {

    public function __construct() {
        return $this->middleware(['auth', 'can:view-admin']);
    }

    public function index() {
        return view('admin');
    }

    public function status() {
        $clickCount = Click::count();
        $urlCount = ShortenedUrl::count();
        return response()->json(['urls' => [
            'used' => $urlCount,
            'avail' => pow(62, config('shortener.url_id_size'))
        ]]);
    }

    public function urls(Request $request) {
        $raw_urls = ShortenedUrl::get();
        $urls = array();
        foreach ($raw_urls as $u) {
            $u->short_url = $request->root() . '/' . $u->id;
            $u->click_count = $u->clicks()->count();
            $u->owner = $u->owner();
            $urls[] = $u;
        }
        return $urls;
    }
}
