export default function absoluteHref(val) {
  let regex = /href\=\"(?!https*:)(?!\/+)([^\s]+)\"/gm;
  let subst = `href="//$1"`;
  return val.replace(regex, subst);
}
