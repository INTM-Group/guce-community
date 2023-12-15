import dayjs from "@/plugins/moment";
import asPercentage from "./asPercentage";

export default class LapsDisplayer {
  constructor(value, limit, isFix = true) {
    this.value = value;
    this.isFix = isFix;
    this.duration = dayjs.duration(value);
    this.asHours = this.duration.as("hours");
    this.inHours = Math.floor(this.asHours) + "h" + this.duration.format("mm");
    this.percentage = Number((this.value == 0 ? 0 : (this.asHours / (limit || 3)) * 100).toFixed(3));
    this.percentageLocal = Number(this.percentage).toLocaleString(undefined, {
      maximumFractionDigits: 3
    });
    this.asPercentage = new String(this.value == 0 ? "-" : asPercentage(this.percentage));
    this.asPercentage.valueOf = () => this.percentage;
  }

  toString() {
    return this.inHours;
  }

  valueOf() {
    return this.value == 0 ? 0 : this.asHours;
  }
}
