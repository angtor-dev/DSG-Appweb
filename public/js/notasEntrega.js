// Almacena los artículos agregados: {id: cantidad}
let articulosSeleccionados = {};

function renderArticulos() {
    const tbody = document.querySelector('#tabla-articulos tbody');
    tbody.innerHTML = '';
    for (const id in articulosSeleccionados) {
        const nombre = articulosMap[id] || 'Artículo';
        const cantidad = articulosSeleccionados[id];
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${nombre}</td>
            <td>
                <input type="number" min="1" class="form-control form-control-sm articulo-cantidad-input" data-id="${id}" value="${cantidad}">
                <input type="hidden" name="articulos[${id}]" value="${cantidad}">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger quitar-articulo" data-id="${id}">&times;</button>
            </td>
        `;
        tbody.appendChild(row);
    }

    // Listeners para quitar artículos
    document.querySelectorAll('.quitar-articulo').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            delete articulosSeleccionados[id];
            renderArticulos();
        });
    });

    // Listeners para actualizar cantidad
    document.querySelectorAll('.articulo-cantidad-input').forEach(input => {
        input.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            let val = parseInt(this.value);
            if (isNaN(val) || val < 1) val = 1;
            articulosSeleccionados[id] = val;
            renderArticulos();
        });
    });
}

function agregarValidaciones() {
    document.getElementById('agregar-articulo')?.addEventListener('click', function() {
        const selector = document.getElementById('articulo-selector');
        const cantidadInput = document.getElementById('articulo-cantidad');
        const id = selector.value;
        const cantidad = parseInt(cantidadInput.value);

        if (!id || isNaN(cantidad) || cantidad <= 0) {
            alert('Seleccione un artículo y una cantidad válida.');
            return;
        }

        // Si ya existe, suma la cantidad
        if (articulosSeleccionados[id]) {
            articulosSeleccionados[id] += cantidad;
        } else {
            articulosSeleccionados[id] = cantidad;
        }

        renderArticulos();
        selector.value = '';
        cantidadInput.value = '';
    });
}