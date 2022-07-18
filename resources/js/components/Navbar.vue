<template>
  <!--<nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
      <router-link :to="{ name: user ? 'home' : 'welcome' }" class="navbar-brand">
        {{ appName }}
      </router-link>

      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false">
        <span class="navbar-toggler-icon" />
      </button>

      <div id="navbarToggler" class="collapse navbar-collapse">
        <ul class="navbar-nav">
          <locale-dropdown />
          &lt;!&ndash; <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li> &ndash;&gt;
        </ul>

        <ul class="navbar-nav ml-auto">
          &lt;!&ndash; Authenticated &ndash;&gt;
          <li v-if="user" class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-dark"
               href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            >
              <img :src="user.photo_url" class="rounded-circle profile-photo mr-1">
              {{ user.name }}
            </a>
            <div class="dropdown-menu">
              <router-link :to="{ name: 'settings.profile' }" class="dropdown-item pl-3">
                <fa icon="cog" fixed-width />
                {{ $t('settings') }}
              </router-link>

              <div class="dropdown-divider" />
              <a href="#" class="dropdown-item pl-3" @click.prevent="logout">
                <fa icon="sign-out-alt" fixed-width />
                {{ $t('logout') }}
              </a>
            </div>
          </li>
          &lt;!&ndash; Guest &ndash;&gt;
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
  </nav>-->
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"> <i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <div id="navbar" class="collapse navbar-collapse">
      <ul class="navbar-nav">
        <locale-dropdown />
        <!-- <li class="nav-item">
          <a class="nav-link" href="#">Link</a>
        </li> -->
      </ul>
      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">

      <li  v-if="user" class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-dark"
           href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
        >
          <!--<img :src="'../storage/img/profile/'+user.photo_url" class="rounded-circle profile-photo mr-1">-->
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
    </ul>
    </div>
  </nav>
  <!-- /.navbar -->
</template>

<script>
import { mapGetters } from 'vuex'
import LocaleDropdown from './LocaleDropdown'

export default {
  components: {
    LocaleDropdown
  },

/*  data: () => ({
    appName: window.config.appName
  }),*/

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

<style scoped>
.profile-photo {
  width: 2rem;
  height: 2rem;
  margin: -.375rem 0;
}
</style>
