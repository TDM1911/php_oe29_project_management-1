<template>
    <div @mouseover="hover = true"
        @mouseleave="hover = false">
        <label class="p-2 m-1 rounded"
            :class="display">
            {{ content }}
        </label>
        <br>
        <span class="text-danger" 
            style="cursor:pointer"
            v-if="hover && authorize" @click="deleteMessage()">
            <i class="far fa-times-circle"></i>
        </span>
    </div>
</template>
<script>
    export default {

        props: {
            content: {required : true},
            display: {default : ''},
            message_id: {required : true},
            index: {required : true},
            authorize: {default : false},
        },

        data() {
            return {
                hover: false,
            };
        },

        computed: {
            //
        },

        methods: {
            deleteMessage()
            {
                if (confirm("Are you sure you want to delete?")) {
                    axios.delete('/messages/' + this.message_id)
                        .then(response => {
                            if (response.data) {
                                this.$root.$emit('delete-message', this.index);
                            } else {
                                alert('Something went wrong! Message fail to delete');
                            }
                        });
                }
            },
        },

    }
</script>
