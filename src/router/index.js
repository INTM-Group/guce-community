import Vue from "vue";
import VueRouter from "vue-router";
import { publicRoute, protectedRoute } from "./config";
import NProgress from "nprogress";
import "nprogress/nprogress.css";
const routes = publicRoute.concat(protectedRoute);
import store from "@/store";

NProgress.configure({ showSpinner: false });

Vue.use(VueRouter);

const router = new VueRouter({
  mode: process.env.NODE_ENV.trim() == "downloaded" ? "hash" : "history",
  base: process.env.BASE_URL,
  linkActiveClass: "active",
  routes
});

// router storage wait
const waitForStorageToBeReady = async (to, from, next) => {
  await store.restored;
  next();
};

router.beforeEach(waitForStorageToBeReady);

// router gards
router.beforeEach((to, from, next) => {
  NProgress.start();
  const token = store.getters["auth/getAccessToken"];
  if (to.meta.protected && !token) {
    return next({ name: "login", query: { redirect: to.path } });
  } else {
    NProgress.done();
  }
  next();
});

router.afterEach(() => {
  NProgress.done();
});

export default router;
