<template>
  <!-- Main Sidebar Container -->
  <aside v-if="$store.state.auth.user" class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <router-link :to="{ name: 'home' }" class="brand-link">
      <img src="/img/dream.png" alt="AdminLTE Logo" class="brand-image  elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">{{ this.$root.$metaInfo.title }}</span>
    </router-link>


    <!-- Sidebar -->
    <div class="sidebar" >
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
<!--        <div class="image">
         &lt;!&ndash; <img :src="'../storage/img/profile/'+$store.state.auth.user.photo_url" class="img-circle elevation-2" alt="User Image">&ndash;&gt;
          <img :src="'/img/profile/'+$store.state.auth.user.photo_url" class="img-circle elevation-2" alt="User Image">
        </div>-->
        <div class="info">
          <router-link :to="{ name: 'settings.profile' }" class="d-block">
            {{ $store.state.auth.user.name }}
          </router-link>
        </div>
      </div>

      <!-- SidebarSearch Form -->
<!--      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>-->

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item menu-open">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                {{$t('menu')}}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <router-link :to="{ name: 'markers.all' }" class="dropdown-item pl-3">
                  <i class="fas fa-user-lock"></i>
                  {{$t('markers')}}
                </router-link>
              </li>
              <li class="nav-item">
                <router-link :to="{ name: 'groups.all' }" class="dropdown-item pl-3">
                  <i class="fas fa-user-lock"></i>
                  {{$t('groups')}}
                </router-link>
              </li>
              <li class="nav-item" v-if="$store.state.auth.user && $can($store.state.auth.user,'menu-item-admin')">
                <router-link :to="{ name: 'permissions.all' }" class="dropdown-item pl-3">
                  <i class="fas fa-user-shield"></i>
                  {{$t('permissions')}}
                </router-link>
              </li>
              <li class="nav-item" v-if="$store.state.auth.user && $can($store.state.auth.user,'menu-item-admin')">
                <router-link :to="{ name: 'roles.all' }" class="dropdown-item pl-3">
                  <i class="fas fa-user-lock"></i>
                  {{$t('roles')}}
                </router-link>
              </li>
              <li class="nav-item" v-if="$store.state.auth.user && $can($store.state.auth.user,'menu-item-admin')">
                <router-link :to="{ name: 'users.all' }" class="dropdown-item pl-3">
                  <i class="fas fa-users"></i>
                  {{$t('users')}}
                </router-link>
              </li>
            </ul>
          </li>
<!--          <li class="nav-header">MULTI LEVEL EXAMPLE</li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="fas fa-circle nav-icon"></i>
              <p>Level 1</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-circle"></i>
              <p>
                Level 1
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Level 2</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
                    Level 2
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-dot-circle nav-icon"></i>
                      <p>Level 3</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-dot-circle nav-icon"></i>
                      <p>Level 3</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-dot-circle nav-icon"></i>
                      <p>Level 3</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Level 2</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="fas fa-circle nav-icon"></i>
              <p>Level 1</p>
            </a>
          </li>-->
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
  <!-- /Main Sidebar Container -->
</template>

<script>
    export default {

      mounted(){
        $('[data-widget="treeview"]').Treeview('init');
      }

    }
</script>



<style scoped>

</style>
