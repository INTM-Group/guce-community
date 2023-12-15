<template>
  <v-container
    min-width="400"
    class="d-flex flex-column justify-space-around align-stretch"
    fluid
  >
    <v-data-table
      dense
      fixed-header
      class="elevation-2 reverseScrollY"
      ref="table"
      item-key="id"
      sort-by="id"
      :loading-text="$vuetify.lang.t('$vuetify.users.loading')"
      :headers="tableHeaders"
      :loading="loading"
      :items="items"
      :group-by="groupping"
      :footer-props="{ 'items-per-page-options': [10, 25, 50, 100, -1] }"
      :items-per-page.sync="itemsPerPage"
      :item-class="setClassCliclable"
      :header-props="{ sortIcon: 'mdi-chevron-up' }"
      @update:group-by="setGrouBy"
      @click:row="editUser"
    >
      <template
        v-slot:[`group.header`]="{ group, groupBy, headers, toggle, isOpen }"
      >
        <td :colspan="headers.length">
          <v-btn @click="toggle" x-small icon :ref="group">
            <v-icon v-if="isOpen">mdi-chevron-down</v-icon>
            <v-icon v-else>mdi-chevron-right</v-icon>
          </v-btn>
          <span class="mx-5 font-weight-bold">
            {{
              tableHeaders.find((item) => groupBy.indexOf(item.value) >= 0).text
            }}
            :
            {{
              groupBy[0] != "type"
                ? group
                : $vuetify.lang.t(`$vuetify.users.${groupBy[0]}._${group}`)
            }}
            ({{ itemsInGroup[group] }})
          </span>
          <v-btn class="float-right" x-small icon @click="groupping = null">
            <v-icon>mdi-close-thick</v-icon>
          </v-btn>
        </td>
      </template>
      <template v-slot:[`header.department`]>
        <button @click="groupping = 'department'" style="white-space: nowrap">
          {{ $vuetify.lang.t("$vuetify.users.department") }}
          <v-icon x-small>mdi-filter-outline</v-icon>
        </button>
      </template>
      <template v-slot:[`header.type`]>
        <button @click="groupping = 'type'" style="white-space: nowrap">
          {{ $vuetify.lang.t("$vuetify.users.accountType") }}
          <v-icon x-small>mdi-filter-outline</v-icon>
        </button>
      </template>
      <template v-slot:[`item.type`]="{ item }">
        <v-chip
          v-for="lab in typesToText(item)"
          :key="lab"
          class="my-1 mx-1"
          small
          >{{ lab }}</v-chip
        >
      </template>
      <template v-slot:[`item.created_at`]="{ item }">
        {{ displayFromDate(item.created_at) }}
      </template>
      <template v-slot:[`item.last_login`]="{ item }">
        {{ item.last_login ? displayFromDate(item.last_login) : "-" }}
      </template>
      <template v-slot:[`item.phone`]="{ item }">
        {{ item.phone ? item.phone : "-" }}
      </template>
      <template v-slot:[`item.service_id`]="{ item }">
        {{ displayService(item.service_id) }}
      </template>
      <template v-slot:[`item.responsable_id`]="{ item }">
        {{ displayRO(item) }}
      </template>
      <template v-slot:footer>
        <div style="position: absolute">
          <v-speed-dial v-model="fab" top>
            <template v-slot:activator>
              <v-tooltip top :disabled="fab">
                <template v-slot:activator="{ on: tooltip }">
                  <v-btn class="mx-2" v-model="fab" icon v-on="{ ...tooltip }">
                    <v-icon v-if="fab" color="grey"> mdi-close </v-icon>
                    <v-icon v-else color="grey"> mdi-download </v-icon>
                  </v-btn>
                </template>
                <span>{{ $vuetify.lang.t("$vuetify.tooltips.download") }}</span>
              </v-tooltip>
            </template>
            <v-tooltip right>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  fab
                  light
                  small
                  @click="downloadData('CSV')"
                  v-bind="attrs"
                  v-on="on"
                >
                  <v-icon>mdi-file-delimited</v-icon>
                </v-btn>
              </template>
              <span>{{ $vuetify.lang.t("$vuetify.tooltips.cvs") }}</span>
            </v-tooltip>
            <v-tooltip right>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  fab
                  light
                  small
                  @click="downloadData('XLSX')"
                  v-bind="attrs"
                  v-on="on"
                >
                  <v-icon>mdi-file-excel</v-icon>
                </v-btn>
              </template>
              <span>{{ $vuetify.lang.t("$vuetify.tooltips.excel") }}</span>
            </v-tooltip>
          </v-speed-dial>
        </div>
      </template>
    </v-data-table>
  </v-container>
