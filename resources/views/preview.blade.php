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
        <form action="{{url('/url/redirect')}}" method="post">
            {{csrf_field()}}
            <input type="hidden" name="id" value="{{$url->id}}"/>
            <input type="submit" class="btn btn-success" style="margin-top: 10px; margin-bottom: 10px;" value="Click here to continue to the site">
        </form>
        <p>
            By appending a '+' sign to any URL shortened with this service, you can visit this page and get a preview of the URL
        </p>
    </div>
@endsection