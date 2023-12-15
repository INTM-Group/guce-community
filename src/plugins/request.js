import axios from "axios";
import store from "@/store";

export const entrypoint = /^https*:\/+/.test(document.body.getAttribute("api"))
  ? new URL(document.body.getAttribute("api"))
  : new URL(document.body.getAttribute("api"), document.location.origin);

export const storagePublic = new URL("/storage", entrypoint);

const privateUrl = new URL("/private/storage", entrypoint);
privateUrl.username = "guce";
privateUrl.password = "INTM";

export const storagePrivate = privateUrl;

// create axios
const service = axios.create({
  baseURL: `${entrypoint}/`, // api base_url
  timeout: 2 * 60 * 1000, // timeout,
  headers: {
    //"Access-Control-Allow-Origin": "*",
    "Content-Type": "application/json"
  }
});

// TODO: add online detection

const err = error => {
  try {
    const { status, data } = error.response;
    const { errors } = data;
    let message = [];
    for (let field in errors) {
      message.push(errors[field]);
    }
    switch (status) {
      case 405: //HTTP_METHOD_NOT_ALLOWED
      case 406: //HTTP_NOT_ACCEPTABLE
      case 407: //HTTP_PROXY_AUTHENTICATION_REQUIRED
      case 408: //HTTP_REQUEST_TIMEOUT
      case 409: //HTTP_CONFLICT
      case 410: //HTTP_GONE
      case 411: //HTTP_LENGTH_REQUIRED
      case 412: //HTTP_PRECONDITION_FAILED
      case 413: //HTTP_REQUEST_ENTITY_TOO_LARGE
      case 414: //HTTP_REQUEST_URI_TOO_LONG
      case 415: //HTTP_UNSUPPORTED_MEDIA_TYPE
      case 416: //HTTP_REQUESTED_RANGE_NOT_SATISFIABLE
      case 417: //HTTP_EXPECTATION_FAILED
      case 418: //HTTP_I_AM_A_TEAPOT
      case 421: //HTTP_MISDIRECTED_REQUEST
      case 423: //HTTP_LOCKED
      case 424: //HTTP_FAILED_DEPENDENCY
      case 425: //HTTP_TOO_EARLY
      case 426: //HTTP_UPGRADE_REQUIRED
      case 428: //HTTP_PRECONDITION_REQUIRED
      case 429: //HTTP_TOO_MANY_REQUESTS
      case 431: //HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE
      case 451: //HTTP_UNAVAILABLE_FOR_LEGAL_REASONS
      case 400: //HTTP_BAD_REQUEST
        window._VMA.$emit("SHOW_SNACKBAR", {
          show: true,
          text: "request.bad",
          color: "error"
        });
        break;

      case 401: //HTTP_UNAUTHORIZED
        window._VMA.$emit("UNAUTHORIZED");
        break;

      case 403: //HTTP_FORBIDDEN
        window._VMA.$emit("FORBIDDEN");
        break;

      case 404: //HTTP_NOT_FOUND
        window._VMA.$emit("SHOW_SNACKBAR", {
          show: true,
          text: message,
          color: "warning"
        });
        break;

      case 422: //HTTP_UNPROCESSABLE_ENTITY
        window._VMA.$emit("SHOW_SNACKBAR", {
          show: true,
          text: message,
          color: "error"
        });
        break;

      case 500: //HTTP_INTERNAL_SERVER_ERROR
        if (data.error == "query.exception" && /users_email_unique/g.test(data.message)) {
          window._VMA.$emit("SHOW_SNACKBAR", {
            show: true,
            text: "server.user_duplicated",
            color: "error"
          });
          break;
        }
      case 501: //HTTP_NOT_IMPLEMENTED
      case 502: //HTTP_BAD_GATEWAY
      case 503: //HTTP_SERVICE_UNAVAILABLE
      case 504: //HTTP_GATEWAY_TIMEOUT
      case 505: //HTTP_VERSION_NOT_SUPPORTED
      case 506: //HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL
      case 507: //HTTP_INSUFFICIENT_STORAGE
      case 508: //HTTP_LOOP_DETECTED
      case 510: //HTTP_NOT_EXTENDED
      case 511: //HTTP_NETWORK_AUTHENTICATION_REQUIRED
        window._VMA.$emit("SERVER_ERROR");
        break;

      default:
        break;
    }
  } catch (e) {}
  return Promise.reject(error);
};

// request interceptor

service.interceptors.request.use(config => {
  //config.headers["Access-Control-Allow-Origin"] = "*";
  config.headers["Content-Type"] = "application/json";
  let token = store.getters["auth/getAccessToken"];
  let targetURL = new URL(config.baseURL);
  if (targetURL.password) {
    config.auth = {
      username: targetURL.username,
      password: targetURL.password
    };
  } else if (token) config.headers["Authorization"] = "Bearer " + token;

  return config;
}, err);

// response interceptor

service.interceptors.response.use(({ data, config }) => {
  if (["put", "post", "delete", "patch"].includes(config.method) && data.meta) {
    window._VMA.$emit("SHOW_SNACKBAR", {
      text: data.meta.message,
      color: "success"
    });
  }
  if (data.error !== undefined) {
    window._VMA.$emit("API_FAILED", data.error);
  }

  return data;
}, err);

export default service;
