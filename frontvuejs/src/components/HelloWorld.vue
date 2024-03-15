<template>
  <div class="content">
    <p>User ID: {{ result.userId }}</p>
    <p>Title: {{ result.title }}</p>
  </div>
</template>

<script>
/*
import axios from "axios";
export default {
  data() {
    return {
      result: null,
    };
  },
  created() {
    axios.get("https://jsonplaceholder.typicode.com/todos/1").then((response) => {
      this.result = response.data;
    });
  },
};
*/

import axios from "axios";

const data = {
  data() {
    return {
      result: null,
    };
  },
  created() {
    axios.get("https://jsonplaceholder.typicode.com/todos/1").then((response) => {
      this.result = response.data;
    });
  },
};

export default data;


</script>

<style scoped>
h3 {
  margin: 40px 0 0;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
</style>
