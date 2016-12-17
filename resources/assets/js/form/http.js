module.exports = {
    post: function (uri, form) {
        return Shortener.sendForm('post', uri, form);
    },


    put: function (uri, form) {
        return Shortener.sendForm('put', uri, form);
    },


    delete: function (uri, form) {
        return Shortener.sendForm('delete', uri, form);
    },


    /**
     * Send the form to the back-end server. Perform common form tasks.
     *
     * This function will automatically clear old errors, update "busy" status, etc.
     */
    sendForm: function (method, uri, form) {
        return new Promise(function (resolve, reject) {
            form.start();

            Vue.http[method](uri, form).then(function (response) {
                    form.finish();

                    resolve(response);
                }).catch(function (errors) {
                    form.errors.set(errors.data);
                    form.busy = false;

                    reject(errors);
                });
        });
    }
};
