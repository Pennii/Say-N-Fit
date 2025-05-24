const imagenUsuarioHeader = document.getElementById("imagenUsuario");
let aliasUsuario = null;
let cookiesBuscar = document.cookie.split("; ");
if ((document.cookie.includes("nombreUsuario=") && window.location.href != "https://localhost:443/vistas/back_office.html")
    || (document.cookie.includes("admin=") && window.location.href == "https://localhost:443/vistas/back_office.html")) {
    for (const cookie of cookiesBuscar) {
        let [nombre, valor] = cookie.split("=");
        if (nombre == "nombreUsuario") {
            aliasUsuario = decodeURIComponent(valor);
        }
    }
    if (window.location.href == "https://localhost/vistas/inicio.html") {
        console.log(window.location.href)
    }
    if (aliasUsuario) {
        imagenUsuarioHeader.setAttribute("data-alias", aliasUsuario);
        imagenUsuarioHeader.src = `https://localhost:8080/iconos_usuarios/${aliasUsuario}.jpeg`;

        const cerrarSesion = document.getElementById("cerrarSesion");
        cerrarSesion.addEventListener("click", (evento) => {
            evento.preventDefault();
            fetch("https://localhost:8080/controladores/cerrar_sesion.php", {
                method: "GET"
            }).then((response) => {
                document.cookie = "nombreUsuario=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                if (document.cookie.includes("admin=")) {
                    document.cookie = "admin=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                }
                window.location.href = "https://localhost:443/index.html";
            })
        });
    } else {
        window.location.href = "https://localhost:443/index.html";
    }

} else {
    window.location.href = "https://localhost:443/index.html";
}
