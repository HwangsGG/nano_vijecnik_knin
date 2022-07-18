<template>
  <div class="basic-layout d-flex align-items-center justify-content-center m-0 bg-white">
    <div class="text-center">
      <div class="title mb-4">
        {{ $t('page_not_found') }}
      </div>

      <div class="links">
        <router-link :to="{ name: 'welcome' }">
          {{ $t('go_home') }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<!--
<template>
  <card class="text-center">
    <h3 class="mb-4">
      {{ $t('page_not_found') }}
    </h3>

    <div class="links">
      <router-link :to="{ name: 'welcome' }">
        {{ $t('go_home') }}
      </router-link>
    </div>
  </card>
</template>-->

<script>
export default {

  layout: 'basic',

  metaInfo () {
    return { title: this.$t('404') }
  },

  name: 'NotFound'
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
