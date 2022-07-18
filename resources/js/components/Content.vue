<template>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
<!--          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Starter Page</h1>
          </div>&lt;!&ndash; /.col &ndash;&gt;
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Starter Page</li>
            </ol>
          </div>&lt;!&ndash; /.col &ndash;&gt;-->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <child/>
      </div>
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</template>

<script>
    export default {
        name: "Content"
    }
</script>

<style scoped>

</style>
