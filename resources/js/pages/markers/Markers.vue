<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div>
                <h3 class="card-title">{{$t('markers')}} &nbsp</h3>
                <i class="fa fa-plus-square" @click="newModal"></i>
                <button v-if="form.selected.length > 0" class="btn btn-outline-secondary" @click="openModalGroup" title="Create group">Add in group<i class="fas fa-plus"></i></button>
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
              <b-table sticky-header id="Markers" no-local-sorting responsive hover :items="markers.data" :fields="fieldsMarkers"  :sort-by.sync="sortBy"  :sort-desc.sync="sortDesc"  @sort-changed="getResults(1)">

                <template v-slot:head(select)>
                  <b-form-checkbox v-model="form.selectAll"  @change="select"></b-form-checkbox>
                </template>

                <template v-slot:cell(select)="data">
                  <b-form-checkbox :value="data.item.id" v-model="form.selected"></b-form-checkbox>
                </template>

                <template v-slot:cell(group_count)="data">
                  <template v-for="group in data.item.groups">
<!--                    <b-link   :to="{ name: 'showGroup', params: { id: group.id } }">-->
                      <b-badge  @click="downloadPictureMarkerFromGroup(group.id)" pill variant="info">
                        {{group.name}}
                      </b-badge>
<!--                    </b-link>&nbsp;-->
                  </template>
                </template>

                <template v-slot:cell(marker)="data">
                  <a  href="#" @click="downloadMarker(data.item.image_marker,data.item.id)">
                    <i class="fa fa-download blue"></i>
                  </a>
                </template>

                <!--<template v-slot:cell(name)="data">
                  <router-link :to="{ name: 'showMarker', params: {id: data.item.id } }">
                    {{ data.value}}
                  </router-link>
                </template>-->

                <template v-slot:cell(action)="data">
                  <a  href="#" @click="editModal(data.item)">
                    <i class="fa fa-edit blue"></i>
                  </a>

                  <a  href="#" @click="deleteMarker(data.item)">
                    <i class="fa fa-trash red"></i>
                  </a>

<!--                  <a v-if="data.item.video_path"  :href="data.item.video_path">
                    <i class="fa fa-download"></i>
                  </a>-->

                  <a href="#" @click="downloadContentMarker(data.item.video_path,data.item.type,data.item.name,data.item.id)">
                    <i class="fa fa-download"></i>
                  </a>
                  <a  href="#" @click="getPictureForMarker(data.item.video_path,data.item.type,data.item.text)">
                    <i class="fa fa-eye "></i>
                  </a>

                  <a  href="#" @click="cloneMarker(data.item)">
                    <i class="fa fa-clone "></i>
                  </a>


                </template>

              </b-table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <pagination :limit=2 :data="markers" @pagination-change-page="getResults" >
                <span slot="prev-nav">&lt; {{$t('previous')}}</span>
                <span slot="next-nav">{{$t('next')}} &gt;</span>
              </pagination>
              <p>{{$t('showing')}} {{this.markers.from}} - {{this.markers.to}} {{$t('to')}} {{this.markers.total}} {{$t('results')}} </p>
            </div>
          </div>
          <!-- /.card -->
        </div>
      </div>

      <!-- Modal -->
      <div class="modal fade bd-example-modal-lg" id="openPicture" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" v-show="!editmode" >{{$t('preview')}}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
              <div class="modal-body">
                <div class="imagePreviewWrapper">
                  <img v-if="type==='picture'" :src="src_path_in_modal" height="250px" width="250px">
                  <video v-if="type==='video'" :src="src_path_in_modal" controls id="video_preview" height="250px" width="250px"/>
                  <model-gltf v-if="type==='models'" :src="src_path_in_modal"></model-gltf>
                  <textarea
                    v-if="type==='text'"
                    :value="text_marker"
                    cols="50"
                    rows="10"
                    readonly
                  >
                  </textarea>
