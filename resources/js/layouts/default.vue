<template>
<!--  <div class="main-layout">
    <navbar />

    <div class="container mt-4">
      <child />
    </div>
  </div>-->
  <div class="wrapper">
    <my-navbar />
    <my-sidebar />
    <my-content />
    <my-footer />
  </div>

<!--  <blank />-->
</template>

<script>
import Blank from '~/components/Blank'
import MyNavbar from '~/components/Navbar'
import MySidebar from '~/components/Sidebar'
import MyContent from '~/components/Content'
import MyFooter from '~/components/Footer'

export default {
  name: 'MainLayout',

  components: {
    Blank,
    MyNavbar,
    MySidebar,
    MyContent,
    MyFooter
  },

}
</script>
