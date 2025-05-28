const lista = document.getElementById("lista");

const volver = document.getElementById("volver");

//Variables que dependen de las cookies
let claveGrupo;
let lider;
let cookies = document.cookie.split("; ");
for (const cookie of cookies) {
    let [nombre, valor] = cookie.split("=");
    if (nombre == "editarUsuarios") {
        claveGrupo = decodeURIComponent(valor);
    }
    if (nombre == "nombreUsuario") {
        lider = decodeURIComponent(valor);
    }
}

window.addEventListener("load", () => {
    const datos = {
        grupo: claveGrupo,
        usuario: lider
    }
    fetch("https://localhost:8080/controladores/edicion_usuarios.php", {
        method: "POST",
        body: JSON.stringify(datos),
    })
        .then((response) => response.json())
        .then((data) => {
            if (!data.lider) {
                window.location.href = './grupos.html';
            }
            if (data.usuarios.length > 1) {
                // Muestra el listado de usuarios del grupo
                for (const usuario of data.usuarios) {
                    if (lider != usuario[0]) {
                        let valor = [usuario[0], claveGrupo];
                        lista.innerHTML += `
                    <div class="usuarioListado">
                        <div class="usuario">
                            <img src="https://localhost:8080/iconos_usuarios/${usuario[0]}.jpeg" class="imagenUsuario"/>
                            <p>${usuario[0]}</p>
                        </div>
                        <div class="opciones">
                            <form id="editarGrupo">
                                <button class="btn" name="eliminar" value="${valor}">Eliminar</button>
                                <button class="btn" name="convertir" value="${valor}">Convertir en lider</button> 
                            </form>
                        </div>
                    </div>
                    `;
                    }
                }
                const editarGrupo = document.getElementById("editarGrupo");
                // Añade los eventos a los botones de editar grupo
                editarGrupo.addEventListener("submit", (event) => {
                    event.preventDefault();
                    const datos = new FormData();
                    datos.append(event.submitter.name, event.submitter.value)
                    fetch("https://localhost:8080/controladores/edicion_usuarios.php", {
                        method: "POST",
                        body: datos
                    }).then(response => response.json()).then(data => {
                        window.location.reload();
                    });
                })
            } else {
                alert("No hay otros usuarios en este grupo");
                window.location.href = './grupos.html';
            }

        });
});

volver.addEventListener("click", () => {
    document.cookie =
        "editarUsuarios=0; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
    window.location.href = "./grupos.html";
});