<!--                  <p v-if="type==='text'">
                    {{text_marker}}
                  </p>-->
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" @click="stopVideo" id="close_window_preview" data-dismiss="modal">{{$t('back')}} </button>
              </div>
          </div>
        </div>
      </div>



      <!-- Modal -->
      <div class="modal fade bd-example-modal-lg" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" v-show="!editmode" id="addNewLabel">{{$t('save_marker')}}</h5>
              <h5 class="modal-title" v-show="editmode" id="addNewLabel">{{$t('update_marker')}}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form enctype="multipart/form-data" @submit.prevent="editmode ? updateMarker() : createMarker()">

              <div class="modal-body">

                <div class="form-group">
                  <label for="name">{{$t('name')}}</label>
                  <input v-model="form.name" type="text" name="name" id="name" :placeholder="$t('name_placeholder')" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                  <has-error :form="form" field="name"></has-error>
                </div>

                <div class="form-group">
                  <label for="description">{{$t('description')}}</label>
                  <textarea v-model="form.description"
                            type="text"
                            cols="30"
                            rows="5"
                            name="description"
                            id="description"
                            :placeholder="$t('description_placeholder')"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.has('description') }">
                  </textarea>
                  <has-error :form="form" field="description"></has-error>
                </div>

                <b-form-group v-show="!editmode"  v-slot="{ ariaDescribedby }">
                  <label >{{$t('type')}}</label>
                  <b-form-radio-group
                    id="radio-group-1"
                    v-model="form.type"
                    :options="options"
                    :aria-describedby="ariaDescribedby"
                    name="radio-options"
                  ></b-form-radio-group>
                  <has-error :form="form" field="selected"></has-error>
                </b-form-group>

                <div v-show="form.type==='text'">
                    <div class="form-group">
                      <textarea
                        id="text"
                        v-model="form.text"
                        :class="{ 'is-invalid': form.errors.has('text') }"
                        :placeholder="$t('text')"
                        class="form-control"
                      >
                      </textarea>
                      <has-error :form="form" field="text"></has-error>
                    </div>

<!--                    <label for="color_picker">{{$t('color_picker')}}</label>-->
                  <div class="form__field">
                    <div class="form__label">
                      <label for="color_picker">{{$t('color_picker')}}</label>
<!--                      <strong>{{$t('color_picker')}}</strong>-->
                    </div>
                    <div class="form__input">
                      <v-swatches
                        id="color_picker"
                        v-model="form.color"
                        show-fallback
                        fallback-input-type="color"
                        :class="{ 'is-invalid': form.errors.has('color') }"
                        popover-x="left"
                      ></v-swatches>
                      <has-error :form="form" field="color"></has-error>
                    </div>
                  </div>

              </div>

                <div v-show="form.type==='video'">

