
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
ob_start();
?>

<div class="contenido-principal-documentos">
    <h2>📄 Documentos de la Empresa</h2>

    <!-- 🔹 Barra superior con buscador y botón nuevo -->
    <div class="barra-superior">
        <form method="GET" class="form-busqueda">
            <input type="text" name="buscar" placeholder="🔍 Buscar documento..." 
                   value="<?= isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : '' ?>">
            <button type="submit" class="btn-buscar">Buscar</button>
        </form>
       <button class="btn-nuevo" onclick="abrirModal()">➕ Nuevo documento</button>

    </div>
<div class="tabla-wrapper" id="tabla-documentos">
    <table class="tabla-documentos">
    <thead>
        <tr>
            <th>Tipo de documento</th>
         
            <th>Fecha emisión</th>
            <th>Fecha vencimiento</th>
            <th>Observacion</th>
            <th>Archivo</th>
            <th>Nombre archivo</th> <!-- NUEVA COLUMNA -->
            <th>Estado</th>
            <th>Accion</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $busqueda = isset($_GET['buscar']) ? strtolower($_GET['buscar']) : '';
        $filtrados = [];

        if (!empty($busqueda)) {
            foreach ($documentos as $doc) {
                if (str_contains(strtolower($doc['tipo_nombre']), $busqueda) ||
                    str_contains(strtolower($doc['nombre_original']), $busqueda)) {
                    $filtrados[] = $doc;
                }
            }
        } else {
            $filtrados = $documentos;
        }
        ?>

        <?php if (!empty($filtrados)): ?>
            <?php foreach ($filtrados as $doc): ?>
                <tr>
                    <td><?= htmlspecialchars($doc['tipo_nombre']) ?></td>
                    
                    <td><?= htmlspecialchars($doc['fecha_emision']) ?></td>
               <td><?= htmlspecialchars($doc['fecha_vencimiento'] ?? '') ?></td>
                    <td><?= htmlspecialchars($doc['observaciones']) ?></td>
                   <td>
    <a href="<?= BASE_URL . $doc['ruta_archivo'] ?>" target="_blank"> Descargar   </a>
</td>
<!-- Mostrar el nombre ORIGINAL REAL del PDF -->
<td><?= htmlspecialchars($doc['nombre_original']) ?></td>                        

                    <td>
                        <?php
                        $estado = strtolower($doc['estado_nombre']);
                        $color = match ($estado) {
                            'aprobado' => 'green',
                            'por vencer' => 'orange',
                            'vencido' => 'red',
                            default => 'gray'
                        };
                        ?>
                        <span class="estado-badge" style="background-color: <?= $color ?>;">
                            <?= htmlspecialchars($doc['estado_nombre']) ?>
                        </span>
                    </td>

                    <td class="acciones">
                        <a href="#" data-id="<?= $doc['ID'] ?>" class="btn-editar">✏️</a>

                       <a href="#" class="btn-eliminar"
   onclick="abrirModalEliminar(<?= $doc['ID'] ?>)">🗑️</a>

                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" style="text-align:center;">No hay documentos registrados</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php if ($totalPaginas > 1): ?>
<div class="paginacion">
    <?php if ($pagina > 1): ?>
        <a href="?pagina=<?= $pagina - 1 ?>" class="btn-pag">« Anterior</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <a href="?pagina=<?= $i ?>"
           class="btn-pag <?= $i == $pagina ? 'activo' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($pagina < $totalPaginas): ?>
        <a href="?pagina=<?= $pagina + 1 ?>" class="btn-pag">Siguiente »</a>
    <?php endif; ?>
</div>
<?php endif; ?>

</div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
<script>
document.addEventListener("DOMContentLoaded", () => {

    const tabla = document.getElementById("tabla-documentos");

    document.addEventListener("click", async (e) => {

        // 🛑 SOLO aplicar AJAX si el click es EXACTAMENTE en una .btn-pag
        if (!e.target.classList.contains("btn-pag")) {
            return; // todo lo demás funciona normal
        }

        e.preventDefault();

        const url = e.target.getAttribute("href");

        try {
            const respuesta = await fetch(url, {
                headers: { "X-Requested-With": "XMLHttpRequest" }
            });

            const html = await respuesta.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");

            const nuevaTabla = doc.querySelector("#tabla-documentos");
            tabla.innerHTML = nuevaTabla.innerHTML;

        } catch (err) {
            console.error("Error en paginación AJAX:", err);
        }
    });
});
</script>

