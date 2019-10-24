<template>
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <!-- message -->
        <div v-if="message.has">
          <div :class="'alert alert-' + message.type">{{message.text}}</div>
        </div>
        <!-- table -->
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>domain id</th>
              <th>domain owner</th>
              <th>domain name</th>
              <th>domain activation status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(domain, name, index) in domains" :key="index">
              <th>{{domain.id}}</th>
              <th>{{domain.user_id}}</th>
              <th>{{domain.domain}}</th>
              <th>{{domain.activation_status}}</th>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { getData } from "../api.js";

export default {
  methods: {
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


