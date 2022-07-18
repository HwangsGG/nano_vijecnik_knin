<template>
  <main class="main" id="top">

    <div class="container" data-layout="container">
      <div class="row flex-center  py-6">
        <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4"><a class="d-flex flex-center mb-4" href="#">
          <img class="mr-2" src="/img/dream.png" alt="" width="100" />
          <span class="text-sans-serif font-weight-extra-bold fs-5 d-inline-block">{{$t('name_project')}}</span></a>
          <div class="card">
            <div class="card-body p-4 p-sm-5">
              <div class="row text-left justify-content-between align-items-center mb-2">
                <div class="col-auto">
                  <h5>{{ $t('login') }}</h5>
                </div>
                <div class="col-auto">
                  <p class="fs--1 text-600 mb-0">{{ $t('or') }}
                    <router-link :to="{ name: 'register' }" >
                      {{ $t('create_an_account') }}
                    </router-link>
                  </p>
                </div>
              </div>
              <form @submit.prevent="login" @keydown="form.onKeydown($event)">
                <div class="form-group">
                  <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" class="form-control" type="email" name="email" :placeholder="$t('email_placeholder')">
                  <has-error :form="form" field="email" />
                </div>
                <div class="form-group">
                  <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" class="form-control" type="password" name="password" :placeholder="$t('password_placeholder')">
                  <has-error :form="form" field="password" />
                </div>
                <div class="row justify-content-between align-items-center">
                  <div class="col-auto">
                    <div class="custom-control custom-checkbox">
                      <input v-model="remember" class="custom-control-input" type="checkbox" id="basic-checkbox" />
                      <label class="custom-control-label" for="basic-checkbox">{{ $t('remember_me') }}</label>
                    </div>
                  </div>
                  <div class="col-auto">
                    <router-link :to="{ name: 'password.request' }" class="fs--1">
                      {{ $t('forgot_password') }}
                    </router-link>
                  </div>
                </div>
                <div class="form-group">
                  <v-button class="btn btn-primary btn-block mt-3"  :loading="form.busy">
                    {{ $t('login') }}
                  </v-button>
                </div>
<!--                <div class="form-group mb-0">
                  <div class="row no-gutters">
                    <div class="col-sm-6 pr-sm-1"><a class="btn btn-outline-google-plus btn-sm btn-block mt-2" href="#"><span class="fab fa-google-plus-g mr-2" data-fa-transform="grow-8"></span> google</a></div>
                    <div class="col-sm-6 pl-sm-1"><a class="btn btn-outline-facebook btn-sm btn-block mt-2" href="#"><span class="fab fa-facebook-square mr-2" data-fa-transform="grow-8"></span> facebook</a></div>
                  </div>
                </div>-->
              </form>
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
      <div class="card">
        <div class="card-header">{{ $t('login') }}</div>

        <div class="card-body">
        <form @submit.prevent="login" @keydown="form.onKeydown($event)">
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

          &lt;!&ndash; Remember Me &ndash;&gt;
          <div class="form-group row">
            <div class="col-md-3" />
            <div class="col-md-7 d-flex">
              <checkbox v-model="remember" name="remember">
                {{ $t('remember_me') }}
              </checkbox>

              <router-link :to="{ name: 'password.request' }" class="small ml-auto my-auto">
                {{ $t('forgot_password') }}
              </router-link>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-md-7 offset-md-3 d-flex">
              &lt;!&ndash; Submit Button &ndash;&gt;
              <v-button :loading="form.busy">
                {{ $t('login') }}
              </v-button>

            </div>
          </div>
        </form>
        </div>
      </div>
    </div>
  </div>
    </div>
  </div>-->
</template>

<script>
import Form from 'vform'
import Cookies from 'js-cookie'
import LoginWithGithub from '~/components/LoginWithGithub'

export default {
  layout: 'basic',
  components: {
    LoginWithGithub
  },

  middleware: 'guest',

  metaInfo () {
    return { title: this.$t('login') }
  },

  data: () => ({
    form: new Form({
      email: '',
      password: ''
    }),
    remember: true
  }),

  methods: {
    async login () {
      // Submit the form.
      const { data } = await this.form.post('/api/login')

      // Save the token.
      this.$store.dispatch('auth/saveToken', {
        token: data.token,
        remember: this.remember
      })

      // Fetch the user.
      await this.$store.dispatch('auth/fetchUser')

      // Redirect home.
      this.redirect()
    },

    redirect () {
      const intendedUrl = Cookies.get('intended_url')

      if (intendedUrl) {
        Cookies.remove('intended_url')
        this.$router.push({ path: intendedUrl })
      } else {
        this.$router.push({ name: 'home' })
      }
    }
  }
}
</script>
