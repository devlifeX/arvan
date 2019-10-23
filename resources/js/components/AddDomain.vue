<template>
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <form>
          <div class="form-group">
            <label for="exampleFormControlInput1">Domain URL</label>
            <small id="passwordHelpInline" class="text-muted">Please enter you domain to continue</small>
            <input type="url" class="form-control" id="domain" placeholder="yourdomain.com" />
          </div>
          <div class="form-group">
            <button type="button" class="btn btn-primary">Add domain</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  mounted() {
    console.log("Component mounted.");
  }
};
</script>