<!-- 🔹 MODAL NUEVO DOCUMENTO -->
<div id="modal-nuevo-doc" class="modal-overlay">
  <div class="modal-content">
    <h3>📤 Nuevo Documento</h3>

    <form id="form-nuevo-doc" enctype="multipart/form-data" method="POST" 
     action="<?= BASE_URL ?>documentos/guardarDocumento">


      <div class="form-grupo">
        <label for="tipo_doc">Tipo de Documento:</label>
        <select name="id_tipo_doc" id="tipo_doc" required>
          <option value="">Seleccionar...</option>
        </select>
      </div>

    <input type="hidden" name="nombre_original" id="nombre_original">


      <!-- 🔹 FECHAS EN 2 COLUMNAS (MISMA ESTRUCTURA QUE EDITAR) -->
      <div class="form-row">
        <div class="form-grupo">
   <label for="fecha_emision">Fecha de emisión</label>
        <input type="date" id="fecha_emision" name="fecha_emision" required>
</div>

<div class="form-grupo">
    <label for="fecha_vencimiento">Fecha de vencimiento</label>
    <input type="date" id="fecha_vencimiento" name="fecha_vencimiento">
</div>

      </div>

      <div class="form-grupo">
        <label for="observaciones">Observaciones:</label>
        <textarea name="observaciones" id="observaciones"></textarea>
      </div>

      <div class="form-grupo">
        <label for="archivo_pdf">Archivo PDF:</label>
        <input type="file" name="archivo_pdf" id="archivo_pdf" 
               accept="application/pdf" required>
      </div>

      <div class="modal-buttons">
        <button type="submit" class="btn-guardar">Guardar</button>
        <button type="button" class="btn-cerrar" id="cerrar-modal">Cancelar</button>
      </div>

    </form>
  </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("modal-nuevo-doc");
  const btnAbrir = document.querySelector(".btn-nuevo");
  const btnCerrar = document.getElementById("cerrar-modal");
  const tipoDocSelect = document.getElementById("tipo_doc");
  const fechaVenc = document.getElementById("fecha_vencimiento");

  // 🔹 Abrir modal
  btnAbrir.addEventListener("click", () => {
    modal.style.display = "flex";  // <-- asegúrate que .modal-overlay usa display:flex
    cargarTiposDocumento();
  });

  // 🔹 Cerrar modal
  btnCerrar.addEventListener("click", () => {
    modal.style.display = "none";
    document.getElementById("form-nuevo-doc").reset();
  });

  // 🔹 Cargar tipos de documento dinámicamente
  function cargarTiposDocumento() {
    fetch("<?= BASE_URL ?>documentos/obtenerTiposDocumento")

      .then(res => {
        if (!res.ok) throw new Error('HTTP error ' + res.status);
        return res.json();
      })
      .then(data => {
        tipoDocSelect.innerHTML = '<option value="">Seleccionar...</option>';
        data.forEach(item => {
          const opt = document.createElement("option");
          opt.value = item.id_tipo_doc;
          opt.textContent = item.tipo_nombre;
          opt.dataset.req = String(item.req_vencimiento);
          tipoDocSelect.appendChild(opt);
        });
      })
      .catch(err => console.error("Error cargando tipos de documento:", err));
  }

  // 🔹 Activar o desactivar fecha de vencimiento según el tipo
  tipoDocSelect.addEventListener("change", function() {
    const selected = this.options[this.selectedIndex];
    const req = selected.dataset.req;

    if (req === "1") {
      fechaVenc.disabled = false;
      fechaVenc.placeholder = "";
    } else {
      fechaVenc.disabled = true;
      fechaVenc.value = "";
      fechaVenc.placeholder = "----";
    }
  });
});
</script>
<?php if (isset($_GET['alerta'])): ?>
<script>
    const tipo = '<?= $_GET['alerta'] ?>';

    let mensaje = "";
    let color = "";

    if (tipo === "exito") {
        mensaje = "✅ Documento guardado correctamente 🎉";
        color = "#4CAF50";
    } 
    else if (tipo === "editado") {
        mensaje = "✏️ Documento editado correctamente";
        color = "#2196F3";
    }
    else {
        mensaje = "⚠️ Ocurrió un error al guardar el documento";
        color = "#f44336";
    }

    const alerta = document.createElement('div');
    alerta.classList.add('alerta-mensaje');
    alerta.innerHTML = `<p>${mensaje}</p>`;
    document.body.appendChild(alerta);

    alerta.style.position = 'fixed';
    alerta.style.top = '50%';
    alerta.style.left = '50%';
    alerta.style.transform = 'translate(-50%, -50%)';
    alerta.style.background = 'white';
    alerta.style.padding = '20px 30px';
    alerta.style.borderRadius = '10px';
    alerta.style.boxShadow = '0 0 10px rgba(0,0,0,0.3)';
    alerta.style.fontFamily = 'Arial, sans-serif';
    alerta.style.color = color;
    alerta.style.zIndex = '9999';
    alerta.style.transition = 'opacity 0.3s ease';

    setTimeout(() => {
        alerta.style.opacity = '0';
        setTimeout(() => alerta.remove(), 300);
    }, 1800);
     history.replaceState(null, "", window.location.pathname);
