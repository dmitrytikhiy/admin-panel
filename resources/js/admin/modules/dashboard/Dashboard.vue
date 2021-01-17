<template>
    <div>
        <h1>Dashboard</h1>
        <router-link to="user">Перейти к user</router-link>
    </div>
</template>

<script>
export default {
    name: 'Dashboard',
    mounted: {
        test() {
            console.log('Dashboard');
        }
    }
}
</script>