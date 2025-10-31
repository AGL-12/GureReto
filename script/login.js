document.addEventListener("DOMContentLoaded", () => {
const isLoginPage = document.getElementById("Email") !== null;
const isUsuarioPage = document.getElementById("nombreUsuario") !== null;

if (isLoginPage) inicializarLogin();
if (isUsuarioPage) inicializarPanelUsuario();
});

// === LOGIN ===
function inicializarLogin() {
const loginForm = document.querySelector("form");
if (!loginForm) return;

loginForm.addEventListener("submit", async function (e) {
e.preventDefault();

const email = document.getElementById("Email").value.trim();
const password = document.getElementById("Password").value.trim();
const rememberMe = document.getElementById("RememberMe").checked;

mostrarMensaje("", "");

if (!email || !password) {
  mostrarMensaje("Por favor, completa todos los campos", "error");
  return;
}

const formData = new FormData();
formData.append("email", email);
formData.append("contrasena", password);
formData.append("remember", rememberMe);

const submitButton = loginForm.querySelector('button[type="submit"]');
submitButton.disabled = true;
submitButton.textContent = "Iniciando sesión...";

try {
  const response = await fetch("../php/controller/control.php?action=login", {
    method: "POST",
    body: formData,
  });

  const textResponse = await response.text();
  console.log("Respuesta del servidor:", textResponse);

  const data = JSON.parse(textResponse);

  if (data.success) {
    mostrarMensaje("Inicio de sesión exitoso. Redirigiendo...", "exito");

    if (rememberMe) localStorage.setItem("userEmail", email);
    else localStorage.removeItem("userEmail");

    setTimeout(() => {
      window.location.href =
        data.tipo === "admin" ? "admin.html" : "usuario.html";
    }, 1200);
  } else {
    mostrarMensaje(data.message || "Credenciales incorrectas", "error");
    submitButton.disabled = false;
    submitButton.textContent = "Entrar";
  }
} catch (error) {
  console.error("Error de conexión:", error);
  mostrarMensaje("No se pudo conectar con el servidor: " + error.message, "error");
  submitButton.disabled = false;
  submitButton.textContent = "Entrar";
}


});

const savedEmail = localStorage.getItem("userEmail");
if (savedEmail) {
const emailInput = document.getElementById("Email");
const rememberCheckbox = document.getElementById("RememberMe");
if (emailInput && rememberCheckbox) {
emailInput.value = savedEmail;
rememberCheckbox.checked = true;
}
}
}

function mostrarMensaje(mensaje, tipo) {
const errorDiv = document.querySelector(".text-danger");
if (!errorDiv) return;

if (mensaje) {
errorDiv.textContent = mensaje;
errorDiv.style.display = "block";
errorDiv.style.marginBottom = "15px";
errorDiv.style.color = tipo === "error" ? "red" : "green";
} else {
errorDiv.textContent = "";
errorDiv.style.display = "none";
}
}

// === PANEL USUARIO ===
function inicializarPanelUsuario() {
const usuarioSeleccionado = sessionStorage.getItem("usuarioSeleccionado");

if (usuarioSeleccionado) {
// Admin editando otro usuario
const usuario = JSON.parse(usuarioSeleccionado);
document.getElementById("id").value = usuario.id;
document.getElementById("email").value = usuario.email;
document.getElementById("nombre").value = usuario.nombre;
document.getElementById("apellido").value = usuario.apellido;
document.getElementById("telefono").value = usuario.telefono;
document.getElementById("genero").value = usuario.genero;
// 🔹 Ocultar navegación normal del usuario
const navList = document.querySelector("header nav ul");
if (navList) navList.innerHTML = "";

// 🔹 Crear botón "Volver al panel de administración"
const volverBtn = document.createElement("button");
volverBtn.textContent = "Volver al Panel Admin";
volverBtn.classList.add("btn-volver");
volverBtn.style.margin = "10px";
volverBtn.addEventListener("click", () => {
  sessionStorage.removeItem("usuarioSeleccionado"); // Limpia la selección
  window.location.href = "admin.html";
});

// Insertar el botón en el header
const headerDiv = document.querySelector("header div") || document.querySelector("header");
headerDiv.appendChild(volverBtn);
} else {
  cargarDatosUsuario();
  // Eventos normales
  const btnCerrarSesion = document.getElementById("btnCerrarSesion");
  if (btnCerrarSesion) {
    btnCerrarSesion.addEventListener("click", (e) => {
      e.preventDefault();
      cerrarSesion();
    });
  }

  const btnEditarPerfil = document.getElementById("btnEditarPerfil");
  if (btnEditarPerfil) {
    btnEditarPerfil.addEventListener("click", habilitarEdicion);
  }

  const btnGuardarPerfil = document.getElementById("btnGuardarPerfil");
  if (btnGuardarPerfil) {
    btnGuardarPerfil.addEventListener("click", guardarCambios);
  }
}
}

