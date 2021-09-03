<template>
    <div>
        <h3 class="text-center">All Products</h3><br/>
        <p class="text-right">
            <router-link :to="{name: 'add-product'}" class="btn btn-primary">Add a new product</router-link>
        </p>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Manufactured Year</th>
                    <th>Photo</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="product in products" :key="product.id">
                    <td>{{ product.id }}</td>
                    <td>{{ product.name }}</td>
                    <td>{{ product.manufactured_year }}</td>
                    <td><img width="100" :src="product.photo" alt=""></td>
                    <td>{{ product.created_at }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <router-link :to="{name: 'edit-product', params: { id: product.id }}"
                                         class="btn btn-primary">
                                Edit
                            </router-link>
                            <button class="btn btn-danger" @click="deleteProduct(product.id)">Delete</button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            products: []
        }
    },
    created() {
        this.axios
            .get('/api/products')
            .then(response => {
                this.products = response.data.data;
            });
    },
    methods: {
        deleteProduct(id) {
            if (confirm("Do you really want to delete?")) {
                this.axios
                    .delete(`/api/products/${id}`)
                    .then(response => {
                        let i = this.products.map(item => item.id).indexOf(id); // find index of your object
                        this.products.splice(i, 1);
                        this.flash(response.data.msg, 'success', {
                            timeout: 3000
                        });
                    });
            }
        }
    }
}
</script>
