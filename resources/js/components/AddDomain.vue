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
              v-model="domain"
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
              <div class="alert alert-info long-text">arvancloud-{{token}}</div>
            </div>
            <div v-if="type==='file'">
              Please download
              <a href="#download" @click="downloadLinkClick">this</a>
              file and put it in your site root (public_html)
            </div>
            <VueLoadingButton
              :loading="loading"
              @click.native="confirmDomain"
            >Confirm Domain {{domain}}</VueLoadingButton>
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
      if (!e.validateUrl(e.domain)) {
        alert("Please enter a valid URL!");
        return;
      }
      e.loading = true;
      fetchData(`${e.baseUrl}/domain/create`, {
        domain: e.domain,
        type: e.type
      }).then(data => {
        if (data.success) {
          e.step = 1;
          e.token = data.token;
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
        "data:text/plain;charset=utf-8," + encodeURIComponent(e.token)
      );
      element.setAttribute("download", `arvancloud-${e.token}.txt`);
      element.style.display = "none";
      document.body.appendChild(element);
      element.click();
      document.body.removeChild(element);
    },
    confirmDomain: () => {
      serverBus.$emit("confirm-domain-clicked");
    },
    confirmDomainHandler: e => {
      e.loading = true;
      fetchData(`${e.baseUrl}/domain/confirm`, { domain: e.domain }).then(
        data => {
          console.log(data);
          if (data.success) {
            e.step = 2;
          } else {
            alert(data.message);
          }
          e.loading = false;
        }
      );
    }
  },
  data() {
    return {
      domain: "",
      type: "dns",
      loading: false,
      baseUrl: "",
      step: 0,
      token: ""
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
    serverBus.$on("confirm-domain-clicked", () => {
      this.confirmDomainHandler(this);
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
