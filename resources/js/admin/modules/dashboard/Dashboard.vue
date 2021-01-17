<template>
    <div>
        <h1>Dashboard</h1>
        <router-link to="user">Перейти к user</router-link>
        <button v-on:click="login()">test</button>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'Dashboard',
    methods: {
        login() {
            axios.post('http://admin-panel.workspace/api/auth/login', {email: 'admin@admin.com', password: 'admin'}).then(res => {})
        }
    }
}
</script>