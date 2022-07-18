<template>
    <section class="content">
        <div class="container-fluid" v-if="$gate.isSuperAdminOrAdmin()">
            <b-tabs content-class="mt-3">
                <b-tab title="Informacije" active>
                    <div class="row mt-5">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Grad <b>{{city.name}}</b></div>
                                    <div class="card-tools">
                                    </div>
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <b-table  id="CityTeacherTable" responsive hover :items="[city]" :fields="fieldsCity">

                                        <template v-slot:cell(action)="data">
                                            <a href="#" @click="editModal(data.item)">
                                                <i class="fa fa-edit blue"></i>
                                            </a>
                                        </template>

                                        <template v-slot:cell(number_children)="data">
                                            {{data.item.children.length}}
                                        </template>

                                    </b-table>

                                </div>
                            </div>
                        </div>
                    </div>
                </b-tab>

                <b-tab title="Korisnici" >
                    <div class="row mt-5">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Popis korisnika grada <b>{{city.name}}</b></div>
                                    <div class="card-tools">
                                        <b-form-group>
                                            <b-input-group>
                                                <b-form-input
                                                    v-model="filter"
                                                    type="search"
                                                    placeholder="Pretraži korisnike"
                                                ></b-form-input>
                                                <b-input-group-append>
                                                    <b-button :disabled="!filter" @click="filter = ''">Obriši</b-button>
                                                </b-input-group-append>
                                            </b-input-group>
                                        </b-form-group>
                                    </div>
                                </div>
                                <b-table id="ChildrenTable" responsive hover :items="city.children" :fields="fieldsChildren" :per-page="perPage" :current-page="currentPage" :filter="filter">
                                    <template v-slot:cell(index)="data">
                                        {{ data.index + 1 + (perPage*currentPage)-perPage/*+teachers.to-teachers.per_page*/}}
                                    </template>
                                    <template v-slot:cell(name)="data">
                                        <router-link :to="{ name: 'showChild', params: {id: data.item.id } }">
                                            {{ data.value}}
                                        </router-link>
                                    </template>

                                </b-table>
                                <b-pagination
                                    v-model="currentPage"
                                    :total-rows="city.children.length"
                                    :per-page="perPage"
                                    :current-page="currentPage"
                                    aria-controls="ChildrenTable"
                                ></b-pagination>
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </b-tab>

            </b-tabs>

            <!-- Modal -->
            <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addNewLabel">Ažuriraj</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="updateCity">
                            <div class="modal-body">
                                <div class="form-group">
                                    <input v-model="form.name" type="text" name="name"
                                           placeholder="Naziv"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                    <has-error :form="form" field="name"></has-error>
                                </div>

                                <div class="form-group">
                                    <input v-model="form.postal_code" type="number" name="postal_code"
                                           placeholder="Poštanski broj"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('postal_code') }">
                                    <has-error :form="form" field="postal_code"></has-error>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Natrag</button>
                                <button type="submit" class="btn btn-success">Ažuriraj</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

        <div v-else>
            <not-found></not-found>
        </div>

    </section>
</template>

<script>
    import moment from 'moment';
    export default {
        data() {
            return {
                perPage: 20,
                currentPage:1,
                filter: null,
                city : {},
                fieldsCity: [
                    {
                        label: 'ID',
                        key: 'id',
                    },
                    {
                        label: 'Naziv',
                        key: 'name',
                        sortable: true,
                        stickyColumn:true,
                    },
                    {
                        label: 'Poštanski broj',
                        key: 'postal_code',
                        sortable: true,
                        // stickyColumn:true,
                    },
                    {
                        label: 'Broj djece',
                        key: 'number_children',
                        sortable: true,
                    },
                    {

                        label: 'Zadnja izmjena',
                        key: 'updated_at',
                        sortable: true,
                        formatter: (value) => {
                            return moment(value).format('DD.MM.YYYY');
                        }
                    },
                    {
                        label: 'Akcije',
                        key:'action',

                    },
                ],
                fieldsChildren: [
                    {
                        label: '#',
                        key: 'index',
                    },
                    {
                        label: 'Ime',
                        key: 'name',
                        sortable: true,
                        stickyColumn:true,
                    },
                    {
                        label: 'Prezime',
                        key: 'last_name',
                        sortable: true,
                        // stickyColumn:true,
                    },
                    {
                        label: 'OIB',
                        key: 'oib',
                        sortable: true
                    },
                    {
                        label: 'Datum rođenja',
                        key: 'date_of_birth',
                        sortable: true,
                        // variant: 'danger',
                        formatter: (value) => {
                            return moment(value).format('DD.MM.YYYY');
                        }
                    },
                    {
                        label: 'Dob',
                        key: 'age',
                        sortable: true,
                        formatter: (value, key, item) => {
                            return  this.getFullDate(item.date_of_birth)
                        }
                    },
                    {
                        label: 'Spol',
                        key: 'sex',
                        sortable: true
                    },
                    /*{
                        label: 'Adresa',
                        key: 'address',
                        sortable: true
                    },*/
                    {
                        label: 'Mjesto rođenja',
                        key: 'place_of_birth',
                        sortable: true
                    },


                    {
                        label: 'ID broj',
                        key: 'generate_number',
                        sortable: true
                    },


                ],

                form: new Form({
                    id:'',
                    name : '',
                    postal_code: '',
                    updated_at: '',
                }),

            }
        },
        methods: {
            updateCity(){
                this.$Progress.start();
                this.form.put('/api/city/'+this.form.id)
                    .then((data) => {
                        // success
                        $('#addNew').modal('hide');
                        if(data.data.success){
                            swal.fire(
                                data.data.data,
                                data.data.message,
                                'success'
                            )
                        }else{
                            swal.fire(
                                data.data.message,
                                data.data,
                                "error");
                        }
                        this.$Progress.finish();
                        Fire.$emit('AfterCreate');
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    });

            },
            editModal(city){
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(city);
            },
            loadCity(){
                    axios.get('/api/city/'+this.$route.params.id).then(({ data }) => (this.city = data.data));
            },
            getFullDate(item){

                var today = new Date();
                let fullDate =  today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                return  moment(fullDate).diff(moment(item),'year')
            },


        },
        created() {
            this.loadCity();
            Fire.$on('AfterCreate',() => {
                this.loadCity();
            });
        },


    }
</script>
