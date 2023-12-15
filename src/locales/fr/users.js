let users = new String("Utilisateurs");

users.loading = "Chargement en cours... veuillez patienter";

users.singular = "Utilisateur";
users.id = "ID";
users.type = new String("Type(s)");
users.email = "Email";
users.sla = "SLA";
users.department = "Département";
users.created_at = "Créé";
users.updated_at = "Mis à jour";
users.last_login = "Dernière connexion";
users.first_name = "Prénom";
users.last_name = "Nom";
users.phone = "Téléphone";
users.my_permissions = "Autorisations";
users.preferences = "Préférences";
users.role = "Rôle";
users.accountType = "Type de Compte/Rôle";
users.connections = "Nb de visite";
users.tickets = "Nb de tickets";
users.messages = "Nb de réponses";
users.stats = new String("Statistiques de connexion");
users.stats.loginCount = "Sessions";
users.stats.avgConected = "Jours de Connexion (moy.)";
users.stats.minConsecutive = "Jours Consécutifs Minimum";
users.stats.maxConsecutive = "Jours Consécutifs Maximum";
users.stats.avgConsecutive = "Jours Consécutifs (moy.)";
users.ro = "RO Attaché";
users.types = {
  type_0: "Désactivé",
  type_1: "Compte Habilité (CH)", //Client
  type_2: "INTM", //Fournisseur
  type_4: "Responsables  Opérationnels (RO)", //Service
  type_8: "Responsables de Contrat (RC)" //Manager
};
users.roles = {
  _0: "Désactivé",
  _1: "CH", //Client
  _2: "INTM", //Fournisseur
  _4: "RO", //Service
  _8: "RC" //Manager
};
users.type._0 = "Désactivé";
users.type._1 = "CH"; //Client
users.type._2 = "INTM"; //Fournisseur
users.type._4 = "RO"; //Service
users.type._8 = "RC"; //Manager

export default users;
