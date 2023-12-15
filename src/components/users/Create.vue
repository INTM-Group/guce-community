<template>
  <v-form ref="form" v-model="valid">
    <v-row>
      <v-col cols="12" sm="4">
        <v-text-field
          dense
          hide-details="auto"
          prepend-icon="mdi-account"
          v-model="item.first_name"
          :readonly="!!item.id"
          :label="$vuetify.lang.t('$vuetify.users.first_name') + ' *'"
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
          :readonly="!!item.id"
          :label="$vuetify.lang.t('$vuetify.users.last_name') + ' *'"
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
          :readonly="!!item.id"
          :label="$vuetify.lang.t('$vuetify.users.phone')"
        />
      </v-col>
      <v-col cols="12" sm="6">
        <v-text-field
          dense
          type="email"
          hide-details="auto"
          prepend-icon="mdi-email"
          required
          v-model="item.email"
          :readonly="!!item.id"
          :label="$vuetify.lang.t('$vuetify.users.email') + ' *'"
          :rules="[
            rules.required(
              $vuetify.lang.t('$vuetify.rules.required', [
                $vuetify.lang.t('$vuetify.users.email'),
              ])
            ),
            rules.email($vuetify.lang.t('$vuetify.rules.email')),
          ]"
        />
      </v-col>
      <v-col cols="12" sm="6">
        <v-combobox
          dense
          hide-details="auto"
          prepend-icon="mdi-briefcase"
          v-model="item.department"
          :items="departments"
          :readonly="!!item.id"
          :label="$vuetify.lang.t('$vuetify.users.department') + ' *'"
          :rules="[
            rules.required(
              $vuetify.lang.t('$vuetify.rules.required', [
                $vuetify.lang.t('$vuetify.users.department'),
              ])
            ),
          ]"
        />
      </v-col>
      <v-col cols="12" sm="6">
        <v-select
          prepend-icon="mdi-account-box-outline"
          dense
          required
          v-model="item.type"
          :items="userTypes"
          :label="$vuetify.lang.t('$vuetify.users.accountType') + ' *'"
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
          v-model="item.responsable_id"
          item-value="id"
          item-text="fullName"
          :items="serviceUsers"
          :label="$vuetify.lang.t('$vuetify.users.ro')"
        />
      </v-col>
    </v-row>
  </v-form>
</template>

<script>
import _reduce from "lodash/reduce";
import User, { userTypes } from "@/models/User";
import { mapGetters, mapState } from "vuex";
import { mapCacheActions } from "vuex-cache";

export default {
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
        value &&
        (key != User.TYPE_SUPPLIER ||
          (key == User.TYPE_SUPPLIER && this.user.type & User.TYPE_SUPPLIER))
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
      users: (state) => state.items,
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
    serviceUsers() {
      return this.users.filter((user) => user.type & User.TYPE_SERVICE);
    },
  },
  watch: {
    valid() {
      this.$emit("validate", this.valid);
    },
  },
  methods: {
    ...mapCacheActions("roles", { getRoles: "all" }),
    ...mapCacheActions("services", { getServices: "all" }),
  },
};
</script>
