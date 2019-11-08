<template>
  <div class="container">
    <div class="row">
      <!-- Step 1 -->
      <div class="col-md-8 step1" v-if="step==0">
        <form>
          <div class="form-group">
            <label for="exampleFormControlInput1">Domain URL</label>
            <small id="passwordHelpInline" class="text-muted">Please enter you domain to continue</small>

            <input
              v-model="domainName"
              type="url"
              class="form-control"
              id="domain"
              placeholder="yourdomain.com"
            />
          </div>
          <div class="form-group">
            <label for>Domain Activation Type</label>
            <select v-model="type">
              <option value="dns">DNS Activation</option>
              <option value="file">File Activation</option>
            </select>
          </div>
          <div class="form-group">
            <VueLoadingButton :loading="loading" @click.native="addDomainClicked">Add domain</VueLoadingButton>
          </div>
        </form>
      </div>

      <!-- Step 2 -->
      <div class="col-md-8 step2" v-if="step==1">
        <div v-if="step!=1" class="step2-disabled"></div>
        <form>
          <div class="form-group">
            <div v-if="type==='dns'">
              Please create TXT record and put below content in there.
              <div class="alert alert-info long-text">arvancloud-{{domain.token}}</div>
            </div>
            <div v-if="type==='file'">
              Please download
              <a href="#download" @click="downloadLinkClick">this</a>
              file and put it in your site root (public_html)
            </div>
            <VueLoadingButton
              :loading="loading"
              @click.native="confirmDomainHandler"
            >Confirm Domain {{domainName}}</VueLoadingButton>
          </div>
        </form>
      </div>

      <!-- Step 3 -->
      <div class="col-md-8 step3" v-if="step==2">
        <div class="alert alert-success">Your Domain Activate successfully, Enjoy our services.</div>
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
      if (!e.validateUrl(e.domainName)) {
        alert("Please enter a valid URL!");
        return;
      }
      e.loading = true;
      fetchData(`${e.baseUrl}/domain/create`, {
        domain: e.domainName,
        type: e.type
      }).then(data => {
        if (data.success) {
          e.step = 1;
          e.domain = data.domain;
        } else {
          alert(data.message);
        }
        e.loading = false;
      });
    },
    downloadLinkClick: () => {
      serverBus.$emit("download-link-clicked");
    },
    downloadLinkHandler: e => {
      let element = document.createElement("a");
      element.setAttribute(
        "href",
        "data:text/plain;charset=utf-8," + encodeURIComponent(e.domain.token)
      );
      element.setAttribute("download", `arvancloud-${e.domain.token}.txt`);
      element.style.display = "none";
      document.body.appendChild(element);
      element.click();
      document.body.removeChild(element);
    },
    confirmDomain: () => {
      serverBus.$emit("confirm-domain-clicked");
    },
    confirmDomainHandler() {
      this.loading = true;
      fetchData(`${this.baseUrl}/domain/confirm`, {
        domain_id: this.domain.id
      }).then(data => {
        console.log(data);
        if (data.success) {
          this.step = 2;
        } else {
          alert(data.message);
        }
        this.loading = false;
      });
    }
  },
  data() {
    return {
      domainName: "",
      type: "dns",
      loading: false,
      baseUrl: "",
      step: 0,
      domain: {}
    };
  },
  created() {
    this.baseUrl = `${window.location.origin}`;
    serverBus.$on("add-domain-clicked", () => {
      this.addDomainClickedHandler(this);
    });
    serverBus.$on("download-link-clicked", () => {
      this.downloadLinkHandler(this);
    });
  },
  components: {
    VueLoadingButton
  }
};
</script>


<style lang="scss">
.step1,
.step2,
.step3 {
  margin-top: 50px;
  position: relative;
}
.long-text {
  overflow-x: scroll;
  white-space: nowrap;
}
</style>
