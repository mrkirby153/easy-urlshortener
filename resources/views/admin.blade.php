@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('body')
    <admin-dashboard inline-template>
        <div>
            <button class="btn btn-sm btn-default"><i class="fa fa-refresh" @click.prevent="refresh"></i> Refresh</button>
            <h3>Used URLs (@{{ ids.urls.used }} / @{{ ids.urls.avail }})</h3>
            <div class="progress">
                <div class="progress-bar progress-bar-info progress-bar-striped" aria-valuenow="30" aria-valuemin="0" aria-valuemax="30" :style="urls_ids">
                    <span class="sr-only">@{{ids.urls.used}} of @{{ ids.urls.avail }}</span>
                </div>
            </div>
            <hr/>
            <button class="btn btn-default" @click.prevent="loadUrls" v-if="ids.urls.used > 0">
                <div v-if="urls.length == 0">Load URLs</div>
                <div v-else>Refresh</div>
            </button>
            <div v-if="urls.length != 0">
                <h4>URLs</h4>
                <table class="table table-responsive table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Owner</th>
                        <th>URL</th>
                        <th>Shortened</th>
                        <th>Clicks</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for="url in urls">
                            <td>@{{ url.created_at }}</td>
                            <td>@{{ url.owner }}</td>
                            <td>@{{ url.long_url }}</td>
                            <td>@{{ url.short_url }}</td>
                            <td>@{{ url.click_count }}</td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-default btn-sm" @click="deleteUrl(url.id)">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </admin-dashboard>
@endsection
