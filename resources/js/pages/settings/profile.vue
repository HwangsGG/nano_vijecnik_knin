<template>
  <card :title="$t('your_info')">
    <form @submit.prevent="update" @keydown="form.onKeydown($event)" enctype="multipart/form-data">
      <!--      <div v-if="success" role="alert" class="alert alert-success alert-dismissible">
        <button type="button" aria-label="Close" class="close">
          <span aria-hidden="true">×</span>
        </button>
        <div>{{$t('info_updated')}}</div>
      </div>-->
      <alert-success :form="form" :message="$t('info_updated')" />

      <!-- Name -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('name') }}</label>
        <div class="col-md-7">
          <input v-model="form.name" :class="{ 'is-invalid': allerrors.name }" class="form-control" type="text" name="name">
          <div v-if="allerrors.name" :class="['help-block invalid-feedback']">{{ allerrors.name[0] }}</div>
        </div>
      </div>

      <!-- Email -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('email') }}</label>
        <div class="col-md-7">
          <input v-model="form.email"  :class="{ 'is-invalid': allerrors.email }" class="form-control" type="email" name="email">
          <div v-if="allerrors.email" :class="['help-block invalid-feedback']">{{ allerrors.email[0] }}</div>
        </div>
      </div>

      <!--Photo profile-->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('photo_profile') }}</label>
        <div class="col-md-7">
          <b-form-file
            ref="uploadfile"
            v-model="form.photo"
            :placeholder="$t('photo_placeholder')"
            :browse-text="$t('photo_text')"
            :drop-placeholder="$t('photo_drop_placeholder')"
            class="form-control"
            :class="{ 'is-invalid': allerrors.photo }">
            </b-form-file>
          <div v-if="allerrors.photo" :class="['help-block invalid-feedback']">{{ allerrors.photo[0] }}</div>
        </div>
      </div>

      <!-- Submit Button -->
      <div class="form-group row">
        <div class="col-md-9 ml-md-auto">
          <!--:loading="form.busy"-->
          <v-button   :loading="loading" type="success">
            {{ $t('update') }}
          </v-button>
        </div>
      </div>


    </form>
  </card>
</template>

<script>
import Form from 'vform'
import { mapGetters } from 'vuex'

export default {
  //scrollToTop: false,

  metaInfo () {
    return { title: this.$t('settings') }
  },

  data: () => ({
    loading:false,
    datauser:{},
    success:false,
    allerrors: [],
    formFile: new FormData,


    form: new Form({
      successful: false,
      photo:[],
      name: '',
      email: '',
      photo_url: '',
      updated_at:'',
    })
  }),

  computed: mapGetters({
    user: 'auth/user'
  }),

  created () {
    // Fill the form with user data.
    this.form.keys().forEach(key => {
      this.form[key] = this.user[key]
    })
    this.form.photo=[];
  },

  methods: {
    async update () {
      this.loading=true;

      const config = {headers: {'content-type': 'multipart/form-data; application/x-www-form-urlencoded; charset=UTF-8'}}

      this.formFile.append('name', this.form.name);
      this.formFile.append('email', this.form.email);
      this.formFile.append('updated_at', this.form.updated_at);
      if(!Array.isArray(this.form.photo)){
        this.formFile.append('photo', this.form.photo);
      }
      this.formFile.append('photo_url', this.form.photo_url);
      this.formFile.append('user', this.user);
      this.formFile.append('_method', 'PATCH');

      await axios.post('/api/settings/profile/',this.formFile,config).then( response => {
        this.datauser=response;
        this.allerrors = [];
        this.success = true;
        this.form.successful=true;
      } ).catch((error) => {
        this.allerrors = error.response.data.errors;
        this.success = false;
      });


      //set new data for user GLOBAL
      this.$store.dispatch('auth/updateUser', { user: this.datauser.data });

      //remove photo in field
      this.$refs.uploadfile.reset();

      this.formFile= new FormData;
      this.loading=false;
    }
  }
}
</script>
