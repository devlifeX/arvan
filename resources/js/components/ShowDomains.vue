<template>
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <!-- message -->
        <div v-if="message.has || domains.length <= 0">
          <div :class="'alert alert-' + message.type">{{message.text}}</div>
        </div>
        <!-- table -->
        <table v-else class="table table-striped table-hover">
          <thead>
            <tr>
              <th>domain id</th>
              <th>domain name</th>
              <th>domain activation status</th>
              <th>Activation</th>
              <th>operation</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(domain, name, index) in domains" :key="index">
              <th>{{domain.id}}</th>
              <th>{{domain.domain}}</th>
              <th>
                <i v-if="domain.activation_status==1" class="fa fa-check green"></i>
                <i v-else class="fa fa-close red"></i>
              </th>
              <th>
                <a
                  v-if="domain.activation_type === 'file'"
                  href="#download"
                  @click="downloadLinkHandler(domain.activation_token)"
                >Download File</a>
                <div
                  v-if="domain.activation_type === 'dns'"
                  class="long-text"
                >arvancloud-{{domain.activation_token}}</div>
              </th>
              <th>
                <button title="remove?" @click="remove(domain.id)">
                  <span class="fa fa-trash red"></span>
                </button>
              </th>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { getData, deleteData } from "../api.js";

export default {
  methods: {
    downloadLinkHandler(token) {
      let element = document.createElement("a");
      element.setAttribute(
        "href",
        "data:text/plain;charset=utf-8," + encodeURIComponent(token)
      );
      element.setAttribute("download", `arvancloud-${token}.txt`);
      element.style.display = "none";
      document.body.appendChild(element);
      element.click();
      document.body.removeChild(element);
    },
    fetchAll() {
      getData(`${this.baseUrl}/domain/show`).then(data => {
        if (data.success) {
          this.domains = data.domains;
          if (data.domains.length <= 0) {
            this.message.has = true;
          }
        } else {
          this.message.type = "danger";
          this.message.text = "Somethings went wrong!";
          this.message.has = true;
        }
      });
    },
    remove(id) {
      deleteData(`${this.baseUrl}/domain/delete/${id}`, { id }).then(data => {
        if (data.success) {
          this.domains = this.domains.filter(domain_id => {
            return domain_id === id;
          });
        } else {
          this.message.type = "danger";
          this.message.text = "Somethings went wrong!";
          this.message.has = true;
        }
      });
    }
  },
  mounted() {
    this.baseUrl = `${window.location.origin}`;
    this.fetchAll();
  },
  data() {
    return {
      domains: [],
      baseUrl: "",
      message: {
        has: false,
        type: "info",
        text: "There is no domain, add new one!"
      }
    };
  }
};
</script>


<style lang="scss">
.long-text {
  overflow-x: scroll;
  white-space: nowrap;
}
</style>