</template>

<script>
import { mapState, mapMutations, mapGetters } from "vuex";
import { mapCacheActions } from "vuex-cache";
import dayjs from "@/plugins/moment";
import exportData from "@/utilities/exportData";
import _memoize from "lodash/memoize";
import displayFromDate from "@/utilities/displayFromDate";
import setClassCliclable from "@/utilities/setClassCliclable";
import itemsInGroup from "@/utilities/itemsInGroup";

const typesToText = _memoize(function (user) {
  return user.types.map((item) =>
    this.$vuetify.lang.t("$vuetify.users.roles._" + item)
  );
});

const displayService = _memoize(function (serviceId) {
  let service = this.services.find((item) => item.id === serviceId);
  if (service) return service.name;
  return "-";
});

const displayRO = function (u) {
  if (!this.items) return "-";
  let responsible = this.items.find((item) => item.id === u.responsable_id);
  if (responsible) return responsible.fullName;
  return "-";
};

export default {
  components: {},
  data() {
    return {
      fab: false,
      currentTime: dayjs,
      groupping: null,
      selectedItemUser: { id: null },
      tableHeaders: [
        {
          text: this.$vuetify.lang.t("$vuetify.users.first_name"),
          align: "start",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "first_name",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.users.last_name"),
          align: "start",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "last_name",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.users.phone"),
          align: "start",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "phone",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.users.email"),
          align: "start",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "email",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.users.accountType"),
          align: "start",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "type",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.users.department"),
          align: "start",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "department",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.users.ro"),
          align: "start",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "responsable_id",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.users.connections"),
          align: "center",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "connections",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.users.tickets"),
          align: "center",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "tickets",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.users.messages"),
          align: "center",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "messages",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.users.created_at"),
          align: "start",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "created_at",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.users.last_login"),
          align: "start",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "last_login",
        },
      ],
    };
  },
  created() {
    this.getRoles();
    this.getServices();
  },
  mounted() {
    this.getUsers();
  },
  computed: {
    ...mapState("app", ["userItemsPerPage"]),
    ...mapState("users", {
      loading: (state) => state.loading,
      items: (state) => state.items,
    }),
    ...mapGetters("roles", {
      roles: "forSelect",
    }),
    ...mapState("services", {
      services: (state) => state.items,
    }),
    itemsInGroup,
    itemsPerPage: {
      get: function () {
        return this.groupping ? -1 : this.userItemsPerPage;
      },
      set: function (value) {
        if (value > 0 || value === -1) this.setUserItemsPerPage(value);
      },
    },
  },
  methods: {
    ...mapCacheActions("users", { getUsers: "all" }),
    ...mapMutations("users", { selectUser: "select" }),
    ...mapCacheActions("roles", { getRoles: "all" }),
    ...mapMutations("app", [
      "userDialog",
      "setUserItemsPerPage"
    ]),
    ...mapCacheActions("services", { getServices: "all" }),
    displayFromDate,
    setClassCliclable,
    displayService,
    displayRO,
    typesToText,
    setGrouBy(value) {
      if (!value) {
        this.groupping = null;
        this.itemsPerPage = 20;
      } else {
        this.itemsPerPage = -1;
        this.$nextTick(() => {
          let table = this.$refs.table;
          let keys = Object.keys(table.$vnode.componentInstance.openCache);
          try {
            keys.forEach((x) => {
              table.$vnode.componentInstance.openCache[x] = false;
            });
          } catch (e) {}
        });
      }
    },
    downloadData(type) {
      // TODO: Speed optimization _.memoize in traslate
      exportData(
        this.items,
        {
          lang: (str) => this.$vuetify.lang.t(str),
          typesToText: this.typesToText,
          items:this.items,
        },
        type,
        "Utilisateurs"
      );
    },
    editUser(user) {
      this.selectUser(user);
      this.userDialog();
    },
  },
};
</script>
<style>
.clickable {
  cursor: pointer;
  user-select: none;
}
.v-data-table.reverseScrollY > .v-data-table__wrapper,
.v-data-table.reverseScrollY > .v-data-table__wrapper > table {
  transform: rotateX(180deg);
}
.v-data-table.reverseScrollY > .v-data-table__wrapper {
  overflow-y: hidden; /* */
}
</style>
