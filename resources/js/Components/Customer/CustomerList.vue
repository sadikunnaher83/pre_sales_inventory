<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12"></div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <h3>Customer</h3>
                        </div>
                        <hr />
                        <div class="float-end">
                            <a
                                href="/CustomerSavePage?id=0"
                                class="btn btn-success mx-3 btn-sm"
                            >
                                Add Customer
                            </a>
                        </div>

                        <!-- Modal -->

                        <div>
                            <input
                                placeholder="Search..."
                                class="form-control mb-2 w-auto form-control-sm"
                                type="text"
                                v-model="searchValue"
                            />
                            <EasyDataTable
                                buttons-pagination
                                alternating
                                :headers="Header"
                                :items="Item"
                                :rows-per-page="10"
                                :search-field="searchField"
                                :search-value="searchValue"
                            >
                                <template #item-number="{ id, name }">
                                    <Link
                                        class="btn btn-success mx-3 btn-sm"
                                        :href="`/CustomerSavePage?id=${id}`"
                                        >Edit</Link
                                    >
                                    <button
                                        class="btn btn-danger btn-sm"
                                        @click="DeleteClick(id)"
                                    >
                                        Delete
                                    </button>
                                </template>
                            </EasyDataTable>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, usePage, router, useForm } from '@inertiajs/vue3';
import { createToaster } from "@meforma/vue-toaster";
import { ref } from 'vue';
import Swal from 'sweetalert2';

const toaster = createToaster({
    position: "top-right",
});

const Header = [
    { text: "Name", value: "name" },
    { text: "Email", value: "email" },
    { text: "Phone", value: "phone" },
    { text: "Actions", value: "number" },
];

const page = usePage();
const Item = ref(page.props.customers);
const searchValue = ref();
const searchField = ref(['name', 'email', 'phone']);

// const DeleteClick = (id) => {
//     let text = "Are you sure to delete?";
//     if(confirm(text) === true){
//         router.get(`/delete-customer/${id}`)
//         toaster.success('Customer deleted successfully')
//     }else{
//         text = "Customer deletion canceled";
//         toaster.error('Customer deletion canceled');
//     }
// }

const DeleteClick = (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            router.get(`/delete-customer/${id}`);
            toaster.success('Customer deleted successfully');
        } else {
            toaster.error('Customer deletion canceled');
        }
    });
};
</script>


<style scoped>



</style>
