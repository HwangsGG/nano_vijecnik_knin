<template>
  <card :title="$t('your_password')">
    <form @submit.prevent="update" @keydown="form.onKeydown($event)">
      <alert-success :form="form" :message="$t('password_updated')" />


      <!--Current password-->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right" for="current_password">{{ $t('current_password') }}</label>
        <div class="col-md-7">

          <input v-model="form.current_password"
            :class="{ 'is-invalid': form.errors.has('current_password') }"
            class="form-control"
            id="current_password"
            type="password"
            name="current_password">

          <has-error :form="form" field="current_password" />
        </div>
      </div>

      <!-- Password -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right" for="password">{{ $t('new_password') }}</label>
        <div class="col-md-7">
          <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" class="form-control" id="password" type="password" name="password">
          <has-error :form="form" field="password" />
        </div>
      </div>

      <!-- Password Confirmation -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right" for="password_confirmation">{{ $t('confirm_password') }}</label>
        <div class="col-md-7">
          <input v-model="form.password_confirmation" :class="{ 'is-invalid': form.errors.has('password_confirmation') }" id="password_confirmation" class="form-control" type="password" name="password_confirmation">
          <has-error :form="form" field="password_confirmation" />
        </div>
      </div>

      <!-- Submit Button -->
      <div class="form-group row">
        <div class="col-md-9 ml-md-auto">
          <v-button :loading="form.busy" type="success">
            {{ $t('update') }}
          </v-button>
        </div>
      </div>
    </form>
  </card>
</template>

<script>
import Form from 'vform'

export default {
  scrollToTop: false,

  metaInfo () {
    return { title: this.$t('settings') }
  },

  data: () => ({
    form: new Form({
      current_password:'',
      password: '',
      password_confirmation: ''
    })
  }),

  methods: {
    async update () {
      await this.form.patch('/api/settings/password')

      this.form.reset()
    }
  }
}
</script>
