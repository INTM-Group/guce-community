import { LayoutAuth, LayoutDefault } from "@/layouts";

export const publicRoute = [
  {
    path: "*",
    component: () => import("@/views/error/NotFound.vue")
  },
  {
    path: "/auth",
    component: LayoutAuth,
    redirect: "/auth/login",
    hidden: true,
    children: [
      {
        path: "login",
        name: "login",
        component: () => import("@/views/auth/Login.vue")
      }
    ]
  },
  {
    path: "/activate/:code",
    component: LayoutAuth,
    hidden: true,
    children: [
      {
        path: "",
        name: "activate",
        component: () => import("@/views/auth/Activation.vue")
      }
    ]
  },
  {
    path: "/404",
    name: "404",
    component: () => import("@/views/error/NotFound.vue")
  },
  {
    path: "/500",
    name: "500",
    component: () => import("@/views/error/Error.vue")
  }
];

export const protectedRoute = [
  {
    path: "/",
    component: LayoutDefault,
    meta: {
      protected: true,
      group: "apps"
    },
    redirect: "/dashboard",
    children: [
      {
        path: "/dashboard",
        name: "dashboard",
        meta: {
          protected: true,
          refresh: true,
          group: "apps",
          icon: "mdi-view-dashboard"
        },
        component: () => import("@/views/Dashboard.vue")
      },
      {
        path: "/tickets",
        name: "tickets",
        meta: {
          protected: true,
          refresh: true,
          add: true,
          group: "apps",
          icon: "mdi-ticket"
        },
        component: () => import("@/views/TicketList.vue")
      },
      {
        path: "/tickets/:filtered",
        name: "ticketFiltered",
        meta: {
          protected: true,
          refresh: true,
          add: true,
          group: "apps",
          icon: "mdi-ticket",
          hiddenInMenu: true
        },
        component: () => import("@/views/TicketList.vue")
      },
      {
        path: "/users",
        name: "users",
        meta: {
          protected: true,
          refresh: true,
          add: true,
          group: "apps",
          icon: "mdi-account-group-outline"
        },
        component: () => import("@/views/UsersList.vue")
      },
      {
        path: "/roles",
        name: "roles",
        meta: {
          protected: true,
          group: "apps",
          admin: true,
          icon: "mdi-account-box-outline"
        },
        component: () => import("@/views/Roles.vue")
      },
      {
        path: "/services",
        name: "services",
        meta: {
          protected: true,
          refresh: true,
          admin: true,
          group: "apps",
          icon: "mdi-domain"
        },
        component: () => import("@/views/Services.vue")
      },
      {
        path: "/softwares",
        name: "softwares",
        meta: {
          protected: true,
          refresh: true,
          add: true,
          group: "apps",
          icon: "mdi-application"
        },
        component: () => import("@/views/SoftwareView.vue")
      },
      {
        path: "/403",
        name: "access_denied",
        meta: {
          hiddenInMenu: true
        },
        component: () => import("@/views/error/Deny.vue")
      }
    ]
  },
  {
    path: "/ticket",
    component: LayoutDefault,
    meta: {
      protected: true,
      icon: "mdi-ticket"
    },
    children: [
      {
        path: "/ticket/:id",
        name: "ticketView",
        meta: {
          protected: true
        },
        component: () => import("@/views/TicketView.vue")
      }
    ]
  },
  {
    path: "/reset",
    component: LayoutAuth,
    hidden: true,
    children: [
      {
        path: "",
        name: "reset",
        component: () => import("@/views/auth/Reset.vue")
      }
    ]
  }
];
