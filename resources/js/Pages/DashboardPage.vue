<template>
<h1>This is dashboard Page</h1>
<h1>Email: {{ page.props.user }}</h1>

</template>

<script setup>
     import { Link, usePage, router, useForm } from '@inertiajs/vue3';
     const page = usePage();
</script>

<style scoped>



</style>
