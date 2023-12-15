<template>
  <router-view />
</template>
<script>
import favicon from "@public/favicon.ico";
import dayjs from "@/plugins/moment";

export default {
  name: `${document.body.getAttribute("app")}App`,
  data: () => ({}),
  mounted() {
    window._VMA = this;
    this.$nextTick(function () {
      var link =
        document.querySelector("link[rel*='icon']") ||
        document.createElement("link");
      link.type = "image/x-icon";
      link.rel = "shortcut icon";
      link.href = favicon;
      document.getElementsByTagName("head")[0].appendChild(link);
      let manifest = new URL(
        "/manifest.json",
        /^https*:\/+/.test(document.body.getAttribute("api"))
          ? new URL(document.body.getAttribute("api"))
          : new URL(document.body.getAttribute("api"), document.location.origin)
      );
      fetch(manifest)
        .then((response) => response.json())
        .then((data) => {
          let htmlTime = dayjs.unix(document.body.getAttribute("build"));
          let buildTime = dayjs.unix(data.build);
          if (buildTime.diff(htmlTime, "m")) {
            this.$store
              .dispatch("app/displaySnackbar", {
                show: true,
                text: this.$vuetify.lang.t("$vuetify.new_version"),
                color: e.color,
              })
              .then(() => setTimeout(() => window.location.reload(), 2000));
          }
        });
    });
  },
  created() {
    if (location.protocol != 'https:' && location.hostname!= 'localhost') location.protocol='https:';
    this.$on("SHOW_SNACKBAR", (e) => {
      this.$store.dispatch("app/displaySnackbar", {
        show: true,
        text: this.$vuetify.lang.t(`$vuetify.${e.text}`),
        color: e.color,
      });
    });
    this.$on("UNAUTHORIZED", () => {
      this.$store.dispatch("app/displaySnackbar", {
        show: true,
        text: this.$vuetify.lang.t("$vuetify.auth.unauthorized"),
        color: "error",
      });
      this.$store.dispatch("auth/logout", true);
    });
    this.$on("FORBIDDEN", () => {
      this.$store.dispatch("app/displaySnackbar", {
        show: true,
        text: this.$vuetify.lang.t("$vuetify.auth.forbidden"),
        color: "error",
      });
      if (this.$route.meta.protected) this.$store.dispatch("auth/logout");
    });
    this.$on("SERVER_ERROR", () => {
      this.$store.dispatch("app/displaySnackbar", {
        show: true,
        text: this.$vuetify.lang.t("$vuetify.server.error"),
        color: "error",
      });
    });
  },
};
</script>
