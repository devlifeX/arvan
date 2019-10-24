<template>
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <form>
          <div class="form-group">
            <label for="exampleFormControlInput1">Domain URL</label>
            <small id="passwordHelpInline" class="text-muted">Please enter you domain to continue</small>
            <input
              v-model="domain"
              type="url"
              class="form-control"
              id="domain"
              placeholder="yourdomain.com"
            />
          </div>
          <div class="form-group">
            <VueLoadingButton :loading="loading" @click.native="addDomainClicked">Add domain</VueLoadingButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import serverBus from "../ServerBus";
import { fetchData } from "../api.js";
import VueLoadingButton from "./vue-loading-button";

export default {
  methods: {
    validateUrl: url => {
      try {
        new URL(url);
        return true;
      } catch (_) {
        return false;
      }
    },
    addDomainClicked: () => {
      serverBus.$emit("add-domain-clicked");
    },
    addDomainClickedHandler: e => {
      if (!e.validateUrl(e.domain)) {
        alert("Please enter a valid URL!");
        return;
      }
      e.loading = true;
      fetchData(`${e.baseUrl}/domain/create`, { domain: e.domain }).then(
        data => {
          console.log(data);
          e.loading = false;
        }
      );
    }
  },
  data() {
    return {
      domain: "",
      loading: false,
      baseUrl: ""
    };
  },
  created() {
    this.baseUrl = `${window.location.origin}`;
    serverBus.$on("add-domain-clicked", () => {
      this.addDomainClickedHandler(this);
    });
  },
  components: {
    VueLoadingButton
  }
};
</script>