<!--                    <div class="form-group">

                      <label for="url_video">{{$t('url_video')}}</label>
                      <input
                        id="url_video"
                        v-model="form.url_video"
                        :class="{ 'is-invalid': form.errors.has('url_video') }"
                        :placeholder="$t('url_video')"
                        class="form-control"
                      >
                      &lt;!&ndash; <input v-model="form.text" type="text" name="name" id="text" :placeholder="$t('text')" class="form-control" :class="{ 'is-invalid': form.errors.has('text') }">&ndash;&gt;
                      <has-error :form="form" field="url_video"></has-error>
                    </div>-->

                  <div class="form-group">
                    <b-form-file
                      id="video"
                      ref="uploadfile"
                      :file-name-formatter="formatNames"
                      :class="{ 'is-invalid': form.errors.has('video') }"
                      v-model="attachments"
                      :placeholder="$t('file_selected')"
                      :browse-text="$t('browse_file')"
                      :drop-placeholder="$t('drop_file')"
                    ></b-form-file>
                    <p class="small">{{$t('format_video')}}</p>
                    <has-error :form="form" field="video"></has-error>
                  </div>


                </div>

                <div v-show="form.type==='picture'">

                  <div class="form-group">
                    <div
                      v-if="previewImage"
                      class="imagePreviewWrapper"
                      :style="{ 'background-image': `url(${previewImage})` }"
                      @click="selectImage">
                    </div>

                    <b-form-file
                      id="picture"
                      ref="uploadfile2"
                      :file-name-formatter="formatNames"
                      v-model="picture"
                      :class="{ 'is-invalid': form.errors.has('picture') }"
                      :placeholder="$t('file_selected')"
                      :browse-text="$t('browse_file')"
                      :drop-placeholder="$t('drop_file')"
                      @input="pickFile">
                    ></b-form-file>
                    <p class="small">{{$t('format_picture')}}</p>
                    <has-error :form="form" field="picture"></has-error>
                  </div>


                </div>

                <div v-show="form.type==='models'">

                  <div class="form-group">
                    <label for="models">{{$t('models')}}</label>
                    <b-form-file
                      id="models"
                      ref="uploadfile3"
                      :file-name-formatter="formatNames"
                      v-model="models"
                      :class="{ 'is-invalid': form.errors.has('models') }"
                      :placeholder="$t('file_selected')"
                      :browse-text="$t('browse_file')"
                      :drop-placeholder="$t('drop_file')"
                    ></b-form-file>
                    <p class="small">{{$t('format_model')}}</p>
                    <has-error :form="form" field="models"></has-error>
                  </div>


                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{$t('back')}} </button>
                <v-button :loading="busy"  v-show="editmode" type="submit" class="btn btn-success">{{$t('update')}}</v-button>
                <v-button :loading="busy"  v-show="!editmode" type="submit" class="btn btn-primary">{{$t('save')}}</v-button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!--Autocomplete groups-->
      <div class="modal fade" id="create_group" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" v-show="!editmode" id="addNewMarkers">{{$t('save_markers_in_group')}}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form @submit.prevent="saveMarkersInGroup()">
              <div class="modal-body">

                <div class="form-group">
                  <vue-bootstrap-typeahead
                    :data="groups"
                    v-model="groupsSearch"
                    size="lg"
                    ref="typehead"
                    :serializer="s => s.name"
                    :placeholder="$t('input_name_marker')"
                    @hit="selectedGroups = $event"
                  ></vue-bootstrap-typeahead>
                  <has-error :form="formGroupSave" field="marker_id"></has-error>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Natrag</button>
                <button type="submit" class="btn btn-primary">Spremi</button>
              </div>

            </form>

          </div>
        </div>
      </div>
    </div>
  </section>

</template>


