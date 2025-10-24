function toggleRow(fila) {
    const filas = document.querySelectorAll('#datosUsuario tr');

    if (fila.classList.contains('selected')) {
        fila.classList.remove('selected');
    } else {
        filas.forEach(row => row.classList.remove('selected'));
        fila.classList.add('selected');
    }
};