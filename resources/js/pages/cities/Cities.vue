<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h3 class="card-title">Gradovi &nbsp</h3>
                                <i v-if="$store.state.auth.user && $can($store.state.auth.user.permission,'city-store')" class="fa fa-plus-square" @click="newModal"></i>
                            </div>

                            <div class="card-tools">

                              <b-form-group>
                                <b-input-group>
                                  <b-form-input
                                    v-model="filter"
                                    type="search"
                                    @keyup="searchit"
                                    placeholder="Pretraži"
                                  ></b-form-input>
                                  <b-input-group-append @click="searchit">
                                    <b-button :disabled="!filter" @click="filter = ''">Obriši</b-button>
                                  </b-input-group-append>
                                </b-input-group>
                              </b-form-group>

                              <b-form-group
                                label="Prikaz"
                                label-for="per-page-select"
                                label-cols-sm="6"
                                label-cols-md="4"
                                label-cols-lg="3"
                                label-align-sm="right"
                                label-size="sm"
                                class="mb-0"
                              >
                                <b-form-select
                                  id="per-page-select"
                                  v-model="numberPerPage"
                                  :options=this.$pageOptions
                                  @change="getResults(1)"
                                  size="sm"
                                ></b-form-select>
                              </b-form-group>

                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                          <b-table sticky-header id="Gradovi" no-local-sorting responsive hover :items="cities.data" :fields="fieldsCities"  :sort-by.sync="sortBy"  :sort-desc.sync="sortDesc"  @sort-changed="getResults(1)">

                                <template v-slot:cell(index)="data">
                                    {{ data.index + 1/*+teachers.to-teachers.per_page*/}}
                                </template>

                                <template v-slot:cell(name)="data">
                                    <router-link :to="{ name: 'showCity', params: {id: data.item.id } }">
                                        {{ data.value}}
                                    </router-link>
                                </template>

                                <template v-slot:cell(action)="data">
                                    <a v-if="$store.state.auth.user &&$can($store.state.auth.user.permission,'city-update')" href="#" @click="editModal(data.item)">
                                        <i class="fa fa-edit blue"></i>
                                    </a>

                                    <a v-if="$store.state.auth.user && $can($store.state.auth.user.permission,'city-delete')" href="#" @click="deleteCity(data.item)">
                                        <i class="fa fa-trash red"></i>
                                    </a>
                                </template>

                            </b-table>
                        </div>
                        <!-- /.card-body -->
                      <div class="card-footer">
                        <pagination :limit=2 :data="cities" @pagination-change-page="getResults" >
                          <span slot="prev-nav">&lt; Prethodna</span>
                          <span slot="next-nav">Sljedeća &gt;</span>
                        </pagination>
                        <p>Prikaz {{this.cities.from}} do {{this.cities.to}} od {{this.cities.total}} prikaza </p>
                      </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode" id="addNewLabel">Unos</h5>
                            <h5 class="modal-title" v-show="editmode" id="addNewLabel">Ažuriranje</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="editmode ? updateCity() : createCity()">
                            <div class="modal-body">

                                <div class="form-group">
                                    <input v-model="form.name" type="text" name="name"
                                           placeholder="Naziv"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                    <has-error :form="form" field="name"></has-error>
                                </div>

                                <div class="form-group">
                                    <input v-model="form.post_code" type="number" name="post_code"
                                           placeholder="Poštanski broj"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('post_code') }">
                                    <has-error :form="form" field="post_code"></has-error>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Natrag </button>
                                <button v-show="editmode" type="submit" class="btn btn-success">Ažuriraj</button>
                                <button v-show="!editmode" type="submit" class="btn btn-primary">Unos</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import {mapGetters} from "vuex";

    export default {
      middleware: 'role:Admin,Super admin',
      titleTemplate:'Gradovi',

      data() {
          return {
              //user: this.$store.state.auth.user.permission,
              numberPerPage:50,
              filter:null,
              sortBy: 'name',
              sortDesc: false,
              editmode: false,
              cities : {},
              search:"",
              fieldsCities: [
                  {
                      label: '#',
                      key: 'index',
                  },
                  {
                      label: 'Naziv' ,
                      key: 'name',
                      sortable: true,
                      stickyColumn:true,
                  },
                  {
                      label: 'Poštanski broj',
                      key: 'post_code',
                      sortable: true,
                  },
                  {
                      label: 'Broj korisnika',
                      key: 'users_count',
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
              form: new Form({
                  id:'',
                  name : '',
                  post_code: '',
                  updated_at: '',
              })
          }
      },


      methods: {

          searchit: _.debounce(() => {Fire.$emit('searching');},500),

          getResults(page = 1) {
            let query = this.filter;
            let sort = this.sortBy;
            let sortDesc = this.sortDesc;
            let perPage= this.numberPerPage;
            if(query){
              axios.get('/api/city?q=' + query+'&page=' + page+'&sort=' + sort+'&desc='+sortDesc+'&perPage='+perPage)
                .then(response => {
                  this.cities = response.data;
                });
            }else{
              axios.get('/api/city?page=' + page+'&sort=' + sort+'&desc='+sortDesc+'&perPage='+perPage)
                .then(response => {
                  this.cities = response.data;
                });
            }

          },

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
                              data.data.data,
                              data.data.message,
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
              this.editmode = true;
              this.deleteClassModal();
              this.form.reset();
              $('#addNew').modal('show');
              this.form.fill(city);
          },
          newModal(){
              this.deleteClassModal();
              this.editmode = false;
              this.form.reset();
              $('#addNew').modal('show');
          },
          deleteCity(city){
              swal.fire({
                  title: 'Da li ste sigurni da želite obrisati grad: '+city.name+'?',
                  text: "Nećete moći poništiti ovo!",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Da, obriši!'
              }).then((result) => {

                  // Send request to the server
                  if (result.value) {
                      this.form.delete('/api/city/'+city.id).then((data)=>{
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

                          Fire.$emit('AfterCreate');
                      }).catch(()=> {
                          swal.fire('GREŠKA','Nešto je pošlo po zlu. Obratite se administratoru', "warning");
                      });
                  }
              })
          },
          /*loadCity(){
              axios.get("/api/city").then(({ data }) => (this.cities = data));

          },*/
          deleteClassModal(){
              $('.is-invalid').removeClass("is-invalid");
              $(".invalid-feedback").removeClass("invalid-feedback");
              $(".help-block").text('');
          },

          createCity(){
              this.$Progress.start();

              this.form.post('/api/city')
                  .then((data)=>{
                    if(data.data.success){
                      toast.fire({
                        type: 'success',
                        title: data.data.message
                      });
                    }else{
                      swal.fire(
                        data.data.data,
                        data.data.message,
                        "error");
                    }
                      Fire.$emit('AfterCreate');
                      $('#addNew').modal('hide')


                      this.$Progress.finish();

                  })
                  .catch(()=>{

                  })
          }
      },

      created() {


          Fire.$on('searching',() => {
            let query = this.filter;
            axios.get('/api/city?q=' + query)
              .then((response) => {
                this.cities = response.data
              })
              .catch(() => {
                this.$Progress.fail();
              })
          });
         //console.log(this.$root.$metaInfo.title)
          this.getResults();
          Fire.$on('AfterCreate',() => {
            this.getResults();
          });
            //    setInterval(() => this.loadUsers(), 3000);
        },

    }
</script>
