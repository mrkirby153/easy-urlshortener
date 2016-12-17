@extends('layouts.app')

@section('title', 'URL Preview')

@section('body')
    <div class="text-center">
        <p class="text-center">
            This URL {{str_replace('+', '', Request::fullUrl())}} redirects to
        </p>
        <strong>
            {{$url->long_url}}
        </strong>
        <p>
            <a href="{{$url->long_url}}" class="btn btn-success" style="margin-top: 10px">Click here to continue to the site</a>
        </p>
        <p>
            By appending a '+' sign to any URL shortened with this service, you can visit this page and get a preview of the URL
        </p>
    </div>
@endsection