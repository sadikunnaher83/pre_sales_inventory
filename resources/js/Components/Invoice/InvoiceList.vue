<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <h3>Invoice List</h3>
                        </div>
                        <hr />
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
                                <template #item-number="{ id }">
                                    <button @click="showDetails(id)"
                                        class="viewBtn btn btn-outline-dark text-sm px-3 py-1 btn-sm m-0 me-2"
                                    >
                                        <i class="fa text-sm fa-eye"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" @click="DeleteClick(id)">
                                        Delete
                                    </button>
                                </template>
                            </EasyDataTable>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal component for invoice details -->

        <InvoiceDetails v-if="show" :customer="customer" @close="CloseModal"/>

    </div>
</template>

<script setup>

import { usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { createToaster } from "@meforma/vue-toaster";
import InvoiceDetails from './InvoiceDetails.vue';
import Swal from 'sweetalert2';

const toaster = createToaster({
    position: "top-right",
});

const page = usePage();
const Item = ref(page.props.list);

const Header =[
    { text:'Name', value: 'customer.name' },
    { text:'Customer ID', value: 'customer.id' },
    { text:'Phone', value: 'customer.phone' },
    { text:'Total', value: 'total' },
    { text:'Discount', value: 'discount' },
    { text:'Vat', value: 'vat' },
    { text:'Payable', value: 'payable' },
    { text:'Action', value: 'number' },

]

const searchValue = ref();
const searchField = ref(['customer.name']);

const show = ref(false);
const customer = ref();

const showDetails = (id) => {
    show.value = !show.value;
    customer.value = Item.value.find(item => item.id === id);
    // console.log(customer.value);
}

const CloseModal = () => {
    show.value = false;
}

const DeleteClick = (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "This record will be deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Perform delete logic here
            console.log("Deleting item with id:", id);
            Swal.fire('Deleted!', 'Your record has been deleted.', 'success');
        } else if (result.isDismissed) {
            // Cancel logic here
            console.log("User cancelled deletion");
            Swal.fire('Cancelled', 'Your record was not deleted.', 'info');
        }
    });
}


</script>

<style scoped></style>