</script>
<?php endif; ?>

<!-- 🔹 MODAL EDITAR DOCUMENTO -->
<div id="modal-editar-doc" class="modal-overlay">
  <div class="modal-content">
    <h3>✏️ Editar Documento</h3>
    <form id="form-editar-doc" enctype="multipart/form-data" method="POST"
          action="<?= BASE_URL ?>documentos/actualizarDocumento">

      <input type="hidden" name="id_documento" id="edit_id_documento">

      <div class="form-grupo">
        <label for="edit_tipo_doc">Tipo de Documento:</label>
        <select name="id_tipo_doc" id="edit_tipo_doc" required></select>
      </div>

      <div class="form-grupo">
    <label for="edit_nombre_original">Nombre del archivo:</label>
    <input type="text" id="edit_nombre_original" disabled>
    <input type="hidden" name="nombre_original" id="edit_nombre_original_hidden">
</div>

<div class="form-row">
    <div class="form-grupo mitad">
        <label for="edit_fecha_emision">Fecha emisión:</label>
        <input type="date" name="fecha_emision" id="edit_fecha_emision" required>
    </div>

    <div class="form-grupo mitad">
        <label for="edit_fecha_vencimiento">Fecha vencimiento:</label>
        <input type="date" name="fecha_vencimiento" id="edit_fecha_vencimiento">
    </div>
</div>


      <div class="form-grupo">
        <label for="edit_observaciones">Observaciones:</label>
        <textarea name="observaciones" id="edit_observaciones" rows="3"></textarea>
      </div>

     <div class="form-grupo">
  <label for="edit_archivo_pdf">Reemplazar PDF (opcional):</label>
  <input type="file" name="archivo_pdf" id="edit_archivo_pdf" accept="application/pdf">
  <p id="archivo-actual" style="font-size: 13px; margin-top: 5px; color: #555;"></p>
</div>


      <div class="modal-buttons">
        <button type="submit" class="btn-guardar">Guardar cambios</button>
        <button type="button" class="btn-cerrar" id="cerrar-modal-editar">Cancelar</button>
      </div>
    </form>
  </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const modalEditar = document.getElementById("modal-editar-doc");
  const btnCerrarEditar = document.getElementById("cerrar-modal-editar");

  const campoVencimiento = document.getElementById("edit_fecha_vencimiento");
  const selectTipo = document.getElementById("edit_tipo_doc");
  const archivoActual = document.getElementById("archivo-actual");

  // 🔹 Cerrar modal editar
  btnCerrarEditar.addEventListener("click", () => {
    modalEditar.style.display = "none";
    document.getElementById("form-editar-doc").reset();
  });

  // 🔹 Escuchar los botones de edición
