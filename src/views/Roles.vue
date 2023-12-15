<template>
  <v-container
    min-width="400"
    class="d-flex flex-column justify-space-around align-stretch"
    fluid
  >
    <div style="max-width: 20vw">
      <v-select
        :items="roles"
        item-text="description.fr.name"
        item-value="id"
        v-model="rol"
        solo
      />
    </div>
    <v-card v-if="rol">
      <v-simple-table>
        <template v-slot:default>
          <thead>
            <tr>
              <th v-for="(head, headIndex) in tableHeaders" :key="headIndex">
                {{ head.text }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(item, functionality) in permissions"
              :key="functionality"
            >
              <td>
                {{
                  $vuetify.lang.t(
                    `$vuetify.roles.functionality.${functionality}`
                  )
                }}
              </td>
              <td>
                <v-icon :color="item.store ? 'success' : 'error'"
                  >mdi-{{
                    item.store ? "check" : "close"
                  }}</v-icon
                >
              </td>
              <td>
                <v-icon :color="item.update ? 'success' : 'error'"
                  >mdi-{{
                    item.update ? "check" : "close"
                  }}</v-icon
                >
              </td>
              <td>
                <v-icon :color="item.destroy ? 'success' : 'error'"
                  >mdi-{{
                    item.destroy ? "check" : "close"
                  }}</v-icon
                >
              </td>
              <td>
                <v-icon :color="item.show ? 'success' : 'error'"
                  >mdi-{{
                    item.show ? "check" : "close"
                  }}</v-icon
                >
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </v-card>
  </v-container>
</template>

<script>
import { mapState } from "vuex";
import { mapCacheActions } from "vuex-cache";

export default {
  components: {},
  data() {
    return {
      tableHeaders: [
        {
          text: this.$vuetify.lang.t("$vuetify.roles.functionality"),
          align: "start",
          sortable: false,
          value: "functionality",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.roles.create").toUpperCase(),
          align: "center",
          sortable: false,
          value: "create",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.roles.modify").toUpperCase(),
          align: "center",
          sortable: false,
          value: "modify",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.roles.delete").toUpperCase(),
          align: "center",
          sortable: false,
          value: "delete",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.roles.read").toUpperCase(),
          align: "center",
          sortable: false,
          value: "read",
        },
      ],
      rol: 1,
    };
  },
  created() {
    this.getRoles();
  },
  computed: {
    ...mapState("roles", {
      name: (state) => state.loading,
      roles: (state) => state.items,
    }),
    permissions() {
      return this.roles.find((item) => item.id === this.rol).permissions || [];
    },
  },
  methods: {
    ...mapCacheActions("roles", { getRoles: "all" }),
  },
};
</script>
