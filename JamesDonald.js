    function abrirModal(idUsuario) {
        document.getElementById('idUsuario').value = idUsuario;
        document.getElementById('modal').style.display = 'flex';
    }

    function cerrarModal() {
        document.getElementById('modal').style.display = 'none';
    }

    // Cerrar el modal al hacer clic fuera del contenido
    window.onclick = function(event) {
        var modal = document.getElementById('modal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }