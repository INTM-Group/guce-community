import vuetifyFr from "vuetify/lib/locale/fr";
import camelCase from "lodash/camelCase";

const requireModule = require.context(".", false, /\.js$/);

const ticketView = new String("Ticket");
const ticketFiltered = new String("Ticket {0}");
ticketView.details = "Détails";
ticketView.messages = "Messagerie";
ticketView.files = "Fichiers";
ticketView.notifications = "Notifications";
const softwares = new String("Logiciels");
softwares.totalCount = "Total Software {0} (une toutes les 3 versions)";
const tooltips = {
  cvs: "Format CVS",
  excel: "Format Excel",
  download: "Télécharger"
};

const frLocals = {
  ...vuetifyFr,
  footer: {
    copy: "droits de reproduction autorisée"
  },
  loading: "Chargement en cours",
  softwares,
  ticketView,
  ticketFiltered,
  tooltips,
  new: { m: "Nouveau", f: "Nouvel" },
  server: {
    error: "Erreur du serveur",
    user_duplicated: "Utilisateur existe déjà dans la bdd, veuillez contacter l'admin"
  },
  request: { bad: "Erreur d'appel" },
  fields: "* champs obligatoires",
  dialog: {
    save: "Sauvegarder",
    modify: "Modifier",
    close: "Fermer",
    cancel: "Annuler",
    view: "Voir {0}",
    create: "Créer",
    send: "Envoyer un e-mail d'activation"
  }
};

requireModule.keys().forEach(fileName => {
  if (fileName === "./index.js") return;
  const moduleName = camelCase(fileName.replace(/(\.\/|\.js)/g, ""));

  frLocals[moduleName] = requireModule(fileName).default;
});

export default frLocals;
