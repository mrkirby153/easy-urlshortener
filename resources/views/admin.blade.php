@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('body')
    <admin-dashboard inline-template>
        <div>
            <button class="btn btn-sm btn-default"><i class="fa fa-refresh" @click.prevent="refresh"></i> Refresh
            </button>
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
            <div v-if="urls.length > 0">
                <h3>All URLs</h3>

                <div class="row url-container" v-for="url in urls">
                    <h3>@{{ url.title }}</h3>
                    <div class="row">
                        <div class="col-sm-8">
                            <a :href="url.long_url" target="_blank" :title="url.long_url">@{{url.long_url}}</a>
                        </div>
                        <div class="col-sm-4 text-right">
                            <span class="created-by">@{{ url.owner }}</span>
                            <span class="time-ago"><timeago :since="url.created_at" :auto-update="1" :max-time="259200"></timeago></span>
                        </div>
                    </div>
                    <div class="row m-top-10">
                        <div class="col-sm-7">
                            <span class="shortened-url">@{{ url.short_url }}<span class="clicks"><i class="fa fa-bar-chart"></i> @{{ url.click_count }}</span></span>
                        </div>
                        <div class="col-sm-5 text-right">
                            <button class="btn btn-danger btn-sm" @click="deleteUrl(url.id)">Delete</button>
                        </div>
                    </div>
                    <hr v-if="urls.length > 1"/>
                </div>
            </div>
        </div>
    </admin-dashboard>
@endsection
