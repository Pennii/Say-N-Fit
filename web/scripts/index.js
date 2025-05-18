// mensaje que se muestra si un usuario no ingresa los datos correctos al iniciar sesion
const login = document.getElementById("login");

const formulario = document.getElementById("formulario");
formulario.addEventListener("submit", (evento) => {
    evento.preventDefault();

    fetch("https://localhost:8080/controladores/login_proceso.php", {
        method: 'POST',
        body: new FormData(formulario),
    }).then(response => response.json()).then(data => {
        if (data.usuario) {
            document.cookie = `nombreUsuario=${data.usuario}; path=/`;
            window.location.href = 'https://localhost/vistas/inicio.html';
        } else {
            if (!document.getElementById("datosIncorrectos")) {
                window.alert("Datos incorrectos");
            }
        }
    }).catch(error => {
        console.error('Error:', error);
    });
});

