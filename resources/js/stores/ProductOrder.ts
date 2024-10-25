import { defineStore } from "pinia";
import { ref } from "vue";

export const useProductOrderStore = defineStore('ProductOrder', () => {
    const order = ref({
        userId: null,
        products: [],
        totalAmount: 0,
        totalQuantity: 0,
        currency: "USD"
    });

    const calculateTotal = () => {
        order.value.totalAmount = order.value.products.reduce((total, product) => {
            return total + product.price * product.quantity;
        }, 0);
    };

    const addProduct = (product, userId) => {
        if (!product.id || !product.name || product.price < 0 || product.quantity <= 0) {
            console.error("Invalid product data.");
            return;
        }
        const existingProduct = order.value.products.find(p => p.id === product.id);

        if (existingProduct) {
            existingProduct.quantity += product.quantity;
        } else {
            order.value.products.push({
                id: product.id,
                name: product.name,
                price: product.price,
                image: product.image,
                quantity: product.quantity
            });
        }
        if (userId) {
            order.value.userId = userId;
        }

        order.value.totalQuantity = 0;
        order.value.products.forEach(data => {
            order.value.totalQuantity += data.quantity;
        });
        calculateTotal();
    };

    const removeProduct = (productId) => {
        order.value.products = order.value.products.filter(p => p.id !== productId);
        order.value.totalQuantity = order.value.products.reduce((total, product) => total + product.quantity, 0);
        calculateTotal();
    };

    const clearAllProducts = (userId: number) => {
        if (order.value.userId === userId) {
            order.value.products = [];
            order.value.totalAmount = 0;
            order.value.totalQuantity = 0;
        } else {
            console.error("User ID does not match.");
        }
    };


    const incrementProducts = (id: number) => {
        const product = order.value.products.find(p => p.id === id);
        if (product) {
            product.quantity ++;
            calculateTotal();
        }
    };

    const decrementProducts = (id) => {
        const product = order.value.products.find(p => p.id === id);

        if (product.quantity > 1 ) {
            product.quantity  --;
            calculateTotal();
        }else if(product.quantity < 1 ){
            removeProduct(id)
        }

    };


    return { order, addProduct, removeProduct, calculateTotal , incrementProducts , decrementProducts , clearAllProducts };
});
