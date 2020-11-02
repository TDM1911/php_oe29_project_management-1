<template>
    <div>
        <div class="row fixed">
            <div>
                <a class="bg-dark text-white font-weight-bold text-decoration-none m-5 pl-3 pr-3 pt-2 pb-2" 
                    @click="toggle()"
                    style="cursor: pointer;">
                    {{ room_name }}
                    <span class="ml-2">
                        <i class="far fa-comments"></i>
                    </span>
                </a>
                <div class="border border-dark bg-dark rounded mt-3 p-3" v-if="show_chat_box">
                    <div id="chat" class="border-bottom border-white mb-2 overflow-auto chatbox">
                        <p v-for="(message,index) in messages" :title="message.created_at" data-toggle="tooltip"
                            :class="current_user == message.user.id ? 'text-right mr-1' : 'text-left ml-1'">
                            <span :class="current_user == message.user.id ? 'text-light' : 'text-primary'"
                                class="ml-2 mr-2" 
                                v-if="index > 0 && messages[index-1].user.id != message.user.id">
                                {{ message.user.name }}
                            </span>
                            <chat-item :content="message.content"
                                :message_id="message.id"
                                :index="index"
                                :authorize="current_user == message.user.id"
                                :display="current_user == message.user.id ? 'bg-light' : 'bg-primary'">
                            </chat-item>
                        </p>
                    </div>
                    <input type="text" 
                        v-model="content" 
                        class="w-100 form-control" 
                        :class="this.error ? 'is-invalid' : ''"
                        placeholder="Enter message here..."
                        @keyup.13="sendMessage()">
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
        width: 300px;
    }
    .chatbox {
         height: 65vh; 
         scrollbar-width: thin; 
         scrollbar-color: #3490dc black;
    }
</style>
<script>
    import ChatItem from '../components/ChatItem';
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
            axios.get(this.render)
                .then(response => {
                    this.messages = response.data.messages;
                });
            this.$root.$on('delete-message', (deleted_index) => {
                this.messages.splice(deleted_index,1);
            });

            Echo.private('room.' + this.room)
            .listen('SendMessage', (e) => {
                this.messages.push(e.message);
                this.scrollToEnd();
            });

            Echo.private('room.' + this.room)
            .listen('DeleteMessage', (e) => {
                for (let i = 0; i < this.messages.length; i++) {
                    if (this.messages[i].id == e.message.id) {
                        this.messages.splice(i,1);
                    }
                };
            });
        },


        updated() {
            this.$nextTick(() => this.scrollToEnd());
        },

        beforeDestroy () {
            Echo.leave(`room.${this.room}`)
        },

        data() {

            return {    
                messages : [],
                show_chat_box: false,
                content: '',
                error: false,
            }
        },

        computed: {
            //
        },

        methods: {
            scrollToEnd ()
            {
                var chat = document.getElementById("chat");
                chat.scrollTop = chat.scrollHeight;
            },

            toggle()
            {
                this.show_chat_box = !this.show_chat_box;
            },

            sendMessage()
            {
                if (this.content == '')
                {
                    this.error = true;
                } else {
                    this.error = false;
                    axios.post(this.store, {
                        'content' : this.content,
                    })
                        .then(response => {
                            this.content = '';
                            this.error = false;
                            this.messages.push(response.data);
                        })
                        .catch(response => {
                            this.error = true;
                            console.log(response.data);
                        });
                }
            },
        }

    }
</script>
