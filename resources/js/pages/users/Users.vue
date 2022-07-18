<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div>
                <h3 class="card-title">{{$t('users')}} &nbsp</h3>
                <i v-if="$store.state.auth.user && $can($store.state.auth.user,'user-create')" class="fa fa-plus-square" @click="newModal"></i>

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
              <b-table sticky-header="500px" id="Gradovi" no-local-sorting responsive hover :items="users.data" :fields="fieldsUsers"  :sort-by.sync="sortBy"  :sort-desc.sync="sortDesc"  @sort-changed="getResults(1)">

                <template v-slot:cell(index)="data">
                  {{ data.index + 1/*+teachers.to-teachers.per_page*/}}
                </template>

                <template v-slot:cell(roles_count)="data">
                    <template v-for="role in data.item.roles">
                      <b-link  :to="{ name: 'showRole', params: { id: role.id } }">
                        <b-badge pill variant="info">
                          {{role.name}}
                        </b-badge>
                      </b-link>&nbsp;
                    </template>
                </template>
                <template v-slot:cell(name)="data">
                  <b-link :to="{ name: 'showUser', params: {id: data.item.id } }">
                    {{ data.value}}
                  </b-link>
                </template>

                <template v-slot:cell(action)="data" v-if="$store.state.auth.user">
                  <a v-if="$can($store.state.auth.user,'user-edit')"  href="#" @click="editModal(data.item)">
                    <i class="fa fa-edit blue"></i>
                  </a>

                  <a  v-if="$can($store.state.auth.user,'user-delete')" href="#" @click="deleteUser(data.item)">
                    <i class="fa fa-trash red"></i>
                  </a>

                  <a v-if="$can($store.state.auth.user,'user-reset-password')" href="#" @click="openModalResetPassword(data.item)">
                    <i class="fa fa-key red"></i>
                  </a>

                  <a v-if="$can($store.state.auth.user,'user-direct-permission')" href="#" @click="openModalPermissionsForUser(data.item)">
                    <i class="fa fa-user-shield red"></i>
                  </a>

                </template>

              </b-table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <pagination :limit=2 :data="users" @pagination-change-page="getResults" >
                <span slot="prev-nav">&lt; {{$t('previous')}}</span>
                <span slot="next-nav">{{$t('next')}} &gt;</span>
              </pagination>
              <p>{{$t('showing')}} {{this.users.from}} - {{this.users.to}} {{$t('to')}} {{this.users.total}} {{$t('results')}} </p>
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
            <form @submit.prevent="editmode ? updateUser() : createUser()" enctype="multipart/form-data">
              <div class="modal-body">

                <div class="form-group">
                  <label class="col-form-label" for="name">{{$t('name')}}</label>
                  <input v-model="form.name" type="text" name="name" id="name"
                         placeholder="Naziv"
                         class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                  <has-error :form="form" field="name"></has-error>
                </div>

                <div class="form-group">
                  <label for="email">{{$t('email')}}</label>
                  <input v-model="form.email" type="email" name="email" id="email"
                         placeholder="Email"
                         class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                  <has-error :form="form" field="email"></has-error>
                </div>

                <div class="form-group">
                  <label for="roles">{{$t('roles')}}</label>
                  <b-form-select name="users"
                                 v-model="form.roles"
                                 :options="roles"
                                 multiple
                                 id="roles"
                                 value-field="id"
                                 text-field="name"
                                 :select-size="5"
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

      <!-- Modal reset password-->
      <div class="modal fade" id="modalResetPassword" tabindex="-1" role="dialog" aria-labelledby="modalResetPasswordLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalResetPasswordLabel">{{$t('reset_password')}}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form @submit.prevent="resetPassword()">
              <div class="modal-body">

                <!-- Password -->
                <div class="form-group row">
                  <label class="col-md-3 col-form-label text-md-right" for="password">{{ $t('new_password') }}</label>
                  <div class="col-md-7">
                    <input v-model="form2.password" :class="{ 'is-invalid': form2.errors.has('password') }" class="form-control" id="password" type="password" name="password">
                    <has-error :form="form2" field="password" />
                  </div>
                </div>

                <!-- Password Confirmation -->
                <div class="form-group row">
                  <label class="col-md-3 col-form-label text-md-right" for="password_confirmation">{{ $t('confirm_password') }}</label>
                  <div class="col-md-7">
                    <input v-model="form2.password_confirmation" :class="{ 'is-invalid': form2.errors.has('password_confirmation') }" id="password_confirmation" class="form-control" type="password" name="password_confirmation">
                    <has-error :form="form2" field="password_confirmation" />
                  </div>
                </div>

                <!--Send mail-->
                <div class="form-group row">
                  <label class="col-md-3 col-form-label text-md-right" for="send_mail">{{ $t('send_mail') }}</label>
                  <div class="col-md-7">
                    <!-- <b-form-checkbox
                      id="send_mail"
                      v-model="form2.send_mail"
                      name="send_mail"
                      value="true"
                      unchecked-value="false"
                    ></b-form-checkbox>-->
                    <input v-model="form2.send_mail" :class="{ 'is-invalid': form2.errors.has('send_mail') }" id="send_mail" class="form-control" type="checkbox" name="send_mail">
                    <has-error :form="form2" field="send_mail" />
                  </div>
                </div>



              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{$t('modal_back')}}</button>
                <v-button :loading="form2.busy" type="submit" class="btn btn-success">{{$t('modal_update')}}</v-button>
              </div>
            </form>

          </div>
        </div>
      </div>

      <!-- Modal permissions -->
      <div class="modal fade" id="modalPermission" tabindex="-1" role="dialog" aria-labelledby="modalPermissionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalPermissionLabel">{{$t('user_permissions_admin')}}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form @submit.prevent="createUpdatePermissions()">
              <div class="modal-body">

                <div class="form-group">
                  <b-form-select name="permissions"
                                 v-model="form3.permissions"
                                 :options="permissions"
                                 multiple
                                 value-field="id"
                                 text-field="name"
                                 :select-size="8"
                                 class="form-control"
                                 :class="{ 'is-invalid': form.errors.has('permissions') }">
                  </b-form-select>
                  <has-error :form="form3" field="permissions"></has-error>
                </div>

              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{$t('modal_back')}} </button>
                <v-button :loading="form.busy"  type="submit" class="btn btn-success">{{$t('modal_update')}}</v-button>
              </div>
            </form>

          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<script>

  import VButton from "../../components/Button";
  export default {
    components: {VButton},
    scrollToTop: true,
    middleware:'role:Super Admin',

    metaInfo () {
      return { title: this.$t('users') }
    },

    data() {
      return {
        numberPerPage:50,
        filter:null,
        sortBy: 'name',
        sortDesc: false,
        editmode: false,
        users : {},
        roles:[],
        permissions:[],
        search:"",
        fieldsUsers: [
          {
            label: this.$t('#'),
            key: 'index',
          },
          {
            label: this.$t('name') ,
            key: 'name',
            sortable: true,
            stickyColumn:true,
          },
          {
            label:this.$t('email') ,
            key: 'email',
            sortable: true,
            stickyColumn:true,
          },

          {
            label: this.$t('roles'),
            key: 'roles_count',
          },
          {
            label: this.$t('updated_at'),
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
          email : '',
          roles:[],
          updated_at: '',
        }),

        //reset password form
        form2: new Form({
          id:'',
          password:'',
          confirm_password:'',
          send_mail: true,
          updated_at: '',
        }),

        //add permissions for user
        form3: new Form({
          id:'',
          permissions:[],
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

        let route="/api/admin/user?page="+page+"&sort="+sort+"&desc="+sortDesc+"&perPage="+perPage;
        if(query){
          route+="&q="+query;
        }

        axios.get(route)
          .then(response => {
            this.users = response.data;
          });


      },

      updateUser(){

        this.$Progress.start();

        this.form.patch('/api/admin/user/'+this.form.id).then((data) => {
            $('#addNew').modal('hide');

            if(data.data.success){
              this.form.reset();
              swal.fire(
                data.data.data,
                data.data.message,
                'success'
              );
            }else{
              this.form.reset();
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

      resetPassword(){
        this.$Progress.start();

        this.form2.patch('/api/admin/user/'+this.form2.id+'/reset_password').then((data) => {

          $('#modalResetPassword').modal('hide');

          if(data.data.success){
            this.form2.reset();
            swal.fire(
              data.data.data,
              data.data.message,
              'success'
            );
          }else{
            this.form2.reset();
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

      editModal(user){
        this.editmode = true;
        this.deleteClassModal();
        this.form.reset();
        $('#addNew').modal('show');
        this.form.fill(user);
        let arrayList =[];

        user.roles.forEach(function (per) {
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

      openModalResetPassword(user){
        this.deleteClassModal();
        this.form2.reset();
        $('#modalResetPassword').modal('show');
        this.form2.fill(user);
        this.form2.send_mail=true;
      },

      openModalPermissionsForUser(user){
        this.deleteClassModal();
        this.form3.reset();
        $('#modalPermission').modal('show');
        this.form3.fill(user);
        let arrayList =[];

        user.permissions.forEach(function (per) {
          arrayList.push(per.id);
        });

        this.form3.permissions=arrayList;
      },

      deleteUser(user){
        swal.fire({
          title:  this.$t('delete_modal_title')+ ' "'+user.name+'" ?',
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
            this.form.delete('/api/admin/user/'+user.id).then((data)=>{
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

      deleteClassModal(){
        $('.is-invalid').removeClass("is-invalid");
        $(".invalid-feedback").removeClass("invalid-feedback");
        $(".help-block").text('');
      },

      createUser(){

        this.form.post('/api/admin/user')
          .then((data)=>{
            if(data.data.success){
              swal.fire({
                type: 'success',
                title: data.data.message
              });
            }else{
              this.form.reset();
              swal.fire(
                data.data.data,
                data.data.message,
                "error");
            }
            Fire.$emit('AfterCreate');
            $('#addNew').modal('hide')
          })
          .catch(()=>{

          })
      },

      createUpdatePermissions(){

        this.form3.patch('/api/admin/user/'+this.form3.id+'/permissions').then((data) => {
            if(data.data.success){
              swal.fire({
                type: 'success',
                title: data.data.message
              });
            }else{
              this.form.reset();
              swal.fire(
                data.data.data,
                data.data.message,
                "error");
            }
            Fire.$emit('AfterCreate');
            $('#modalPermission').modal('hide')
          })
          .catch(()=>{

          })
      },

      loadRoles(){
        axios.get("/api/roles").then(({ data }) => {this.roles = data});
      },

      loadPermissions(){
        axios.get("/api/permissions").then(({ data }) => {this.permissions = data});
      },
    },


    created() {
      this.getResults();
      this.loadRoles();
      this.loadPermissions();

      Fire.$on('searching',() => {
        this.getResults(1);
      });


      Fire.$on('AfterCreate',() => {
        this.getResults();
      });

    },

  }
</script>
