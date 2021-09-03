<template>
    <div>
        <h3 class="text-center">Add Product</h3>
        <div class="row">
            <div class="col-md-6">
                <form @submit.prevent="addProduct">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" :class="errors.name ? 'has-error' : ''" class="form-control"
                               v-model="product.name">
                        <span v-if="errors.name" class="form-error">{{ errors.name[0] }}</span>
                    </div>
                    <div class="form-group">
                        <label>Manufactured Year</label>
                        <input min="1990" type="number" :class="errors.manufactured_year ? 'has-error' : ''"
                               class="form-control" v-model="product.manufactured_year">
                        <span v-if="errors.manufactured_year" class="form-error">{{
                                errors.manufactured_year[0]
                            }}</span>
                    </div>

                    <div class="form-group">
                        <label>Photo</label>
                        <input type="file" id="file" ref="file" v-on:change="handleFileUpload()"/>
                        <br>
                        <span v-if="errors.photo" class="form-error">{{ errors.photo[0] }}</span>
                    </div>

                    <button type="submit" class="btn btn-primary">Add Product</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            product: {},
            errors: {}
        }
    },
    methods: {
        addProduct() {
            let formData = new FormData();
            formData.append('name', this.product.name ? this.product.name : '');
            formData.append('manufactured_year', this.product.manufactured_year ? this.product.manufactured_year : '');
            formData.append('photo', this.product.photo ? this.product.photo : '');

            this.axios
                .post('/api/products', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(response => {
                    this.$router.push({name: 'products-list'});
                    this.flash(response.data.msg, 'success', {
                        timeout: 3000
                    });
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                    this.flash(error.response.data.message, 'error', {
                        timeout: 3000
                    });
                })
        },
        handleFileUpload() {
            this.product.photo = this.$refs.file.files[0];
        }
    }
}
</script>
