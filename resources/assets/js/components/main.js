Vue.component('url-shortener', {
    data(){
        return {
            urls: [],
            forms: {
                create: $.extend(true, new Form({
                    url: '',
                    custom_alias: ''
                }), {})
            },
            loading: false,
            shortened: ''
        };
    },

    mounted(){
        this.refreshUrls();
    },

    methods: {
        copyAlert(){
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            toastr["success"]("URL copied to clipboard", "Success!")
        },
        shortenUrl(){
            this.shortened = '';
            Shortener.post('/url/create', this.forms.create).then(response => {
                toastr.options = {
                    "closeButton": false,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "10000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
                toastr["success"]("Your URL has been shortened", "URL Generated");
                if (Shortener.user != 'null')
                    this.refreshUrls();
                this.shortened = response.data.success;
            });
        },
        refreshUrls(){
            this.loading = true;
            this.$http.get('/url/get').then(response => {
                this.urls = [];
                this.loading = false;
                this.urls = response.data;
            });
        },
        deleteUrl(url){
            toastr["info"]("Deleting URL, this may take a long time, depending on the amount of clicks it has.", "Deleting URL");
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "10000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            this.loading = true;
            this.$http.post('/url/delete', {
                url: url
            }).then(response => {
                this.loading = false;
                toastr.options = {
                    "closeButton": false,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
                toastr["success"]("URL has been deleted", "Success");
                this.refreshUrls();
            }).catch(e => {
                toastr.options = {
                    "closeButton": false,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
                toastr["error"](e.data.error, "Error");
                this.loading = false;
            })
        }
    }
});

Vue.component('admin-dashboard', {
    data(){
        return {
            ids: {
                urls: {
                    used: 0,
                    avail: 0
                }
            },
            urls: []
        }
    },

    computed: {
        urls_ids(){
            return "width:" + (this.ids.urls.used / this.ids.urls.avail) * 100 + "%";
        },
        anonymousUrls(){
            return this.urls.filter(function(url){
                return url.owner == -1;
            });
        }
    },

    mounted(){
        this.refresh();
        setInterval(function(){
            this.refresh();
        }.bind(this), 5000);
    },

    methods: {
        refresh(){
            this.$http.get('/admin/status').then(resp => {
                this.ids.urls = resp.data.urls;
            });
        },
        loadUrls(){
            this.$http.get('/admin/urls').then(resp=>{
                this.urls = [];
                this.urls = resp.data;
            });
        },
        deleteUrl(url){
            this.$http.post('/url/delete', {
                url: url
            }).then(response => {
                toastr.options = {
                    "closeButton": false,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
                toastr["success"]("URL has been deleted", "Success");
                this.refresh();
                this.loadUrls();
            }).catch(e => {
                toastr.options = {
                    "closeButton": false,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
                toastr["error"](e.data.error, "Error");
            })
        }
    }
});
