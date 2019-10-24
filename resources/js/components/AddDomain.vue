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
            <VueLoadingButton :loading="loading" @click.native="clickHandler">Add domain</VueLoadingButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import VueLoadingButton from "./vue-loading-button";

import { send } from "../api.js";

export default {
  methods: {
    clickHandler: () => {
      send();
    }
  },
  data() {
    return {
      loading: false
    };
  },
  components: {
    VueLoadingButton
  }
};
</script>
