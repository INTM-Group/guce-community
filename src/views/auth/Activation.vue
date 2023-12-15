<template>
  <v-container fill-height="fill-height">
    <v-layout align-center="align-center" justify-center="justify-center">
      <v-flex class="activation-form text-xs-center">
        <h1 class="ma-2 grey--text text-center">
          {{ $vuetify.lang.t("$vuetify.activation.title", [clientName]) }}
        </h1>
        <v-alert v-if="codeError" border="left" type="warning"
        colored-border dismissible
        :close-text="$vuetify.lang.t('$vuetify.activation.action')"
        close-icon="mdi-login" @input="goToLogin"
        class="grey--text elevation-2">
          {{ $vuetify.lang.t("$vuetify.activation.code_error") }}
        </v-alert>
        <v-card v-else light elevation="2">
          <v-card-title>
            {{ $vuetify.lang.t("$vuetify.activation.subtitle") }}<br />
            <small
              class="text-caption"
              style="white-space: pre-wrap !important"
              >{{ $vuetify.lang.t("$vuetify.rules.complexity") }}</small
            >
          </v-card-title>
          <v-form ref="frmLogin" v-model="formValid" class="px-4">
            <v-text-field
              prepend-icon="mdi-lock"
              autocomplete="off"
              name="password"
              :label="$vuetify.lang.t('$vuetify.activation.password')"
              :placeholder="$vuetify.lang.t('$vuetify.activation.password')"
              type="password"
              :rules="formRule.password"
              required
              v-model="formModel.password"
              v-on:keyup.enter="login"
            />
            <v-text-field
              prepend-icon="mdi-lock-outline"
              autocomplete="off"
              name="password_confirmation"
              :label="
                $vuetify.lang.t('$vuetify.activation.password_confirmation')
              "
              :placeholder="
                $vuetify.lang.t('$vuetify.activation.password_confirmation')
              "
              type="password"
              :rules="formRule.password_confirmation"
              required
              v-model="formModel.password_confirmation"
              v-on:keyup.enter="login"
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
              {{ $vuetify.lang.t("$vuetify.activation.action") }}
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
  name: "Activation",
  data() {
    return {
      codeError: false,
      clientName: document.body.getAttribute("client"),
      loading: false,
      formValid: false,
      formModel: {
        password_confirmation: null,
        password: null,
      },
      formRule: {
        password_confirmation: [
          (v) =>
            (!!v && v === this.formModel.password) ||
            this.$vuetify.lang.t("$vuetify.rules.required", [
              this.$vuetify.lang.t("$vuetify.activation.password_confirmation"),
            ]),
        ],
        password: [
          (v) =>
            /^(?=(.*[a-z]){1,})(?=(.*[A-Z]){1,})(?=(.*[0-9]){1,})(?=(.*[!@#$%^&*()\-__+.]){1,}).{8,}$/i.test(
              v
            ) ||
            this.$vuetify.lang.t("$vuetify.rules.password", [
              this.$vuetify.lang.t("$vuetify.activation.password"),
            ]),
        ],
      },
    };
  },
  methods: {
    ...mapActions("auth", ["activation"]),
    goToLogin(){
      this.$router.push('/');
    },
    login() {
      if (this.$refs.frmLogin.validate()) {
        this.loading = true;
        this.activation({
          ...this.formModel,
          ...this.$route.params,
        })
          .then(() => {
            this.$router.push({ name: "dashboard" });
            this.loading = false;
          })
          .catch(()=>this.codeError = true)
          .finally(() => this.loading = false);
      }
    },
  },
};
</script>
<style scoped>
.activation-form {
  max-width: 500px;
}
</style>