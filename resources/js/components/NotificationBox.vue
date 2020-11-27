<template>
    <div>
        <div class="fixed">
            <div>
                <a class="bg-dark text-white font-weight-bold text-decoration-none pl-3 pr-3 pt-2 pb-2" 
                    @click="toggle()"
                    style="cursor: pointer;">
                    <i class="fas fa-bell"></i>
                    <i v-if="notifications.length" class="fas fa-circle text-danger"></i>
                    ({{ notifications.length }})
                </a>
            </div>
            <div class="border-dark bg-white rounded mt-3"
                style="max-height: 400px"
                v-if="show_box && notifications.length != 0">
                <div class="border pl-3 pr-3 pt-1 pb-1"
                    v-for="(notification,index) in notifications" :title="notification.created_at">
                    <div class="row">
                        <a class="col text-decoration-none" :href="notification.url">
                            {{ notification.message }}
                        </a>
                        <div class="col-2">
                            <a @click="seen(index)">
                                <i class="far fa-eye text-secondary"
                                    style="cursor: pointer;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
    .fixed {
        position: fixed;
        bottom: 1rem;
        right: 1rem;
        max-width: 300px;
    }
</style>
<script>
    export default {

        props: {
            channel: {required: true},
        },

        created() 
        {
            axios.get('http://127.0.0.1:8000/notifications')
                .then(response => {
                    this.notifications = response.data;
                });

            Echo.private(`notify.${this.channel}`)
                .listen('NotifyEvent', (e) => {
                    alert('AAA');
                });
        },


        updated() {
            this.$nextTick(() => this.scrollToEnd());
        },

        beforeDestroy () {
            Echo.leave(`notify.${this.channel}`)
        },

        data() {

            return {
                show_box: false,
                notifications: [],
                unread: 0,
            }
        },

        computed: {
            //
        },

        methods: {
            seen(index)
            {
                axios.patch('http://127.0.0.1:8000/notifications/' + this.notifications[index].id)
                    .then(response => {
                        if (response.data) {
                            this.notifications.splice(index, 1);
                        }
                    });
            },

            scrollToEnd ()
            {
                //
            },

            toggle()
            {
                this.show_box = !this.show_box;
            },
        }

    }
</script>
