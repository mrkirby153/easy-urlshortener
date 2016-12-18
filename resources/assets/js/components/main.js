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
                swal({
                    title: 'URL Shortened',
                    type: 'success',
                    html: 'Your shortened url is: <br><br><pre>' + response.data.success + '</pre>',
                });
                if (Shortener.user != 'null')
                    this.refreshUrls();
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
            const vm = this;
            swal({
                title: 'Confirm Deletion',
                type: 'warning',
                html: "Are you sure you want to delete the url <b>" + url + "</b>?<br/><br/><b>NOTE:</b> Deleting URLs can take a long time depending on how many clicks the URL has.<br/><br/>" +
                "Do not close this page while the deletion is in progress",
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                showLoaderOnConfirm: true,
                preConfirm: function (email) {
                    return new Promise(function (resolve, reject) {
                        vm.$http.post('/url/delete', {url: url}).then(resp => {
                            resolve();
                        }).catch(e => {
                            vm.refreshUrls();
                            reject('ERROR: ' + e.data.error);
                        });
                    })
                },
                allowOutsideClick: false
            }).then(function () {
                vm.refreshUrls();
                swal({
                    type: 'success',
                    title: 'URL Deleted',
                    html: 'Your URL was deleted!',
                    timer: 1000
                })
            });
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
            return this.urls.filter(function (url) {
                return url.owner == -1;
            });
        }
    },

    mounted(){
        this.refresh();
        setInterval(function () {
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
            this.$http.get('/admin/urls').then(resp => {
                this.urls = [];
                this.urls = resp.data;
            });
        },
        deleteUrl(url){
            const vm = this;
            swal({
                title: 'Confirm Deletion',
                type: 'warning',
                html: "Are you sure you want to delete the URL <b>" + url + "</b>?<br/><br/><b>NOTE:</b> Deleting URLs can take a long time depending on how many clicks the URL has.<br/><br/>" +
                "Do not close this page while the deletion is in progress",
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                showLoaderOnConfirm: true,
                preConfirm: function (email) {
                    return new Promise(function (resolve, reject) {
                        vm.$http.post('/url/delete', {url: url}).then(resp => {
                            resolve();
                        }).catch(e => {
                            reject('ERROR: ' + e.data.error);
                            vm.refreshUrls();
                        });
                    })
                },
                allowOutsideClick: false
            }).then(function () {
                vm.loadUrls();
                vm.refresh();
                swal({
                    type: 'success',
                    title: 'URL Deleted',
                    html: 'Your URL was deleted!'
                })
            });
        }
    }
});
