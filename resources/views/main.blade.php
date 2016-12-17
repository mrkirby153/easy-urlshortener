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
            <div v-if="urls.length > 0">
                <h3>Your URLs</h3>
                <button class="btn btn-sm" @click="refreshUrls"><i class="fa fa-refresh" :class="{'fa-spin': loading}" aria-hidden="true"></i></button>
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
                        <td>
                            <div class="input-group">
                                <input type="text" readonly v-model="url.short_url" class="form-control"/>
                                <span class="input-group-btn">
                                    <button class="btn btn-default" :data-clipboard-text="url.short_url" v-clipboard @click="copyAlert"><i class="fa fa-clone" aria-hidden="true"></i></button>
                                </span>
                            </div>
                        </td>
                        <td>@{{url.click_count}}</td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-danger" @click="deleteUrl(url.id)">Delete</button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </url-shortener>
@endsection
