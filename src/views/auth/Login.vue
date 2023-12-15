<template>
  <v-container fill-height="fill-height">
    <v-layout align-center="align-center" justify-center="justify-center">
      <v-flex class="login-form text-xs-center">
        <h1 class="ma-2 grey--text text-center">
          {{ $vuetify.lang.t("$vuetify.auth.title", [clientName]) }}
        </h1>
        <v-form ref="frmLogin" lazy-validation v-model="formValid">
          <v-card light elevation="2">
            <v-card-text>
              <v-text-field
                prepend-icon="mdi-email"
                autocomplete="off"
                name="login"
                :label="$vuetify.lang.t('$vuetify.auth.email')"
                :placeholder="$vuetify.lang.t('$vuetify.auth.email')"
                type="email"
                required
                :rules="formRule.email"
                v-model="formModel.email"
              />
              <v-text-field
                prepend-icon="mdi-lock"
                autocomplete="off"
                name="password"
                :label="$vuetify.lang.t('$vuetify.auth.password')"
                :placeholder="$vuetify.lang.t('$vuetify.auth.password')"
                type="password"
                :rules="formRule.password"
                required
                v-model="formModel.password"
                v-on:keyup.enter="login"
              />
              <v-btn
              x-small
                color="blue darken-4"
                plain
                class="float-right text-decoration-underline no-uppercase"
                @click="resetPassword()"
              >
                Mot de passe oublié?
              </v-btn>
              <v-checkbox v-model="rememberMe" hide-details light>
                <template slot="label">
                  {{ $vuetify.lang.t("$vuetify.auth.remember") }}
                  <v-dialog
                    v-model="dialog"
                    persistent
                    scrollable
                    overlay-color="white"
                    :fullscreen="$vuetify.breakpoint.smAndDown"
                    :hide-overlay="$vuetify.breakpoint.smAndDown"
                    max-width="50vw"
                  >
                    <template v-slot:[`activator`]="{ on, attrs }">
                      <v-icon
                        class="mx-1"
                        style="margin-top: -1em"
                        x-small
                        color="primary"
                        v-bind="attrs"
                        v-on="on"
                        >mdi-information-outline</v-icon
                      >
                    </template>
                    <v-card>
                      <v-card-title
                        class="headline blue--text text--darken-4"
                        >{{
                          $vuetify.lang.t("$vuetify.auth.rpgd.header")
                        }}</v-card-title
                      >
                      <v-card-text>
                        <v-container>{{
                          $vuetify.lang.t("$vuetify.auth.rpgd.body")
                        }}</v-container>
                      </v-card-text>
                      <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                          color="blue darken-1"
                          text
                          @click="
                            dialog = false;
                            rememberMe = false;
                          "
                          >{{
                            $vuetify.lang.t("$vuetify.auth.rpgd.refuse")
                          }}</v-btn
                        >
                        <v-btn
                          color="blue darken-1"
                          text
                          @click="
                            dialog = false;
                            rememberMe = true;
                          "
                          >{{
                            $vuetify.lang.t("$vuetify.auth.rpgd.agree")
                          }}</v-btn
                        >
                      </v-card-actions>
                    </v-card>
                  </v-dialog>
                </template>
              </v-checkbox>
            </v-card-text>
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
                {{ $vuetify.lang.t("$vuetify.auth.login") }}
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-form>
      </v-flex>
    </v-layout>
  </v-container>
</template>
<script>
import { mapActions } from "vuex";

export default {
  name: "Login",
  data() {
    return {
      clientName: document.body.getAttribute("client"),
      rememberMe: false,
      dialog: false,
      loading: false,
      formValid: false,
      formModel: {
        email: null,
        password: null,
      },
      formRule: {
        email: [
          (v) =>
            /^[\w\.=-]+@[\w\.-]+\.[\w]{2,3}$/i.test(v) ||
            this.$vuetify.lang.t("$vuetify.rules.required", [
              this.$vuetify.lang.t("$vuetify.auth.email"),
            ]),
        ],
        password: [
          (v) =>
            !!v ||
            this.$vuetify.lang.t("$vuetify.rules.required", [
              this.$vuetify.lang.t("$vuetify.auth.password"),
            ]),
        ],
      },
    };
  },
  methods: {
    ...mapActions("auth", { auth: "login" }),
    login() {
      if (this.$refs.frmLogin.validate()) {
        this.loading = true;
        this.$store.commit("auth/SET_RGPD", this.rememberMe);
        this.auth(this.formModel)
          .then(() => {
            if (this.$store.state.auth.token) {
              this.$router.push(this.$route.query.redirect || "/dashboard");
            }
          })
          .finally(() => {
            this.loading = false;
          });
      }
    },
    resetPassword() {
      this.$router.push({
        name: "reset"
      });
    },
  },
};
</script>
<style scoped>
.login-form {
  max-width: 500px;
}

.no-uppercase {
     text-transform: none;
}
</style>
