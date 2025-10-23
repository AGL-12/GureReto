 document.getElementById("eliminar").addEventListener("click", function() {
            const filaSeleccionada = document.querySelector("#datosUsuario tr.selected");
            if (filaSeleccionada) {
                filaSeleccionada.remove();
            } else {
                alert("Por favor, selecciona una fila para eliminar.");
            }
        });
        const filas = document.querySelectorAll("#datosUsuario tr");
        filas.forEach(fila => {
            fila.addEventListener("click", () => {
                if (fila.classList.contains("selected")) {
                    fila.classList.remove("selected");
                } else {
                    filas.forEach(f => f.classList.remove("selected"));
                    fila.classList.add("selected");
                }
            });
        });