<script>


  import {ModelGltf}  from 'vue-3d-model';
  import VButton from "../../components/Button";
  import VSwatches from 'vue-swatches'
  import { VueEditor } from "vue2-editor";
  import VueBootstrapTypeahead from 'vue-bootstrap-typeahead';

  // Import the styles too, globally
  import "vue-swatches/dist/vue-swatches.css"

  export default {

    middleware:'role:Admin,Teacher,SUDO,Super admin',

    components: {
      VButton,
      VSwatches,
      VueEditor,
      VueBootstrapTypeahead,
      ModelGltf

    },
    // middleware: 'role:Admin,Super admin',

    data() {
      return {
        busy:false,
        text_marker:null,
        type:null,
        src_path_in_modal:null,
        previewImage: null,
        options:[
          {text:this.$t('text'),value:'text'},
          {text:this.$t('video'),value:'video'},
          {text:this.$t('picture'),value:'picture'},
          {text:this.$t('models'),value:'models'},
        ],
        groups:[],
        groupsSearch: '',
        selectedGroups: null,
        numberPerPage:50,
        filter:null,
        sortBy: 'name',
        sortDesc: false,
        editmode: false,
        markers : {},
        search:"",
        fieldsMarkers: [
          {
            key: 'select',
            stickyColumn: true,
          },
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
            label: this.$t('object_type') ,
            key: 'type',
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
            label: this.$t('groups'),
            key: 'group_count',
          },
          {
            label: this.$t('marker_picture'),
            key: 'marker',
          },
          {
            label: this.$t('action'),
            key:'action',

          },
        ],
        attachments: [],
        picture:[],
        models:[],
        new_name_marker:"",
        formFile: new FormData,
        formGroupSave: new Form({
          markers_id:"",
          group_id:0,
          newGroup:"",//ako grupa ne postoji napravi novu grupu
        }),
        form: new Form({
          id:'',
          name : '',
          description:'',
          updated_at: '',
          //selected:'text',
          text:'',
          color:'#000000',
          type:'text',
          url_video:'',
          selected: [],
          selectAll: false,
        })
      }
    },


    methods: {

      async cloneMarker(marker){

        await axios.get('/api/check-name?name='+marker.name)
          .then(response => {
            this.new_name_marker = response.data;
          });

        this.editmode = false;
        this.deleteClassModal();
        this.form.fill(marker);
        this.form.name=this.new_name_marker;
        this.form.type=null;
        this.form.selected=[];
        this.form.selectAll=false;

        $('#addNew').modal('show');
      },
      stopVideo(){
        let video = document.getElementById("video_preview");
        video.pause();
        video.currentTime = 0;
      },

      selectImage () {
        this.$refs.uploadfile2.click()
      },
      pickFile () {
        let input = this.$refs.uploadfile2
        let file = input.files
        if (file && file[0]) {
          let reader = new FileReader
          reader.onload = e => {
            this.previewImage = e.target.result
          }
          reader.readAsDataURL(file[0])
          this.$emit('input', file[0])
        }
      },


      //od odabranih markera pokupi id-ove
      select() {
        this.form.selected = [];
        if (this.form.selectAll) {
          for (let i in this.markers.data) {
            this.form.selected.push(this.markers.data[i].id);

          }

        }
      },
      openModalGroup() {
        $('#create_group').modal('show');
      },
      saveMarkersInGroup() {
        this.$Progress.start();
        this.formGroupSave.markers_id=this.form.selected
        //ako je array prazan onda ce se dodati upisani naziv grupe u varijablu kako bi se unijela nova grupa
        if(this.groups.length === 0){
          this.formGroupSave.newGroup = this.groupsSearch;
          this.formGroupSave.group_id=0;
        }else{
          this.formGroupSave.group_id= this.groups[0].id;
        }

        this.formGroupSave.post('/api/markers-group')
          .then((data) => {
              if(data.data.success){
                swal.fire({type: 'success', title: data.data.message});
              }else {
                swal.fire(data.data.data, data.data.message, "error");
              }
              Fire.$emit('AfterCreate');
              $('#create_group').modal('hide');
              this.groupsSearch='';
              this.selectedGroups=null;
              this.groups=[];
              this.form.selected= [],
              this.$Progress.finish();
          }).catch(error => {
            if (error.response.status == 422){
              this.formGroupSave.errors.errors = error.response.data.errors;
            }
          })
      },
      async autoComplete(query){
        axios.get('/api/groups/' + '?q=' + query).then(({data}) => {
          this.groups  = data
        });

      },
      getPictureForMarker(path,type,text){
        this.type=type;
        this.text_marker=text;
        this.src_path_in_modal=path;
        $('#openPicture').modal('show');
      },
      downloadPictureMarkerFromGroup(id,group_name) {
        axios({
          method: 'get',
          url: '/api/group/' + id + '/export-marker-picture',
          responseType: 'arraybuffer',

        }).then(function (response) {

          let blob = new Blob([response.data], {type: 'application/pdf'});
          let link = document.createElement('a');
          link.href = window.URL.createObjectURL(blob);
          link.download = group_name+".pdf";
          link.click();
        })

      },
      createMarker() {
        this.busy=true;
        this.$Progress.start();
        this.formFile.append('models', this.models);
        this.formFile.append('picture', this.picture);
        this.formFile.append('video', this.attachments);
        this.formFile.append('id', this.form.id);
        this.formFile.append('name', this.form.name);
        this.formFile.append('description', this.form.description);
        this.formFile.append('updated_at', this.form.updated_at);
        this.formFile.append('text', this.form.text);
        this.formFile.append('color', this.form.color);
        this.formFile.append('type', this.form.type);

        /*const config = {headers: {'Content-Type': 'multipart/form-data'}};*/

        axios.post('/api/marker', this.formFile).then(data => {
          //console.log(data);
          if(data.data.success){
            swal.fire({
              type: 'success',
              title: data.data.message
            });
            Fire.$emit('AfterCreate');
            $('#addNew').modal('hide');
            this.$refs.uploadfile.reset();
            this.$refs.uploadfile2.reset();
            this.$refs.uploadfile3.reset();
            this.attachments=[];
            this.picture=[];
            this.models=[];
            this.previewImage=null;
            this.formFile= new FormData;
            this.form.reset();
          }
          else {
            swal.fire(
              data.data.data,
              data.data.message,
              "error");

            $('#addNew').modal('hide');
            this.formFile = new FormData;
            this.$refs.uploadfile.reset();
            this.$refs.uploadfile2.reset();
            this.$refs.uploadfile3.reset();
            this.attachments=[];
            this.previewImage=null;
            this.picture=[];
            this.models=[];

          }
          this.$Progress.finish();
          this.busy=false;

        })
          .catch(error => {
            if (error.response.status == 422){
              this.form.errors.errors = error.response.data.errors;
              this.busy=false;
            }
          });
      },

      formatNames(files) {
        if (files.length === 1) {
          return files[0].name
        } else if (files.length === 2 || files.length === 3 || files.length === 4) {
          return `${files.length} odabrane datoteke`
        } else {
          return `${files.length} odabranih datoteka`
        }
      },

      downloadMarker(path,id) {
        axios({
          url:window.location.origin+'/'+ path,
          method: 'GET',
          responseType: 'blob',
        }).then((response) => {
          var fileURL = window.URL.createObjectURL(new Blob([response.data]));
          var fURL = document.createElement('a');

          fURL.href = fileURL;
          fURL.setAttribute('download', id+'.jpg');
          document.body.appendChild(fURL);

          fURL.click();
        });
      },

      downloadContentMarker(path,type,name,id) {
        if (type==="text"){
          axios({
            method: 'get',
            url: '/api/marker/' + id + '/export-marker-text',
            responseType: 'arraybuffer',
          }).then(function (response) {
            let blob = new Blob([response.data], {type: 'application/pdf'});
            let link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = name+'.pdf' ;
            link.click();
          })

        }else{
          axios({
            url:window.location.origin+'/'+ path,
            method: 'GET',
            responseType: 'blob',
          }).then((response) => {
            var fileURL = window.URL.createObjectURL(new Blob([response.data]));
            var fURL = document.createElement('a');

            fURL.href = fileURL;
            let name_file =  path.substring(path.lastIndexOf('/')+1);
            fURL.setAttribute('download',name_file);
            document.body.appendChild(fURL);

            fURL.click();
          });
        }

      },

      searchit: _.debounce(() => {Fire.$emit('searching');},500),

      getResults(page = 1) {

        let query = this.filter;
        let sort = this.sortBy;
        let sortDesc = this.sortDesc;
        let perPage= this.numberPerPage;

        let route="/api/marker?page="+page+"&sort="+sort+"&desc="+sortDesc+"&perPage="+perPage;
        if(query){
          route+="&q="+query;
        }

        axios.get(route)
          .then(response => {
            this.markers = response.data;
          });


      },

      updateMarker(){
        this.$Progress.start();
        this.form.put('/api/marker/'+this.form.id)
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
        this.form.type=null;
        this.form.selected=[];
        this.form.selectAll=false;
      },

      newModal(){
        this.deleteClassModal();
        this.editmode = false;
        this.form.reset();
        $('#addNew').modal('show');
      },

      deleteMarker(marker){
        swal.fire({
          title:  this.$t('delete_modal_title')+ ' "'+marker.name+'" ?',
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
            this.form.delete('/api/marker/'+marker.id).then((data)=>{
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

      createMarker1(){
        this.$Progress.start();

        this.form.post('/api/marker')
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

    },

    watch: {
      groupsSearch: _.debounce(function(addr) { this.autoComplete(addr) }, 500)
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
<style scoped lang="scss">
.imagePreviewWrapper {
  width: 250px;
  height: 250px;
  display: block;
  cursor: pointer;
  margin: 0 auto 30px;
  background-size: cover;
  background-position: center center;
}
</style>
