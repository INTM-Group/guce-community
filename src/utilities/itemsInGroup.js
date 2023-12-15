import _set from "lodash/set";

export default function itemsInGroup() {
    if (!this.groupping) return 0;
    return this.items.reduce((result, item) => {
        if (!result[item[this.groupping]]) _set(result, item[this.groupping], 0);
        result[item[this.groupping]]++;
        return result;
    }, {});
}