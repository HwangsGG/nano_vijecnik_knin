<template>
  <div>
  <b-tabs content-class="mt-3">

    <b-tab :title="$t('information')" active>
      <div class="row mt-5">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <div class="card-title">{{$t('title_show_group')}}</div>
              <div class="card-tools">
              </div>
            </div>

            <b-table responsive hover :items="[group]" :fields="fieldsGroup">
              <template v-slot:cell(action)="data">
                <a href="#" v-on:click="editModal(data.item)"><i class="fa fa-edit blue"></i></a>
              </template>

            </b-table>
            <!-- /.card-header -->
            <!-- /.card -->
          </div>
        </div>
      </div>
    </b-tab>
    <b-tab :title="$t('markers_title_in_group')">
      <div class="row mt-5">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <div class="card-title" v-if="group.markers && group.markers.length>0">{{$t('markers_title_card_in_group')}}</div>
              <div class="card-title" v-else>{{$t('markers_title_in_group_empty')}}</div>
              <div class="card-tools">

                <button class="btn btn-outline-primary" @click="openModalConnectMarker"><i class="fas fa-connectdevelop"></i></button>
<!--                <button class="btn btn-outline-primary" @click="openModalAutocomplete"><i class="fas fa-connectdevelop"></i></button>-->
              </div>
            </div>

            <b-table  responsive hover :items="group.markers" :fields="fieldsMarkers">

              <template v-slot:cell(name)="data">
                <router-link :to="{ name: 'showMarker', params: {id: data.item.id } }">
                  {{ data.value}}
                </router-link>
              </template>

              <template v-slot:cell(marker)="data">
                <a  href="#" @click="downloadMarker(data.item.image_marker,data.item.id)">
                  <i class="fa fa-download blue"></i>
                </a>
              </template>

              <template v-slot:cell(action)="data">

<!--                <a href="#" v-on:click="deleteaMarker(data.item)"><i class="fa fa-trash red"></i></a>-->
              </template>


            </b-table>
            <!-- /.card-header -->
            <!-- /.card -->
          </div>
        </div>
      </div>
    </b-tab>


  </b-tabs>

  <!--Autocomplete groups-->
  <div class="modal fade" id="connect_marker" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addNewMarkers">{{$t('save_markers_in_group')}}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form @submit.prevent="">
          <div class="modal-body">

            <div class="form-group">
              <vue-bootstrap-typeahead
                :data="markers"
                v-model="markersSearch"
                size="lg"
                ref="typehead"
                :serializer="s => s.name"
                :placeholder="$t('input_name_marker')"
                @hit="selectedMarkers = $event"
              ></vue-bootstrap-typeahead>
              <has-error :form="formConnectMarkerAndGroup" field="marker_id"></has-error>
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
</template>

<script>
import VueBootstrapTypeahead from 'vue-bootstrap-typeahead'
import moment from "moment";
export default {
  components:{
    VueBootstrapTypeahead
  },
  data() {
    return {
      editmode:false,
      markersSearch:'',
      selectedMarkers: null,
      group: {},
      markers:[],
      fieldsGroup: [
        {
          label: '#',
          key: 'index',
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
          label: this.$t('action'),
          key:'action',

        },
      ],
      fieldsMarkers: [
        {
          key: 'select',
          stickyColumn: true,
        },
        {
          label: '#',
          key: 'index',
        },
        {
          label: this.$t('name') ,
          key: 'name',
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
          label: this.$t('marker_picture'),
          key: 'marker',
        },
        {
          label: this.$t('action'),
          key:'action',

        },
      ],

      form: new Form({
        id:'',
        name : '',
        description:'',
        updated_at: '',
      }),
      formMarker: new Form({
        id:'',
        name : '',
        updated_at: '',
        //selected:'text',
        text:'',
        color:'#000000',
        type:'text',
        url_video:'',
        selected: [],
        selectAll: false,
      }),
      formConnectMarkerAndGroup: new Form({
          marker_id:null,
          group_id : null,

      }

      )

    }
  },
  methods:{

    async autoComplete(query){
      axios.get('/api/markers/' + '?q=' + query).then(({data}) => {
        this.markers  = data
      });

    },
    openModalConnectMarker() {
      $('#connect_marker').modal('show');
    },

    getResults() {
      let route="/api/group/"+this.$route.params.id;
      axios.get(route).then(response => {
          this.group = response.data;
        });
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

  },

  watch: {
    markersSearch: _.debounce(function(addr) { this.autoComplete(addr) }, 500)
  },

  created() {
    this.getResults();

    Fire.$on('AfterCreate',() => {
      this.getResults();
    });

  },

}
</script>

<style scoped>

</style>
