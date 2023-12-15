<template>
  <v-list dense>
    <v-list-item-group color="primary">
      <template v-for="(file, index) in files">
        <v-divider v-if="index == 0" :key="`DT${index}`" />
        <v-list-item :key="index" @click="donwloadFile(file)">
          <v-list-item-icon>
            <v-icon>mdi-file</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title v-text="file.name" />
            <v-list-item-subtitle v-text="displayFromDate(file.mtime, true)" />
            <v-list-item-subtitle v-text="formatBytes(file.size)" />
          </v-list-item-content>
        </v-list-item>
        <v-divider :key="`DB${index}`" />
      </template>
    </v-list-item-group>
  </v-list>
</template>

<script>
import { mapState } from "vuex";
import { storagePrivate } from "@/plugins/request";
import displayFromDate from "@/utilities/displayFromDate";
import formatBytes from "@/utilities/formatBytes";

export default {
  data: () => ({
  }),
  computed: {
    ...mapState("tickets", ["files"]),
  },
  methods: {
    donwloadFile(file) {
      window.open(storagePrivate.href + file.filesPath + file.name, "_blank");
    },
    displayFromDate,
    formatBytes,
  },
};
</script>
