@extends('layouts.app')

@section('title', 'Dashboard')

@section('body')
    <url-shortener inline-template>
        <div>
            <form @submit.prevent="shortenUrl">
                <form-text display="URL" :form="forms.create" input="url"></form-text>
                <form-text display="Custom Alias (Optional)" :form="forms.create" input="custom_alias"></form-text>
                <button type="submit" class="btn btn-success pull-right" @click.prevent="shortenUrl" :disabled="forms.create.busy">
                    <span v-if="forms.create.busy">
                        <i class="fa fa-spin fa-spinner"></i> Working...
                    </span>
                    <span v-else>
                        Shorten
                    </span>
                </button>
            </form>
            <transition name="fade">
                <div v-if="shortened">
                    <h3>Shortened URL</h3>
                    <div class="input-group">
                        <input type="text" readonly v-model="shortened" class="form-control"/>
                        <span class="input-group-btn">
                                    <button class="btn btn-default" :data-clipboard-text="shortened" v-clipboard @click="copyAlert"><i class="fa fa-clone" aria-hidden="true"></i></button>
                                </span>
                    </div>
                </div>
            </transition>
            <div v-if="urls.length > 0">
                <h3>Your URLs</h3>
                <button class="btn btn-sm btn-default" @click="refreshUrls"><i class="fa fa-refresh" aria-hidden="true"></i></button>

                <div class="row url-container" v-for="url in urls">
                    <h3>@{{ url.title }}</h3>
                    <div class="row">
                        <div class="col-sm-8">
                            <a :href="url.long_url" target="_blank" :title="url.long_url">@{{url.long_url}}</a>
                        </div>
                        <div class="col-sm-4 text-right">
                            <timeago :since="url.created_at" :auto-update="1" :max-time="259200"></timeago>
                        </div>
                    </div>
                    <div class="row m-top-10">
                        <div class="col-sm-8">
                            <span class="shortened-url">@{{ url.short_url }}<span class="clicks"><i class="fa fa-bar-chart"></i> @{{ url.click_count }}</span></span>

                        </div>
                        <div class="col-sm-4 text-right">
                            <button class="btn btn-default btn-sm" :data-clipboard-text="url.short_url" v-clipboard @click="copyAlert">Copy</button>
                            <button class="btn btn-danger btn-sm" @click="deleteUrl(url.id)">Delete</button>
                        </div>
                    </div>
                    <hr v-if="urls.length > 1"/>
                </div>
            </div>
        </div>
    </url-shortener>
@endsection