document.addEventListener("click", function(e) {
  if (e.target.closest(".btn-editar")) {
    e.preventDefault();
    const id = e.target.closest(".btn-editar").dataset.id;
    abrirModalEditar(id);
  }
});


  // ============================================================
  //    🔥🔥 FUNCION PRINCIPAL: Abrir modal con datos completos
  // ============================================================
  function abrirModalEditar(id) {

    // 1️⃣ Cargar tipos de documento (PRIMERO)
    fetch("<?= BASE_URL ?>documentos/obtenerTiposDocumento")
      .then(res => res.json())
      .then(tipos => {
        selectTipo.innerHTML = '<option value="">Seleccionar...</option>';

        tipos.forEach(item => {
          const opt = document.createElement("option");
          opt.value = item.id_tipo_doc;
          opt.textContent = item.tipo_nombre;
          selectTipo.appendChild(opt);
        });

        // 2️⃣ Luego traer los datos del documento
        return fetch(`<?= BASE_URL ?>documentos/obtenerDocumentoPorId?id=${id}`);
      })

      // 3️⃣ Rellenar el modal con los datos
      .then(res => res.json())
      .then(doc => {
        if (!doc) {
          alert("Documento no encontrado");
          return;
        }

        document.getElementById("edit_id_documento").value = doc.ID;
       document.getElementById("edit_nombre_original").value = doc.nombre_original;
// 🔒 Desactivar edición del nombre del archivo
document.getElementById("edit_nombre_original").disabled = true;
document.getElementById("edit_nombre_original").style.background = "#eee";
document.getElementById("edit_nombre_original").style.cursor = "not-allowed";

        document.getElementById("edit_fecha_emision").value = doc.fecha_emision || "";
        document.getElementById("edit_observaciones").value = doc.observaciones || "";

        // 📄 Mostrar archivo actual
        archivoActual.innerHTML = doc.ruta_archivo
          ? `📄 Archivo actual: <a href="<?= BASE_URL ?>${doc.ruta_archivo}" target="_blank">${doc.nombre_original}</a>`
          : "📁 No hay archivo cargado";

        // 🕓 Fecha de vencimiento
        if (!doc.fecha_vencimiento || doc.fecha_vencimiento === "0000-00-00") {
          campoVencimiento.value = "";
          campoVencimiento.placeholder = "---";
        } else {
          campoVencimiento.value = doc.fecha_vencimiento;
        }

        // 🚫 PRIMERO seleccionar el tipo en el select
        selectTipo.value = doc.tipo_ID;

        // 🔥 Luego activar/desactivar vencimiento según el tipo
        aplicarReglasSegunReq(doc.Req_vencimiento);


        // Finalmente mostrar el modal
        modalEditar.style.display = "flex";
      })
      .catch(err => console.error("Error:", err));
  }

  // ============================================================
  //     🔥🔥 FUNCIÓN: Reglas según tipo de documento
  // ============================================================
function aplicarReglasSegunReq(req_vencimiento) {
    if (req_vencimiento == 2) {
        campoVencimiento.disabled = true;
        campoVencimiento.value = "";
        campoVencimiento.placeholder = "---";
    } else {
        campoVencimiento.disabled = false;
    }
}
  // ============================================================
  //  🔥 Cuando el usuario cambie de tipo manualmente
  // ============================================================
  selectTipo.addEventListener("change", (e) => {
    const tipoID = e.target.value;

    // Volvemos a obtener la lista de tipos
    fetch("<?= BASE_URL ?>documentos/obtenerTiposDocumento")
      .then(res => res.json())
      .then(tipos => {
         const tipo = tipos.find(t => t.id_tipo_doc == tipoID);
         if (tipo) aplicarReglasSegunReq(tipo.req_vencimiento);
      });
});

});
</script>
<!-- Modal ELIMINAR -->
<div id="modalEliminar" class="modal-eliminar">
    <div class="modal-contenido">
        <h3>❗ Confirmar eliminación</h3>
        <p>¿Estás segura de eliminar este documento?</p>

        <div class="modal-botones">
            <button class="btn-cancelar" onclick="cerrarModalEliminar()">Cancelar</button>
            <a id="btnConfirmarEliminar" href="#" class="btn-confirmar">Eliminar</a>
        </div>
    </div>
</div>
<script>
function abrirModalEliminar(id) {
    const modal = document.getElementById("modalEliminar");
    const btn = document.getElementById("btnConfirmarEliminar");

    btn.href = "<?= BASE_URL ?>documentos/eliminar/" + id;
    modal.style.display = "flex";
}

function cerrarModalEliminar() {
    document.getElementById("modalEliminar").style.display = "none";
}

// Cerrar al hacer clic fuera
window.onclick = function(e) {
    const modal = document.getElementById("modalEliminar");
    if (e.target === modal) {
        modal.style.display = "none";
    }
}
</script>


