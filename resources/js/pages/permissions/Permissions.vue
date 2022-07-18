<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div>
                <h3 class="card-title">{{$t('permissions')}} &nbsp</h3>
                <i v-if="$store.state.auth.user && $can($store.state.auth.user,'permission-create')" class="fa fa-plus-square" @click="newModal"></i>
              </div>

              <div class="card-tools">

                <b-form-group>
                  <b-input-group>
                    <b-form-input
                      v-model="filter"
                      type="search"
                      @keyup="searchit"
                      :placeholder="$t('search')"
                    ></b-form-input>
                    <b-input-group-append @click="searchit">
                      <b-button :disabled="!filter" @click="filter = ''">{{$t('delete')}}</b-button>
                    </b-input-group-append>
                  </b-input-group>
                </b-form-group>

                <b-form-group
                  :label="$t('view')"
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
              <b-table sticky-header id="Gradovi" no-local-sorting responsive hover :items="permissions.data" :fields="fieldsPermissions"  :sort-by.sync="sortBy"  :sort-desc.sync="sortDesc"  @sort-changed="getResults(1)">

                <template v-slot:cell(index)="data">
                  {{ data.index + 1/*+teachers.to-teachers.per_page*/}}
                </template>

                <template v-slot:cell(name)="data">
                  <router-link :to="{ name: 'showPermission', params: {id: data.item.id } }">
                    {{ data.value}}
                  </router-link>
                </template>

                <template v-slot:cell(action)="data" v-if="$store.state.auth.user">
                  <a v-if="$can($store.state.auth.user,'permission-edit')" href="#" @click="editModal(data.item)">
                    <i class="fa fa-edit blue"></i>
                  </a>

                  <a  v-if="$can($store.state.auth.user,'permission-delete')" href="#" @click="deletePermission(data.item)">
                    <i class="fa fa-trash red"></i>
                  </a>
                </template>

              </b-table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <pagination :limit=2 :data="permissions" @pagination-change-page="getResults" >
                <span slot="prev-nav">&lt; {{$t('previous')}}</span>
                <span slot="next-nav">{{$t('next')}} &gt;</span>
              </pagination>
              <p>{{$t('showing')}} {{this.permissions.from}} - {{this.permissions.to}} {{$t('to')}} {{this.permissions.total}} {{$t('results')}} </p>
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
              <h5 class="modal-title" v-show="!editmode" id="addNewLabel">{{$t('save')}}</h5>
              <h5 class="modal-title" v-show="editmode" id="addNewLabel">{{$t('update')}}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form @submit.prevent="editmode ? updatePermission() : createPermission()">
              <div class="modal-body">

                <div class="form-group">
                  <label for="name">{{$t('name')}}</label>
                  <input v-model="form.name" type="text" name="name" id="name"
                         placeholder="Naziv"
                         class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                  <has-error :form="form" field="name"></has-error>
                </div>

                <div class="form-group">
                  <label for="permissions">{{$t('permissions')}}</label>
                  <b-form-select name="permissions"
                                 id="permissions"
                                 v-model="form.roles"
                                 :options="roles"
                                 multiple
                                 value-field="id"
                                 text-field="name"
                                 :select-size="8"
                                 class="form-control"
                                 :class="{ 'is-invalid': form.errors.has('roles') }">
                  </b-form-select>
                  <has-error :form="form" field="roles"></has-error>
                </div>

              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{$t('back')}} </button>
                <v-button :loading="form.busy"  v-show="editmode" type="submit" class="btn btn-success">{{$t('update')}}</v-button>
                <v-button :loading="form.busy"  v-show="!editmode" type="submit" class="btn btn-primary">{{$t('save')}}</v-button>
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
  import VButton from "../../components/Button";

  export default {
    components: {VButton},
    middleware:'role:Super Admin',
    // middleware: 'permission:Admin,Super admin',

    metaInfo () {
      return { title: this.$t('permissions') }
    },

    data() {
      return {

        numberPerPage:50,
        filter:null,
        sortBy: 'name',
        sortDesc: false,
        editmode: false,
        permissions : {},
        roles:[],
        search:"",
        fieldsPermissions: [
          {
            label:  this.$t('#'),
            key: 'index',
          },
          {
            label:  this.$t('name') ,
            key: 'name',
            sortable: true,
            stickyColumn:true,
          },
          {
            label:  this.$t('roles'),
            key: 'roles_count',
          },
          {
            label:  this.$t('updated_at'),
            key: 'updated_at',
            sortable: true,
            formatter: (value) => {
              return moment(value).format('DD.MM.YYYY');
            }
          },
          {
            label: this.$t('action'),
            key:'action',

          },
        ],
        form: new Form({
          id:'',
          name : '',
          roles:[],
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

        let route="/api/permission?page="+page+"&sort="+sort+"&desc="+sortDesc+"&perPage="+perPage;
        if(query){
          route+="&q="+query;
        }

        axios.get(route)
          .then(response => {
            this.permissions = response.data;
          });


      },

      updatePermission(){
        this.$Progress.start();
        this.form.put('/api/permission/'+this.form.id)
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
      editModal(permission){
        this.editmode = true;
        this.deleteClassModal();
        this.form.reset();
        $('#addNew').modal('show');
        this.form.fill(permission);
        let arrayList =[];

        permission.roles.forEach(function (per) {
          arrayList.push(per.id);
        });

        this.form.roles=arrayList;
      },
      newModal(){
        this.deleteClassModal();
        this.editmode = false;
        this.form.reset();
        $('#addNew').modal('show');
      },
      deletePermission(permission){
        swal.fire({
          title:  this.$t('delete_modal_title')+ ' "'+permission.name+'" ?',
          text: this.$t('delete_modal_text') ,
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: this.$t('confirmButtonText'),
          cancelButtonText: this.$t('cancelButtonText')
        }).then((result) => {

          // Send request to the server
          if (result.value) {
            this.form.delete('/api/permission/'+permission.id).then((data)=>{
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
              swal.fire(this.t('error'),this.t('error_message'), "warning");
            });
          }
        })
      },
      /*loadPermission(){
          axios.get("/api/permission").then(({ data }) => (this.permissions = data));

      },*/
      deleteClassModal(){
        $('.is-invalid').removeClass("is-invalid");
        $(".invalid-feedback").removeClass("invalid-feedback");
        $(".help-block").text('');
      },

      createPermission(){
        this.$Progress.start();

        this.form.post('/api/permission')
          .then((data)=>{
            if(data.data.success){
              swal.fire({
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
      },
      loadRoles(){
        axios.get("/api/roles").then(({ data }) => {this.roles = data});
      },
    },


    created() {
      this.getResults();
      this.loadRoles();

      Fire.$on('searching',() => {
        this.getResults(1);
      });


      Fire.$on('AfterCreate',() => {
        this.getResults();
      });

    },

  }
</script>
