<div v-if="urls.length > 0">
    <h3>{{$title}}</h3>
    <button class="btn btn-sm btn-default" @click="refreshUrls"><i class="fa fa-refresh" aria-hidden="true"></i></button>

    <div class="row url-container" v-for="url in urls">
        <h3>@{{ url.title }}</h3>
        <div class="row">
            <div class="col-sm-8">
                <a :href="url.long_url" target="_blank" :title="url.long_url">@{{url.long_url}}</a>
            </div>
            <div class="col-sm-4 text-right">
                @if($show_owner)
                    <span class="created-by">@{{ url.owner }}</span>
                @endif
                <timeago :since="url.created_at" :auto-update="1" :max-time="259200"></timeago>
            </div>
        </div>
        <div class="row m-top-10">
            <div class="col-sm-8">
                <span class="shortened-url">@{{ url.short_url }}</span>
            </div>
            <div class="col-sm-4 text-right">
                <button class="btn btn-default btn-sm" :data-clipboard-text="url.short_url" v-clipboard @click="copyAlert">Copy</button>
                <button class="btn btn-danger btn-sm" @click="deleteUrl(url.id)">Delete</button>
            </div>
        </div>
        <hr v-if="urls.length > 1"/>
    </div>
</div>