import ModelAbstract from "@/models/ModelAbstract";

export default class Role extends ModelAbstract {
  constructor(original = {}) {
    super(original);
    this.type = original["type"];
    this.slug = original["slug"];
    this.description = original["description"];
    this.permissions = original["permissions"] || {};
  }
}
