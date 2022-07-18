<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div>
                <h3 class="card-title">{{$t('groups')}} &nbsp</h3>
                <i class="fa fa-plus-square" @click="newModal"></i>
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
              <b-table sticky-header id="Groups" no-local-sorting responsive hover :items="groups.data" :fields="fieldsGroups"  :sort-by.sync="sortBy"  :sort-desc.sync="sortDesc"  @sort-changed="getResults(1)">


                <template v-slot:cell(description)="data">
                  <p v-if="data.item.description && data.item.description.length<=100">
                    {{ data.item.description}}
                  </p>
                  <p v-else-if="data.item.description && data.item.description.length>100">
                    {{ data.item.description.substring(0,100)+"..."}}
                  </p>

                </template>

                <template v-slot:cell(group_pdf)="data">
                  <a  href="#" @click="downloadPictureGroupFromGroup(data.item.id)">
                    <i class="fa fa-download blue"></i>
                  </a>
                </template>

                <template v-slot:cell(download_word_from_gruop)="data">
                  <a v-if="data.item.path_word"  :href="data.item.path_word">
                    <i class="fa fa-download"></i>
                  </a>
                </template>




                <template v-slot:cell(name)="data">
                  <router-link :to="{ name: 'showGroup', params: {id: data.item.id } }">
                    {{ data.value}}
                  </router-link>
                </template>

                <template v-slot:cell(action)="data">

                  <a  href="#" @click="editModal(data.item)">
                    <i class="fa fa-edit blue"></i>
                  </a>

                  <a  href="#" @click="deleteGroup(data.item)">
                    <i class="fa fa-trash red"></i>
                  </a>
                </template>

              </b-table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <pagination :limit=2 :data="groups" @pagination-change-page="getResults" >
                <span slot="prev-nav">&lt; {{$t('previous')}}</span>
                <span slot="next-nav">{{$t('next')}} &gt;</span>
              </pagination>
              <p>{{$t('showing')}} {{this.groups.from}} - {{this.groups.to}} {{$t('to')}} {{this.groups.total}} {{$t('results')}} </p>
            </div>
          </div>
          <!-- /.card -->
        </div>
      </div>

      <!-- Modal -->
      <div class="modal fade bd-example-modal-lg" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" v-show="!editmode" id="addNewLabel">{{$t('save')}}</h5>
              <h5 class="modal-title" v-show="editmode" id="addNewLabel">{{$t('update')}}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form enctype="multipart/form-data" @submit.prevent="editmode ? updateGroup() : createGroup()">

              <div class="modal-body">
                <div class="form-group">
                  <label for="name">{{$t('name')}}</label>
                  <input v-model="form.name" type="text" name="name" id="name" :placeholder="$t('name')" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                  <has-error :form="form" field="name"></has-error>
                </div>

                <div class="form-group">
                  <label for="name">{{$t('description')}}</label>
                  <textarea v-model="form.description"
                            type="text"
                            cols="30"
                            rows="10"
                            name="description"
                            id="description"
                            :placeholder="$t('description')"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.has('description') }">
                  </textarea>
                  <has-error :form="form" field="description"></has-error>
                </div>

                <div class="form-group">
                  <label for="word">{{$t('wordUpload')}}</label>
                  <b-form-file
                    id="word"
                    ref="uploadWord"
                    :file-name-formatter="formatNames"
                    v-model="wordDoc"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('wordDoc') }"
                    :placeholder="$t('file_selected')"
                    :browse-text="$t('browse_file')"
                    :drop-placeholder="$t('drop_file')"
                  ></b-form-file>
                  <p class="small">{{$t('format_word')}}</p>
                  <has-error :form="form" field="wordDoc"></has-error>
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
  import VButton from "../../components/Button";

  export default {
    middleware:'role:Admin,Teacher,SUDO,Super admin',

    components: {
      VButton,
    },

    data() {
      return {
        busy:false,
        wordDoc:[],
        groupsSearch: '',
        selectedGroups: null,
        numberPerPage:50,
        filter:null,
        sortBy: 'name',
        sortDesc: false,
        editmode: false,
        groups : {},
        search:"",
        fieldsGroups: [
          {
            label: 'ID',
            key: 'id',
            sortable: true,
          },
          {
            label: this.$t('name') ,
            key: 'name',
            sortable: true,
            stickyColumn:true,
          },
          {
            label: this.$t('description') ,
            key: 'description',
            sortable: true,
            stickyColumn:true,
          },
          {
            label: this.$t('owner') ,
            key: 'user.name',
            sortable: true,
            stickyColumn:true,
          },

          {
            label: this.$t('created_at'),
            key: 'updated_at',
            sortable: true,
            formatter: (value) => {
              return moment(value).format('DD.MM.YYYY');
            }
          },
          {
            label: this.$t('markers_count'),
            key: 'markers_count',
          },
          {
            label: this.$t('group_pdf'),
            key: 'group_pdf',
          },
          {
            label: this.$t('download_word_from_gruop') ,
            key: 'download_word_from_gruop',

          },
          {
            label: this.$t('action'),
            key:'action',

          },
        ],
        formFile: new FormData,
        form: new Form({
          id:'',
          name : '',
          description:'',
          updated_at: '',
          wordDoc:'',
        })
      }
    },


    methods: {

      formatNames(files) {
        if (files.length === 1) {
          return files[0].name
        } else if (files.length === 2 || files.length === 3 || files.length === 4) {
          return `${files.length} odabrane datoteke`
        } else {
          return `${files.length} odabranih datoteka`
        }
      },

      openModalGroup() {
        $('#create_group').modal('show');
      },

      downloadPictureGroupFromGroup(id) {
        axios({
          method: 'get',
          url: '/api/group/' + id + '/export-marker-picture',
          responseType: 'arraybuffer',

        }).then(function (response) {

          let blob = new Blob([response.data], {type: 'application/pdf'});
          let link = document.createElement('a');
          link.href = window.URL.createObjectURL(blob);
          link.download = "PicturesGroup.pdf";
          link.click();
        })

      },

      searchit: _.debounce(() => {Fire.$emit('searching');},500),

      getResults(page = 1) {
        let query = this.filter;
        let sort = this.sortBy;
        let sortDesc = this.sortDesc;
        let perPage= this.numberPerPage;

        let route="/api/group?page="+page+"&sort="+sort+"&desc="+sortDesc+"&perPage="+perPage;
        if(query){
          route+="&q="+query;
        }

        axios.get(route)
          .then(response => {
            this.groups = response.data;
          });


      },

      updateGroup(){
        this.$Progress.start();
        this.form.put('/api/group/'+this.form.id)
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

      editModal(marker){
        this.editmode = true;
        this.deleteClassModal();
        this.form.reset();
        $('#addNew').modal('show');
        this.form.fill(marker);
      },

      newModal(){
        this.deleteClassModal();
        this.editmode = false;
        this.form.reset();
        $('#addNew').modal('show');
      },

      deleteGroup(group){
        swal.fire({
          title:  this.$t('delete_modal_title')+ ' "'+group.name+'" ?',
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
            this.form.delete('/api/group/'+group.id).then((data)=>{
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

      createGroup(){
        this.busy=true;
        this.$Progress.start();
        this.formFile.append('wordDoc', this.wordDoc);
        this.formFile.append('name', this.form.name);
        this.formFile.append('description', this.form.description);
        this.formFile.append('updated_at', this.form.updated_at);

        axios.post('/api/group',this.formFile).then((data)=>{
            if(data.data.success){
              swal.fire({
                type: 'success',
                title: data.data.message
              });

              Fire.$emit('AfterCreate');
              $('#addNew').modal('hide');

              this.$refs.uploadWord.reset();
              this.wordDoc=[];
              this.formFile= new FormData;
              this.form.reset();

            }else{
              swal.fire(
                data.data.data,
                data.data.message,
                "error"
              );

              this.$refs.uploadWord.reset();
              this.wordDoc=[];
              this.formFile= new FormData;
              this.form.reset();

              Fire.$emit('AfterCreate');
              $('#addNew').modal('hide')

              this.$Progress.finish();
              this.busy=false;
            }
        }).catch((error)=>{

            if (error.response.status == 422){
              this.form.errors.errors = error.response.data.errors;
              this.busy=false;
            }

        })
      },

    },

    created() {
      this.getResults();

      Fire.$on('searching',() => {
        this.getResults(1);
      });


      Fire.$on('AfterCreate',() => {
        this.getResults();
      });

    },

  }
</script>
