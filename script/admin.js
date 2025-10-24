document.addEventListener("DOMContentLoaded", obtenerUsuarios);

async function obtenerUsuarios() {
    try {
        // Llamada a Control.php con la acción 'listar'
        const response = await fetch("../php/controller/Control.php?action=listar");

        // Parsear JSON directamente
        const data = await response.json();

        if (!data.success) {
            console.error("Error de API:", data.error || data.message || "No se pudo obtener la lista de usuarios.");
            return;
        }

        const tabla = document.getElementById("datosUsuario");

        // Limpia las filas anteriores (conserva el encabezado)
        while (tabla.rows.length > 1) {
            tabla.deleteRow(1);
        }

        // Recorre los usuarios y añade las filas a la tabla
        data.usuarios.forEach(usuario => {
            const fila = tabla.insertRow();
            fila.dataset.id = usuario.id;

            fila.addEventListener("click", () => togglerow(fila));

            // Rellena las celdas
            fila.insertCell().textContent = usuario.id;
            fila.insertCell().textContent = usuario.email;
            fila.insertCell().textContent = usuario.nombre;
            fila.insertCell().textContent = usuario.apellido;
            fila.insertCell().textContent = usuario.telefono;
            fila.insertCell().textContent = usuario.genero;
        });

    } catch (error) {
        console.error("Error al obtener usuarios. Revisa tu conexión a DB y las rutas:", error);
    }
}

// Función de selección de fila
function togglerow(fila) {
    const tbody = fila.parentElement;
    tbody.querySelectorAll("tr").forEach(r => r.classList.remove("selected"));
    fila.classList.add("selected");
}

document.getElementById("eliminar").addEventListener("click", async () => {
    const filaSeleccionada = document.querySelector("#datosUsuario tr.selected");
    if (!filaSeleccionada) {
        alert("Selecciona un usuario primero.");
        return;
    }

    const idUsuario = filaSeleccionada.dataset.id;

    if (!confirm("¿Estás seguro de eliminar este usuario?")) return;

    try {
        const response = await fetch(`../php/controller/Control.php?action=borrar&id=${idUsuario}`, {
            method: "POST"
        });
        const data = await response.json();

        if (data.success) {
            alert("Usuario eliminado correctamente.");
            filaSeleccionada.remove(); // Quitar la fila de la tabla
        } else {
            alert("Error al eliminar: " + (data.error || data.message));
        }
    } catch (error) {
        alert("Error de conexión o servidor: " + error);
    }
});
