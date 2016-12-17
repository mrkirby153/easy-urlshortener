@extends('layouts.app')

@section('title', 'Dashboard')

@section('body')
    <url-shortener inline-template>
        <div>

            <h3>Your URLs</h3>
            <table class="table table-responsive">
                <thead>
                <tr>
                    <th>URL</th>
                    <th>Shortened URL</th>
                    <th>Clicks</th>
                </tr>
                </thead>
                <tbody>
                    <tr v-for="url in urls">
                        <td><a :href="url.long_url" target="_blank">@{{url.long_url}}</a></td>
                        <td><input type="text" readonly v-model="url.short_url" class="form-control"/></td>
                        <td>@{{url.clicks}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </url-shortener>
@endsection
