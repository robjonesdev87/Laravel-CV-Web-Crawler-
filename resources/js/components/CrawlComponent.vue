<template>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Run Crawl</div>
                    <div class="card-body">
                        <div v-if="errors.length">
                            <b>Validation Error</b>
                            <ul>
                                <li style="color:red" v-for="error in errors">{{ error }}</li>
                            </ul>
                        </div>

                        <form action="/validate" method="post" @submit="checkForm">
                            <input type="hidden" name="_token" :value="csrf"/>
                            <div class="form-group">
                                <input class="form-control" type="text" name="url" v-model="url"/>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary btn-lg" style="display:inline"
                                    :disabled="disableButtons">Crawl
                            </button>
                            <button type="button" class="btn btn-danger btn-lg" style="display:inline" @click="flushRedis"
                                    :disabled="disableButtons">Flush Redis
                            </button>

                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-3" style="margin-top:50px">
                <div class="card">
                    <div class="card-header">Queued</div>
                    <div class="card-body">
                        <code v-for="item in queuedCrawls">
                            <code v-if="item.jobStatus === 'Queued'">
                                <li>{{ item.url }}</li>
                            </code>
                        </code>

                    </div>
                </div>
            </div>

            <div class="col-md-3" style="margin-top:50px">
                <div class="card">
                    <div class="card-header">In Progress</div>
                    <div class="card-body">
                        <code v-for="item in queuedCrawls">
                            <code v-if="item.jobStatus === 'In Progress'">
                                <li>{{ item.url }}</li>
                            </code>
                        </code>

                    </div>
                </div>
            </div>

            <div class="col-md-3" style="margin-top:50px">
                <div class="card">
                    <div class="card-header">Complete</div>
                    <div class="card-body">
                        <code v-for="item in queuedCrawls">
                            <code v-if="item.jobStatus === 'Crawl Complete'">
                                <li style="margin-bottom:10px">
                                    {{ item.url }}
                                    <modal-component></modal-component>

                                </li>
                            </code>
                        </code>

                    </div>
                </div>
            </div>

            <div class="col-md-3" style="margin-top:50px">
                <div class="card">
                    <div class="card-header">Failed</div>
                    <div class="card-body">
                        <code v-for="item in queuedCrawls">
                            <code v-if="item.jobStatus === 'Crawl Failed'">
                                <li>{{ item.url }}</li>
                            </code>
                        </code>
                    </div>
                </div>
            </div>


        </div>
    </div>
</template>

<script>

export default {
    data: function () {
        return {
            csrf: window.csrfToken.csrfToken,
            errors: [],
            ajaxError: "",
            url: "http://example.com",
            queuedCrawls: [],
            name: "",
            disableButtons: false,
            modalOpen: true
        }
    },
    methods: {
      flushRedis: function (e) {

        let _this = this;
        _this.disableButtons = true;

        axios.post('/flush-redis', {
          _token: this.csrf,
          url: this.url,
        }).then(function (response) {
          setTimeout(() => _this.disableButtons = false, 1500);
        }).catch(function (error) {
          setTimeout(() => _this.disableButtons = false, 1500);
          console.log(error);
        });

        this.websocketListen();

        e.preventDefault();

      },
      checkForm: function (e) {

            let _this = this;
            _this.disableButtons = true;

            this.errors = [];

            if (!this.url) {
                this.errors.push('URL Required.');
            }

            if (this.url) {
                axios.post('/validate', {
                    _token: this.csrf,
                    url: this.url,
                }).then(function (response) {
                    setTimeout(() => _this.disableButtons = false, 1500);
                }).catch(function (error) {
                    setTimeout(() => _this.disableButtons = false, 1500);
                    console.log(error);
                });
            }

            e.preventDefault();
        },
        websocketListen: function (e) {
            let _this = this;

            axios.post('/get-queue-data', {
                _token: this.csrf,
            }).then(function (response) {
                _this.queuedCrawls = response.data;
            }).catch(function (error) {
                console.log(error);
            });

            Echo.channel('crawl-channel')
                .listen('BroadcastCrawlQueue', e => {
                    _this.queuedCrawls = e.crawl;
                    console.log(e.crawl);
                });
        }
    },
    props: {
        //testProp: {
        //    type: Number,
        //    default: -1
        //}
    },
    mounted() {
        this.websocketListen();
        console.log('Crawl Component mounted.')
    }
}
</script>
