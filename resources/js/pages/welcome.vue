<template>
  <div class="basic-layout d-flex align-items-center justify-content-center m-0 bg-white">
    <div class="text-center">


      <div class="row-cols-6">
        <img src="/img/dream.png" class="brand-image  elevation-3" style="opacity: .8">
      </div>

      <div class="title mb-4">
        {{ title }}
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {

  layout: 'basic',

  metaInfo () {
    return { title: this.$t('home') }
  },

  data: () => ({
    title: window.config.appName
  }),

  computed: mapGetters({
    authenticated: 'auth/check'
  })
}
</script>

<style scoped>
.top-right {
  position: absolute;
  right: 10px;
  top: 18px;
}

.title {
  font-size: 85px;
}
</style>
