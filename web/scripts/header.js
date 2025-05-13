const imagenUsuarioHeader = document.getElementById("imagenUsuario");
let aliasUsuario;
let cookiesBuscar = document.cookie.split("; ");
for (const cookie of cookiesBuscar) {
    let [nombre, valor] = cookie.split("=");
    if (nombre == "nombreUsuario") {
        aliasUsuario = decodeURIComponent(valor);
    }
}

imagenUsuarioHeader.setAttribute("data-alias", aliasUsuario);
imagenUsuarioHeader.src = `https://localhost:8080/iconos_usuarios/${aliasUsuario}.jpeg`;

const cerrarSesion = document.getElementById("cerrarSesion");
cerrarSesion.addEventListener("click", (evento) => {
    evento.preventDefault();
    fetch("https://localhost:8080/controladores/cerrar_sesion.php", {
        method: "GET"
    }).then((response) => {
        console.log("hola")
        document.cookie = "nombreUsuario=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        window.location.href = "https://localhost:443/index.html";
    })
});