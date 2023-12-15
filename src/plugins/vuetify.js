import Vue from "vue";
import Vuetify from "vuetify/lib/framework";
import Fragment from "vue-fragment";
import { TiptapVuetifyPlugin } from "tiptap-vuetify";
import "tiptap-vuetify/dist/main.css";
import chartjsPluginDatalabels from "chartjs-plugin-datalabels";
import locales from "@/locales";
import store from "@/store";

const vuetify = new Vuetify({
    theme: {
        dark: store.getters["app/isDark"],
        options: {
            customProperties: true
        },
        themes: store.getters["app/getThemeColors"]
    },
    lang: {
        locales: locales,
        current: "fr"
    }
});

Vue.use(Vuetify);
Vue.use(Fragment.Plugin);
Vue.use(chartjsPluginDatalabels);
Vue.use(TiptapVuetifyPlugin, {
    vuetify,
    iconsGroup: "mdi"
});

export default vuetify;