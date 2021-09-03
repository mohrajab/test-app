import Products from './components/Product/Products.vue';
import AddProduct from './components/Product/AddProduct';
import EditProduct from './components/Product/EditProduct';

export const routes = [
    {
        name: 'products-list',
        path: '/home',
        component: Products
    },
    {
        name: 'add-product',
        path: '/products/add',
        component: AddProduct
    },
    {
        name: 'edit-product',
        path: '/products/edit/:id',
        component: EditProduct
    },
];
