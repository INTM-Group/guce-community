<template>
  <v-form ref="form" v-model="valid">
    <v-row>
      <v-col cols="12" sm="4">
        <v-text-field
          dense
          hide-details="auto"
          prepend-icon="mdi-account"
          v-model="item.first_name"
          :readonly="!edition"
          :label="
            $vuetify.lang.t('$vuetify.users.first_name') + (edition ? ' *' : '')
          "
          :rules="[
            rules.required(
              $vuetify.lang.t('$vuetify.rules.required', [
                $vuetify.lang.t('$vuetify.users.first_name'),
              ])
            ),
          ]"
        />
      </v-col>
      <v-col cols="12" sm="4">
        <v-text-field
          dense
          hide-details="auto"
          prepend-icon="mdi-account"
          v-model="item.last_name"
          :readonly="!edition"
          :label="
            $vuetify.lang.t('$vuetify.users.last_name') + (edition ? ' *' : '')
          "
          :rules="[
            rules.required(
              $vuetify.lang.t('$vuetify.rules.required', [
                $vuetify.lang.t('$vuetify.users.last_name'),
              ])
            ),
          ]"
        />
      </v-col>
      <v-col cols="12" sm="4">
        <v-text-field
          dense
          hide-details="auto"
          prepend-icon="mdi-phone"
          v-model="item.phone"
          :readonly="!edition"
          :label="
            $vuetify.lang.t('$vuetify.users.phone')
          "
        />
      </v-col>
      <v-col cols="12" sm="6">
        <v-text-field
          dense
          type="email"
          hide-details="auto"
          prepend-icon="mdi-email"
          required
          :readonly="!edition || !isRoot"
          v-model="item.email"
          :label="
            $vuetify.lang.t('$vuetify.users.email') + (isRoot && edition ? ' *' : '')
          "
        />
      </v-col>
      <v-col cols="12" sm="6">
        <v-combobox
          dense
          hide-details="auto"
          prepend-icon="mdi-briefcase"
          v-model="item.department"
          :items="departments"
          :readonly="!edition"
          :label="
            $vuetify.lang.t('$vuetify.users.department') + (edition ? ' *' : '')
          "
          :rules="[
            rules.required(
              $vuetify.lang.t('$vuetify.rules.required', [
                $vuetify.lang.t('$vuetify.users.department'),
              ])
            ),
          ]"
        />
      </v-col>
      <v-col cols="12" sm="6" v-if="!edition">
        <v-text-field
          dense
          hide-details="auto"
          prepend-icon="mdi-calendar"
          readonly
          :value="displayFromDate(item.created_at)"
          :label="$vuetify.lang.t('$vuetify.users.created_at')"
        />
      </v-col>
      <v-col cols="12" sm="6" v-if="!edition">
        <v-text-field
          dense
          hide-details="auto"
          prepend-icon="mdi-calendar"
          readonly
          :value="item.last_login ? displayFromDate(item.last_login) : '-'"
          :label="$vuetify.lang.t('$vuetify.users.last_login')"
        />
      </v-col>
      <v-col cols="12" sm="6">
        <v-select
          prepend-icon="mdi-account-box-outline"
          dense
          required
          v-model="item.type"
          :items="userTypes"
          :deletable-chips="edition"
          :label="
            $vuetify.lang.t('$vuetify.users.accountType') + (edition ? ' *' : '')
          "
          :readonly="!edition"
          :rules="[
            rules.required(
              $vuetify.lang.t('$vuetify.rules.required', [
                $vuetify.lang.t('$vuetify.users.accountType'),
              ])
            ),
          ]"
        />
      </v-col>
       <v-col cols="12" sm="6">
        <v-select
          prepend-icon="mdi-face-agent"
          dense
          required
          v-model="item.responsable_id"
          item-value="id"
          item-text="fullName"
          :items="serviceUsers"
          :readonly="!edition"
          :label="$vuetify.lang.t('$vuetify.users.ro')"
        />
      </v-col>
    </v-row>
  </v-form>
</template>

<script>
import _reduce from "lodash/reduce";
import _get from "lodash/get";
import displayFromDate from "@/utilities/displayFromDate";
import User, { userTypes } from "@/models/User";
import { mapGetters, mapState } from "vuex";
import { mapCacheActions } from "vuex-cache";

export default {
  props: {
    edition: {
      type: Boolean,
      default: false,
    },
  },
  data: () => ({
    userTypes: [],
    valid: false,
    rules: {
      required: (message) => (v) => (!!v && v != "-") || message,
      email: (message) => (v) =>
        /^[\w\.=-]+@[\w\.-]+\.[\w]{2,3}$/i.test(v) || message,
    },
  }),
  created() {
    this.userTypes = _reduce(
      userTypes,
      (result, value, key) =>
        value
          ? [
              ...result,
              {
                text: this.$vuetify.lang.t(`$vuetify.users.types.type_${key}`),
                value: value,
              },
            ]
          : result,
      []
    );
    this.getRoles();
    this.getServices();
  },
  computed: {
    ...mapState("app", {
      dialogActive: (state) => state.dialogActive,
    }),
    ...mapState("auth", ["user"]),
    ...mapState("users", {
      users:(state) => state.items,
      item: (state) => state.current,
      departments: (state) => state.departments,
    }),
    ...mapGetters("roles", {
      roles: "forSelect",
    }),
    ...mapGetters("services", {
      services: "forSelect",
    }),
    modalComponent() {
      return this.item.id ? "user-edition" : "user-creation";
    },
    isRoot() {
      return _get(this.user.has_permissions, "root", false);
    },
    serviceUsers(){
      return this.users.filter((user) => user.type & User.TYPE_SERVICE);
    }
  },
  watch: {
    valid() {
      this.$emit("validate", this.valid);
    },
  },
  methods: {
    ...mapCacheActions("roles", { getRoles: "all" }),
    ...mapCacheActions("services", { getServices: "all" }),
    displayFromDate,
  },
};
</script>
