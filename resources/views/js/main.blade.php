<script>
    window.Shortener = {
        csrfToken: '{{csrf_token()}}',

        user: '{{Auth::user()? Auth::id() : 'null'}}',

        debug: '{{Config::get('app.debug')? 'true' : 'false'}}'
    };
</script>