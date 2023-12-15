import ModelAbstract from "@/models/ModelAbstract";
import dayjs from "@/plugins/moment";

export default class User extends ModelAbstract {
  static TYPE_DISABLED = 0;
  static TYPE_CLIENT = 1; //CH
  static TYPE_SUPPLIER = 2; // INTM
  static TYPE_SERVICE = 4; //RO
  static TYPE_MANAGER = 8; //RC

  constructor(original = {}) {
    super(original);
  }

  setOriginal(oringinal = {}) {
    super.setOriginal(oringinal);
    this.type = oringinal["type"];
    this.email = oringinal["email"];
    this.service_id = oringinal["service_id"];
    this.creator_id = oringinal["creator_id"];
    this.connections = oringinal["connections"];
    this.last_login = oringinal["last_login"];
    this.first_name = oringinal["first_name"] || "";
    this.last_name = oringinal["last_name"] || "";
    this.phone = oringinal["phone"] || "";
    this.responsable_id = oringinal["responsable_id"] || null;
    this.department = oringinal["department"];
    this.has_permissions = oringinal["has_permissions"];
    this.preferences = oringinal["preferences"];
    this.stats = oringinal["stats"] || {
      loginCount: 0,
      avgConected: 0,
      minConsecutive: 0,
      maxConsecutive: 0,
      avgConsecutive: 0
    };
    this.roles = (oringinal["roles"] || []).map(item => item.id);
    this.rolesNames = (oringinal["roles"] || []).map(item => item.description.fr.name);
    this.tickets = oringinal["tickets"];
    this.messages = oringinal["messages"];
    this.fullName = `${this.first_name} ${this.last_name}`;
    this.initials = `${this.first_name.substr(0, 1)}${this.last_name.substr(0, 1)}`;
  }

  get types() {
    if (!this._types) {
      this._types = [];
      for (let key in userTypes) {
        if (Object.hasOwnProperty.call(userTypes, key)) {
          if (this.type & userTypes[key]) this._types.push(userTypes[key]);
        }
      }
    }
    return this._types;
  }

  set types(val) {
    this._types = val;
    this.type = this._types.reduce((a, b) => a + b, 0);
  }

  toSheet({ lang, typesToText, items }, type) {
    let result = {};
    result[lang("$vuetify.users.id")] = this.id;
    result[lang("$vuetify.users.first_name")] = this.first_name;
    result[lang("$vuetify.users.last_name")] = this.last_name;
    result[lang("$vuetify.users.phone")] = this.phone;
    result[lang("$vuetify.users.email")] = this.email;
    result[lang("$vuetify.users.accountType")] = typesToText(this);
    result[lang("$vuetify.users.department")] = this.department;
    let ro = items.find(item => item.id ===this.responsable_id);
    result[lang("$vuetify.users.ro")] = ro ?ro.fullName:'-';
    result[lang("$vuetify.users.connections")] = this.connections;
    result[lang("$vuetify.users.tickets")] = this.tickets;
    result[lang("$vuetify.users.messages")] = this.messages;
    result[lang("$vuetify.users.created_at")] = dayjs(this.created_at)[type === "CSV" ? "format" : "toDate"]("L LTS");
    result[lang("$vuetify.users.last_login")] = this.last_login
      ? dayjs(this.last_login)[type === "CSV" ? "format" : "toDate"]("L LTS")
      : "-";
    return result;
  }
}

const userTypes = {};

userTypes[`${User.TYPE_DISABMED}`] = User.TYPE_DISABMED;
userTypes[`${User.TYPE_CLIENT}`] = User.TYPE_CLIENT;
userTypes[`${User.TYPE_SUPPLIER}`] = User.TYPE_SUPPLIER;
userTypes[`${User.TYPE_SERVICE}`] = User.TYPE_SERVICE;
userTypes[`${User.TYPE_MANAGER}`] = User.TYPE_MANAGER;

export { userTypes };
