<template>
  <main class="main" id="top">
    <div class="container" data-layout="container">
      <div class="row flex-center py-6">
        <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4"><a class="d-flex flex-center mb-4" href="#">
          <img class="mr-2" src="/img/dream.png" alt="" width="100" />
          <span class="text-sans-serif font-weight-extra-bold fs-5 d-inline-block">{{$t('name_project')}}</span></a>
          <div class="card">
            <div class="card-body p-4 p-sm-5">
              <div class="row text-left justify-content-between align-items-center mb-2">
                <div class="col-auto">
                  <h5> {{$t('register')}}</h5>
                </div>
                <div class="col-auto">
                  <p class="fs--1 text-600 mb-0">{{$t('have_an_account')}}
                    <router-link :to="{ name: 'login' }" >
                      {{ $t('login') }}
                    </router-link>
                  </p>
                </div>
              </div>
              <div v-if="mustVerifyEmail" class="alert alert-success alert-dismissible fade show" role="alert">
                {{ $t('verify_email_address') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>

              <form @submit.prevent="register" @keydown="form.onKeydown($event)">
                <div class="form-group">
                  <input v-model="form.name" :class="{ 'is-invalid': form.errors.has('name') }" class="form-control" type="text" name="name" :placeholder="$t('name')">
                  <has-error :form="form" field="name" />
                </div>
                <div class="form-group">
                  <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" class="form-control" type="email" name="email" :placeholder="$t('email')">
                  <has-error :form="form" field="email" />
                </div>
                <div class="form-row">
                  <div class="form-group col-6">
                    <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" class="form-control" type="password" name="password" :placeholder="$t('password_placeholder')">
                    <has-error :form="form" field="password" />
                  </div>
                  <div class="form-group col-6">
                    <input v-model="form.password_confirmation" :class="{ 'is-invalid': form.errors.has('password_confirmation') }" class="form-control" type="password" name="password_confirmation" :placeholder="$t('confirm_password')">
                    <has-error :form="form" field="password_confirmation" />
                  </div>
                </div>
                <div class="form-group">
                  <input v-model="form.accept" class="custom-control-input" type="checkbox" id="basic-register-checkbox"  :class="{ 'is-invalid': form.errors.has('accept') }" />
                  <label class="custom-control-label" for="basic-register-checkbox">I accept the <a href="#!">terms </a>and <a href="#!">privacy policy</a></label>
                  <has-error :form="form" field="accept" />
                </div>
                <div class="form-group">
                  <v-button class="btn btn-primary btn-block mt-3" :loading="form.busy">{{ $t('register') }}</v-button>

                </div>
              </form>
             <!-- <div class="w-100 position-relative mt-4">
                <hr class="text-300" />
                <div class="position-absolute absolute-centered t-0 px-3 bg-white text-sans-serif fs&#45;&#45;1 text-500 text-nowrap">or register with</div>
              </div>
              <div class="form-group mb-0">
                <div class="row no-gutters">
                  <div class="col-sm-6 pr-sm-1"><a class="btn btn-outline-google-plus btn-sm btn-block mt-2" href="#"><span class="fab fa-google-plus-g mr-2" data-fa-transform="grow-8"></span> google</a></div>
                  <div class="col-sm-6 pl-sm-1"><a class="btn btn-outline-facebook btn-sm btn-block mt-2" href="#"><span class="fab fa-facebook-square mr-2" data-fa-transform="grow-8"></span> facebook</a></div>
                </div>
              </div>-->
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
 <!-- <div class="content">
    <div class="container">
      <div class="row justify-content-center py-4">
        <div class="col-md-8">
              <card v-if="mustVerifyEmail" :title="$t('register')">
                <div class="alert alert-success" role="alert">
                  {{ $t('verify_email_address') }}
                </div>
              </card>
              <card v-else :title="$t('register')">
                 <form @submit.prevent="register" @keydown="form.onKeydown($event)">
                    &lt;!&ndash; Name &ndash;&gt;
                    <div class="form-group row">
                      <label class="col-md-3 col-form-label text-md-right">{{ $t('name') }}</label>
                      <div class="col-md-7">
                        <input v-model="form.name" :class="{ 'is-invalid': form.errors.has('name') }" class="form-control" type="text" name="name">
                        <has-error :form="form" field="name" />
                      </div>
                    </div>

                    &lt;!&ndash; Email &ndash;&gt;
                    <div class="form-group row">
                      <label class="col-md-3 col-form-label text-md-right">{{ $t('email') }}</label>
                      <div class="col-md-7">
                        <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" class="form-control" type="email" name="email">
                        <has-error :form="form" field="email" />
                      </div>
                    </div>

                    &lt;!&ndash; Password &ndash;&gt;
                    <div class="form-group row">
                      <label class="col-md-3 col-form-label text-md-right">{{ $t('password') }}</label>
                      <div class="col-md-7">
                        <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" class="form-control" type="password" name="password">
                        <has-error :form="form" field="password" />
                      </div>
                    </div>

                    &lt;!&ndash; Password Confirmation &ndash;&gt;
                    <div class="form-group row">
                      <label class="col-md-3 col-form-label text-md-right">{{ $t('confirm_password') }}</label>
                      <div class="col-md-7">
                        <input v-model="form.password_confirmation" :class="{ 'is-invalid': form.errors.has('password_confirmation') }" class="form-control" type="password" name="password_confirmation">
                        <has-error :form="form" field="password_confirmation" />
                      </div>
                    </div>

                    <div class="form-group row">
            <div class="col-md-7 offset-md-3 d-flex">
              &lt;!&ndash; Submit Button &ndash;&gt;
              <v-button :loading="form.busy">
                {{ $t('register') }}
              </v-button>

              &lt;!&ndash; GitHub Register Button &ndash;&gt;
              <login-with-github />
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
import LoginWithGithub from '~/components/LoginWithGithub'

export default {
  layout: 'basic',
  components: {
    LoginWithGithub
  },

  middleware: 'guest',

  metaInfo () {
    return { title: this.$t('register') }
  },

  data: () => ({
    form: new Form({
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
      accept: false,
    }),
    mustVerifyEmail: false
  }),

  methods: {
    async register () {
      // Register the user.
      const { data } = await this.form.post('/api/register')

      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true
      } else {
        // Log in the user.
        const { data: { token } } = await this.form.post('/api/login')

        // Save the token.
        this.$store.dispatch('auth/saveToken', { token })

        // Update the user.
        await this.$store.dispatch('auth/updateUser', { user: data })

        // Redirect home.
        this.$router.push({ name: 'home' })
      }
    }
  }
}
</script>
