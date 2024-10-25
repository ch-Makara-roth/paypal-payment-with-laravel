<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref ,onMounted } from 'vue';
import Card from 'primevue/card';
import Button from 'primevue/button';
import { Icon } from '@iconify/vue';
import { useProductOrderStore } from '@/stores/ProductOrder';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';


const props = defineProps({
    success: {
        type: String,
    }
})
const productOrderStore = useProductOrderStore();
const { order , addProduct } = productOrderStore;
const toast = useToast();

const product = ref([
    {
        id: 1,
        code: "001",
        name: 'Product 1',
        image: "https://thumbs.dreamstime.com/b/cup-coffee-coffee-beans-isolated-white-background-copy-space-text-coffee-cup-beans-isolated-white-background-164317823.jpg",
        description: 'Description for product 1',
        price: 29.99,
        quantity: 1,
        category: "Food"
    },
    {
        id: 2,
        code: "002",
        name: 'Product 2',
        image: "https://cdn.s-liquor.com.kh/sliquors3/wp-content/uploads/2022/10/Krud-Energy-Drink-Can-250ml.jpg?strip=all&lossy=1&webp=85&avif=80&ssl=1",
        description: 'Description for product 2',
        price: 49.99,
        quantity: 1,
        category: "Food"
    },
    {
        id: 3,
        code: "003",
        name: 'Product 3',
        image: "https://i.pinimg.com/236x/dc/71/2b/dc712b943fed78defa00820bc6edd1c3.jpg",
        description: 'Description for product 3',
        price: 19.99,
        quantity: 1,
        category: "Food"
    },
    {
        id: 4,
        code: "003",
        name: 'Product 3',
        image: "https://i.pinimg.com/236x/dc/71/2b/dc712b943fed78defa00820bc6edd1c3.jpg",
        description: 'Description for product 3',
        price: 19.99,
        quantity: 1,
        category: "Food"
    },
    {
        id: 5,
        code: "003",
        name: 'Product 3',
        image: "https://i.pinimg.com/236x/dc/71/2b/dc712b943fed78defa00820bc6edd1c3.jpg",
        description: 'Description for product 3',
        price: 19.99,
        quantity: 1,
        category: "Food"
    }
]);
if (props.success) {
    toast.add({ severity: 'success', summary: props.success, detail: props.success, life: 3000 });
}
</script>

<template>

    <Head title="Dashboard" />
    <Toast />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="text-black px-8 py-5"> Home / <span> Product </span></div>
                    <div class=" justify-center items-center py-10  flex flex-wrap">
                        <div v-for="(products , index) in product " :key="index" class=" items-center m-2">
                            <Card class="bg-white text-black" style="width: 23rem; overflow: hidden">
                                <template #header>
                                    <div class="flex justify-center items-center">
                                        <img class="  h-40 w-40 object-cover" alt="user header"
                                        :src="products.image" />
                                    </div>
                                </template>
                                <template #title>{{ products.name }}</template>
                                <template #subtitle>
                                    <div class="grid grid-cols-3">
                                        <p class=" col-span-2"> <span> code: </span>{{  products.code }}</p>
                                        <p class="col-spam-1 font-bold  text-red-600 "><span> Price : </span>{{  products.price }} $</p>
                                    </div>
                                </template>
                                <template #content>
                                    <p class="m-0">
                                       {{ products.description }}
                                    </p>
                                </template>
                                <template #footer>
                                    <div class="flex gap-4 mt-1 py-4 justify-end items-end">
                                        <Button @click="addProduct(products , $page.props.auth.user.id )" class=" shadow-md hover:shadow-none hover:bg-slate-200 " severity="secondary" outlined >
                                            <Icon  height="40px" width="40px" icon="material-symbols:add"/>
                                        </Button>
                                    </div>
                                </template>
                            </Card>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
