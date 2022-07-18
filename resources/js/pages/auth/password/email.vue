<template>
  <main class="main" id="top">
    <div class="container" data-layout="container">
      <div class="row flex-center py-6 text-center">
        <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4">
          <a class="d-flex flex-center mb-4" href="#">
            <img class="mr-2" src="/img/dream.png" alt="" width="100" />
            <span class="text-sans-serif font-weight-extra-bold fs-5 d-inline-block">{{$t('name_project')}}</span></a>
          <div class="card">
            <div class="card-body p-4 p-sm-5">
              <h5 class="mb-0">{{$t('forgot_password')}}</h5><small>{{$t('forgot_password_message')}}</small>
              <form @submit.prevent="send" @keydown="form.onKeydown($event)" class="mt-4">
                <alert-success :form="form" :message="status" />
                <div class="form-group">
                  <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" class="form-control" type="email" name="email" :placeholder="$t('email')">
                  <has-error :form="form" field="email" />
                </div>
                <div class="form-group">
                  <v-button class="btn btn-primary btn-block mt-3" :loading="form.busy">
                    {{ $t('send_password_reset_link') }}
                  </v-button>
                </div>
              </form>
              <!--<a class="fs&#45;&#45;1 text-600" href="#!">I can't recover my account using this page<span class="d-inline-block ml-1">&rarr;</span></a>-->
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

<!--  <div class="content">
    <div class="container">
      <div class="row justify-content-center py-4">
        <div class="col-md-8">
      <card :title="$t('reset_password')">
        <form @submit.prevent="send" @keydown="form.onKeydown($event)">
          <alert-success :form="form" :message="status" />

          &lt;!&ndash; Email &ndash;&gt;
          <div class="form-group row">
            <label class="col-md-3 col-form-label text-md-right">{{ $t('email') }}</label>
            <div class="col-md-7">
              <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" class="form-control" type="email" name="email">
              <has-error :form="form" field="email" />
            </div>
          </div>

          &lt;!&ndash; Submit Button &ndash;&gt;
          <div class="form-group row">
            <div class="col-md-9 ml-md-auto">
              <v-button :loading="form.busy">
                {{ $t('send_password_reset_link') }}
              </v-button>
            </div>
          </div>
        </form>
      </card>
    </div>
  </div>
    </div>
  </div>-->

</template>

<script>
import Form from 'vform'

export default {
  layout: 'basic',
  middleware: 'guest',

  metaInfo () {
    return { title: this.$t('reset_password') }
  },

  data: () => ({
    status: '',
    form: new Form({
      email: ''
    })
  }),

  methods: {
    async send () {
      const { data } = await this.form.post('/api/password/email')

      this.status = data.status

      this.form.reset()
    }
  }
}
</script>
