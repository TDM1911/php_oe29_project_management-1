<template>
    <div class="m-2">
        <input type="text" 
            class="form-control"
            v-model="search">
        <p v-for="room in results" class="border bg-white rounded mt-2 p-3 d-flex">
            <a class="mr-auto">
                {{ room.name }}
            </a>
            <button class="btn btn-primary ml-auto btn-sm">
                Open
            </button>
        </p>
    </div>
</template>
<script>
    export default {

        props: {
            render : {required : true},
            current_user : {required : true},
            store: {required : true},
            room: {required: true},
            room_name: {default: ''},
        },

        created() 
        {
            //
        },

        updated() {
            this.$nextTick(() => this.searchUser());
        },

        data() {

            return {
                error: false,
                timer: 0,
                search: '',
                results: [],
            }
        },

        computed: {
            //
        },

        methods: {
            searchUser()
            {
                if (this.search != '' && this.search.length > 5) {
                    if (this.timer) {
                        clearTimeout(this.timer);
                        this.timer = null;
                    }
                    this.timer = setTimeout(() => {
                        axios.get('/room', {
                            'search' : this.search,
                        })
                            .then(response => {
                                this.results = response.data.results;
                            });
                    }, 2000);
                }
                else {
                    // reset...
                }
            },
        }

    }
</script>
