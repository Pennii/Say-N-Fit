const imagenUsuarioHeader = document.getElementById("imagenUsuario");
let aliasUsuario;
let cookiesBuscar = document.cookie.split("; ");
for (const cookie of cookiesBuscar) {
    let [nombre, valor] = cookie.split("=");
    if (nombre == "nombreUsuario") {
        aliasUsuario = decodeURIComponent(valor);
    }
}
imagenUsuarioHeader.src = `../iconos_usuarios/${aliasUsuario}.jpeg`;
