<template>
  <v-container fill-height="fill-height">
    <v-layout align-center="align-center" justify-center="justify-center">
      <v-flex class="reset-form text-xs-center">
        <h1 class="ma-2 grey--text text-center">
          {{ $vuetify.lang.t("$vuetify.reset.title", [clientName]) }}
        </h1>
        <v-alert
          v-if="codeError"
          border="left"
          type="warning"
          colored-border
          dismissible
          :close-text="$vuetify.lang.t('$vuetify.reset.action')"
          close-icon="mdi-login"
          @input="goToLogin"
          class="grey--text elevation-2"
        >
          {{ $vuetify.lang.t("$vuetify.reset.code_error") }}
        </v-alert>
        <v-card v-else light elevation="2">
          <v-card-title>
            {{ $vuetify.lang.t("$vuetify.reset.subtitle") }}<br />
          </v-card-title>
          <v-form
            ref="formReset"
            v-model="formValid"
            class="px-4"
            @submit.prevent="login"
          >
            <v-text-field
              prepend-icon="mdi-email"
              name="email"
              type="email"
              :label="$vuetify.lang.t('$vuetify.reset.email')"
              :placeholder="$vuetify.lang.t('$vuetify.reset.email')"
              required
              :rules="formRule.email"
              v-model="formModel.email"
            />
          </v-form>
          <v-card-actions>
            <v-spacer />
            <v-btn
              large
              elevation="1"
              color="primary"
              @click="login"
              :loading="loading"
              :disabled="!formValid"
            >
              {{ $vuetify.lang.t("$vuetify.reset.action") }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-flex>
    </v-layout>
  </v-container>
</template>
<script>
import { mapActions } from "vuex";

export default {
  name: "reset",
  data() {
    return {
      codeError: false,
      clientName: document.body.getAttribute("client"),
      loading: false,
      formValid: false,
      formModel: {
        email: null,
      },
      formRule: {
        email: [
          (v) =>
            /^[\w\.=-]+@[\w\.-]+\.[\w]{2,3}$/i.test(v) ||
            this.$vuetify.lang.t("$vuetify.rules.required", [
              this.$vuetify.lang.t("$vuetify.auth.email"),
            ]),
        ],
      },
    };
  },
  methods: {
    ...mapActions("auth", ["resetPassword"]),
    goToLogin() {
      this.$router.push("/");
    },
    login() {
      if (this.$refs.formReset.validate()) {
        this.loading = true;
        this.resetPassword({
          ...this.formModel,
          ...this.$route.params,
        })
          .then((result) => {
            if (result !== false) {
              this.$router.push({ name: "login" });
            }
            this.loading = false;
          })
          .catch((e) => {
            console.log(e);
            this.codeError = true;
          })
          .finally(() => (this.loading = false));
      }
      return false;
    },
  },
};
</script>
<style scoped>
.reset-form {
  max-width: 500px;
}
</style>