// Asignar eventos
const btnCerrarSesion = document.getElementById("btnCerrarSesion");
if (btnCerrarSesion) {
btnCerrarSesion.addEventListener("click", (e) => {
e.preventDefault();
cerrarSesion();
});
}

const btnEditarPerfil = document.getElementById("btnEditarPerfil");
if (btnEditarPerfil) {
btnEditarPerfil.addEventListener("click", habilitarEdicion);
}

const btnGuardarPerfil = document.getElementById("btnGuardarPerfil");
if (btnGuardarPerfil) {
btnGuardarPerfil.addEventListener("click", guardarCambios);
}


function cargarDatosUsuario() {
fetch("../php/controller/control.php?action=obtenerSesion", {
method: "GET",
credentials: "same-origin",
})
.then((response) => response.json())
.then((data) => {
if (data.success) {
document.getElementById("nombreUsuario").textContent = data.nombre;
document.getElementById("email").value = data.email;
document.getElementById("nombre").value = data.nombre;
document.getElementById("apellido").value = data.apellido || "";
document.getElementById("telefono").value = data.telefono || "";
document.getElementById("genero").value = data.genero || "No especificado";
} else {
alert("Sesión no válida. Inicia sesión nuevamente.");
window.location.href = "Login.html";
}
})
.catch((error) => {
console.error("Error:", error);
alert("Error al cargar los datos. Redirigiendo al login...");
window.location.href = "Login.html";
});
}

function habilitarEdicion() {
["nombre", "apellido", "telefono"].forEach((id) =>
document.getElementById(id).removeAttribute("readonly")
);
document.getElementById("btnEditarPerfil").style.display = "none";
document.getElementById("btnGuardarPerfil").style.display = "inline-block";
}

function guardarCambios() {
const formData = new FormData();
formData.append("nombre", document.getElementById("nombre").value);
formData.append("apellido", document.getElementById("apellido").value);
formData.append("telefono", document.getElementById("telefono").value);

fetch("../php/controller/control.php?action=actualizarPerfil", {
method: "POST",
body: formData,
credentials: "same-origin",
})
.then((response) => response.json())
.then((data) => {
if (data.success) {
alert("Perfil actualizado correctamente");
["nombre", "apellido", "telefono"].forEach((id) =>
document.getElementById(id).setAttribute("readonly", true)
);
document.getElementById("btnEditarPerfil").style.display = "inline-block";
document.getElementById("btnGuardarPerfil").style.display = "none";
document.getElementById("nombreUsuario").textContent = data.nombre;
} else {
alert(data.message || "Error al actualizar el perfil");
}
})
.catch((error) => {
console.error("Error:", error);
alert("Error de conexión");
});
}

function cerrarSesion() {
if (confirm("¿Estás seguro de que deseas cerrar sesión?")) {
fetch("../php/controller/control.php?action=logout", {
method: "POST",
credentials: "same-origin",
})
.then((response) => response.json())
.finally(() => {
window.location.href = "Login.html";
});
}
}