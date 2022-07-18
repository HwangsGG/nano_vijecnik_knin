<template>
  <section>
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
      <div class="container">
        <router-link :to="{ name: user ? 'home' : 'welcome' }" class="navbar-brand">
          <img src="/img/dream.png" class="brand-image" style="width: 70px;">
        </router-link>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false">
          <span class="navbar-toggler-icon" />
        </button>

        <div id="navbar" class="collapse navbar-collapse">
          <ul class="navbar-nav">
            <locale-dropdown />
            <!-- <li class="nav-item">
              <a class="nav-link" href="#">Link</a>
            </li> -->
          </ul>

          <ul class="navbar-nav ml-auto">
              <!-- <li class="nav-item">
                <a class="nav-link" href="#">Link</a>
              </li> -->
              <li v-if="user" class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark"
                   href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                >
                  <img :src="'/img/profile/'+user.photo_url" class="rounded-circle profile-photo mr-1">
                  {{ user.name }}
                </a>
                <div class="dropdown-menu">
                  <router-link :to="{ name: 'settings.profile' }" class="dropdown-item pl-3">
                    <i class="fas fa-cog"></i>
                    {{ $t('settings') }}
                  </router-link>

                  <div class="dropdown-divider" />
                  <a href="#" class="dropdown-item pl-3" @click.prevent="logout">
                    <i class="fas fa-sign-out-alt"></i>
                    {{ $t('logout') }}
                  </a>
                </div>
              </li>

              <template v-else>
              <li class="nav-item">
                <router-link :to="{ name: 'login' }" class="nav-link" active-class="active">
                  {{ $t('login') }}
                </router-link>
              </li>
              <li class="nav-item">
                <router-link :to="{ name: 'register' }" class="nav-link" active-class="active">
                  {{ $t('register') }}
                </router-link>
              </li>
              </template>
          </ul>
        </div>
      </div>
    </nav>

    <child />

  </section>
</template>

<script>
  import { mapGetters } from 'vuex'
  import LocaleDropdown from "../components/LocaleDropdown";
export default {

  name: 'BasicLayout',

  components: {LocaleDropdown},

  data: () => ({
    appName: window.config.appName
  }),

  computed: mapGetters({
    user: 'auth/user'
  }),

  methods: {
    async logout () {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Redirect to login.
      this.$router.push({ name: 'login' })
    }
  }

}
</script>

<style lang="scss">
.basic-layout {
  color: #636b6f;
  height: 100vh;
  font-weight: 100;
  position: relative;

  .links > a {
    color: #636b6f;
    padding: 0 25px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: .1rem;
    text-decoration: none;
    text-transform: uppercase;
  }
}
.profile-photo {
  width: 2rem;
  height: 2rem;
  margin: -.375rem 0;
}
</style>
