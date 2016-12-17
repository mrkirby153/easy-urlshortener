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
                this.refreshUrls();
            });
        },
        refreshUrls(){
            this.urls = [];
            this.loading = true;
            this.$http.get('/url/get').then(response => {
                this.loading = false;
                this.urls = response.data;
            });
        }
    }
});