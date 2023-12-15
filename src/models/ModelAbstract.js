import dayjs from "@/plugins/moment";
import _clone from 'lodash/clone';

export default class ModelAbstract {
  original = {};
  constructor(original) {
    this.setOriginal(original);
  }

  reset() {
    this.setOriginal(this.original);
  }

  setOriginal(original = {}) {
    if (original) {
      this.original = original;
      this.id = original["id"];
      this.created_at = original["created_at"];
      this.updated_at = dayjs(original["updated_at"]);
    }
  }

  clone() {
    return _clone(this);
  }

  valueOf() {
    return this.id;
  }
}
