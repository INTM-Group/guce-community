/**
 *
 * @param {number} value
 */
export default function asPercentage(value) {
  let real = value / 100;
  return real
    ? Number(real).toLocaleString(undefined, {
        style: "percent",
        minimumFractionDigits: 0
      })
    : "- %";
}
