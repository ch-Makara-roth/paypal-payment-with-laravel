<script setup lang="ts">
import { defineProps, defineEmits, useAttrs } from "vue";

import Dialog from "primevue/dialog";
import Button from "primevue/button";
import DataView from 'primevue/dataview';
import { useProductOrderStore } from "@/stores/ProductOrder";
import axios from "axios";
import { useToast } from "primevue/usetoast";
import { Icon } from "@iconify/vue";

const productOrderStore = useProductOrderStore();
const { order, removeProduct , incrementProducts , decrementProducts , clearAllProducts } = productOrderStore;

const props = defineProps({
    visible: {
        type: Boolean,
        default: false,
    },
});
const toast = useToast();

const emit = defineEmits(['update:visible', 'Order', 'cancel']);

const handleOrder = async () => {
    emit('Order');
    if (order?.products?.length > 0) {
        try {
            const response = await axios.post('/paypal', order, {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            const approveLink = response.data.approve_link;
            window.location.href = approveLink;
        } catch (error) {
            console.error('Error processing order:', error);
        }
    } else {
        toast.add({ severity: 'error', summary:'Pls Add the Product to Order ', life: 3000 });
    }
    closeDialog();
};

const handleCancel = () => {
    closeDialog();
};

const closeDialog = () => {
    emit('update:visible', false);
};
const handleHide = () => {
    closeDialog();
};
</script>

<template>
    <Dialog v-model:visible="props.visible" :closable="false" modal :draggable="false" position='top'
        :style="{ width: '70rem' }" :blockScroll="false" :pt="{
            root: 'border-none bg-white text-black',
            mask: {
                style: 'backdrop-filter: blur(1px)'
            },
            header: {
                class: 'bg-red text-white text-xl w-full font-bold flex'
            },
            content: {
                class: 'pt-4 text-lg leading-6 dialog-content'
            },
        }">
        <template #header>
            <div class="flex justify-between w-full">
                <div class="flex items-center">
                    <h3 class="text-black">Order Detail</h3>
                </div>
                <div class="flex items-center">
                    <Button icon="pi pi-times" class="p-button-text text-black" @click="closeDialog" />
                </div>
            </div>
        </template>
        <div class="grid grid-cols-4 bg-white">
            <div class="col-span-3">
                <div class="mb-4">
                    <div class="grid grid-cols-2 py-4">
                        <h3> Order Lists</h3>
                        <div class="flex justify-evenly">
                            <Button @click="clearAllProducts(order.userId)" class="p-2 shadow-lg hover:shadow-sm text-red-500 bg-slate-200" > Clear </Button>
                        </div>
                    </div>

                    <hr>
                </div>
                <div class="card bg-white text-black">
                    <DataView :value="order?.products" dataKey="id">
                        <template #list="slotProps">
                            <div class="flex flex-col bg-white text-black border shadow-md rounded-md overflow-hidden">
                                <div v-for="(item, index) in slotProps.items" :key="index">
                                    <div class="flex flex-col sm:flex-row sm:items-center p-6 gap-4 overflow-hidden"
                                        :class="{ 'border-t border-surface-200 dark:border-surface-700': index !== 0 }">
                                        <div class="md:w-24 relative">
                                            <img class="block  xl:block mx-auto rounded w-full" :src="`${item.image}`"
                                                :alt="item.name" />
                                        </div>
                                        <div
                                            class="flex flex-col md:flex-row justify-between md:items-center flex-1 gap-6">
                                            <div class="flex flex-row md:flex-col justify-between items-start gap-2">
                                                <div>
                                                    <span
                                                        class="font-medium text-surface-500 dark:text-surface-400 text-sm">{{
                                                            item.category }}</span>
                                                    <div class="text-lg font-medium mt-2">{{ item.name }}</div>
                                                    <div class="flex flex-col md:items-end gap-8">
                                                        <span class="text-xl font-semibold"> <span> price: </span>${{
                                                            item.price }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex  md:items-end gap-8">
                                                <div class="flex gap-6 justify-center items-center">
                                                    <Button @click="incrementProducts(item.id)" class=" hover:shadow-sm shadow-lg p-2 bg-slate-200">
                                                        <Icon icon="ic:round-plus" />
                                                    </Button>
                                                    <h4>{{ item.quantity }}</h4>
                                                    <Button  @click="decrementProducts(item.id)" class="hover:shadow-sm shadow-lg p-2 bg-slate-200">
                                                        <Icon icon="ic:round-minus" />
                                                    </Button>
                                                </div>
                                                <Button icon="pi pi-trash" class="p-button-text text-red-600"
                                                    @click="removeProduct(item.id)" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </DataView>
                </div>
            </div>
            <div class=" col-span-1">
                more information
            </div>
        </div>
        <template #footer>
            <div class="flex justify-end gap-8 py-8">
                <Button label="Cancel" class=" h-fit  p-3 hover:bg-slate-400 border p-button-text text-black"
                    @click="handleCancel" />

                <Button label="Order" class=" h-fit  p-3 hover:bg-slate-400 border p-button-text text-black"
                    @click="handleOrder" />
            </div>
        </template>
    </Dialog>
</template>

<style scoped>
::v-deep .p-dataview-content {
    background: white !important
}

.dialog-content {
    overflow-y: hidden;
    max-height: calc(100vh - 200px);
}
</style>
