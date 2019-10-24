<template>
  <div class="container">
    <div class="row">all domains</div>
  </div>
</template>

<script>
import { fetchData } from "../api.js";

export default {
  methods: {},
  data() {
    return {
      domains: []
    };
  },
  created() {
    this.baseUrl = `${window.location.origin}`;
  }
};
</script>


