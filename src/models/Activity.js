import ModelAbstract from "@/models/ModelAbstract";
import _uniq  from "lodash/uniq";

export default class Activity extends ModelAbstract {
  static TYPE_UNKNOWN = 0;
  static TYPE_UPDATE = 1;
  static TYPE_STATUS = 2;
  static TYPE_MESSAGE = 4;
  static TYPE_CLIENT = 8;
  static TYPE_16 = 16;
  static TYPE_32 = 32;
  static TYPE_64 = 64;
  static TYPE_OTHER = 128;

  constructor(original = {}) {
    super(original);
    this.type = original["type"];
    this.user_id = original["user_id"];
    this.target_type = original["target_type"];
    this.target_id = original["target_id"];
    this.data = original["data"];
    this.message = original["message"];
  }

  get maxType() {
    return Math.max(...this.allTypes);
  }

  get allTypes() {
    return [
      this.type & Activity.TYPE_UPDATE,
      this.type & Activity.TYPE_STATUS,
      this.type & Activity.TYPE_MESSAGE,
      this.type & Activity.TYPE_CLIENT,
      this.type & Activity.TYPE_OTHER
    ];
  }

  get title() {
    return "$vuetify.activities.actions._" + this.maxType;
  }

  get description() {
    return _uniq([this.maxType, this.type & Activity.TYPE_STATUS]).reduce(
      (result, type) => (type ? [...result, "$vuetify.activities.description._" + type] : result),
      []
    );
  }

  get icon() {
    return iconActionType[this.maxType];
  }

  get color() {
    return `status_${this.data.toStatus || this.data.fromStatus}`;
  }

  toString() {
    return this.data.message || this.message;
  }
}

const iconActionType = {};

iconActionType[`${Activity.TYPE_UNKNOWN}`] = "mdi-none";
iconActionType[`${Activity.TYPE_UPDATE}`] = "mdi-update";
iconActionType[`${Activity.TYPE_STATUS}`] = "mdi-none";
iconActionType[`${Activity.TYPE_MESSAGE}`] = "mdi-message";
iconActionType[`${Activity.TYPE_CLIENT}`] = "mdi-message-reply";
iconActionType[`${Activity.TYPE_16}`] = "mdi-none";
iconActionType[`${Activity.TYPE_32}`] = "mdi-none";
iconActionType[`${Activity.TYPE_64}`] = "mdi-none";
iconActionType[`${Activity.TYPE_OTHER}`] = "mdi-crosshairs-question";
