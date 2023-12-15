export default function setClassCliclable(item) {
    let allClasses = ["clickable", `status${item.status}`, `user-type`, `user-type${(item.types || [])[0]}`];
    return allClasses.join(" ");
}
