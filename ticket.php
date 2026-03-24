<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISTEMA // RIFA_CHAYA_SPARK</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        :root {
            --main-teal: #32727e; 
            --bg-dark: #0f172a; 
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: var(--bg-dark);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            color: white;
            padding-top: 60px;
        }

        .btn-admin-top {
            position: absolute;
            top: 20px;
            left: 20px;
            background: #3b82f6;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }

        .input-panel {
            background: #fff;
            padding: 25px;
            margin-bottom: 30px;
            border-radius: 16px;
            width: 100%;
            max-width: 500px;
            color: #0f172a;
        }

        .input-group { margin-bottom: 12px; }
        label { display: block; font-size: 13px; font-weight: bold; margin-bottom: 5px; color: #475569; }
        input {
            width: 100%; padding: 10px; border: 2px solid #e2e8f0;
            border-radius: 8px; box-sizing: border-box;
        }

        .btn-generate {
            width: 100%; padding: 14px; background: var(--main-teal);
            color: #fff; border: none; font-weight: bold; cursor: pointer;
            text-transform: uppercase; border-radius: 8px; margin-top: 10px;
        }

        /* --- DISEÑO DE BOLETO --- */
        .ticket-container {
            display: flex;
            background: var(--main-teal); 
            color: white; 
            width: 850px;
            height: 280px;
            position: relative;
            box-sizing: border-box;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .stub {
            width: 32%;
            padding: 20px;
            border-right: 2px dashed rgba(255,255,255,0.3);
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255,255,255,0.1); 
        }

        .stub-header { margin-bottom: 15px; }
        .data-group { margin-bottom: 10px; }
        .label-text { font-weight: bold; font-size: 11px; color: #e2e8f0; text-transform: uppercase; }
        .value-text {
            font-family: 'Courier New', monospace;
            font-size: 15px;
            color: #fff;
            font-weight: bold;
            display: block;
            border-bottom: 1px solid rgba(255,255,255,0.4);
            padding-bottom: 2px;
        }

        .main-ticket {
            width: 68%;
            padding: 25px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: center; 
        }

        .ticket-title { font-size: 22px; font-weight: 900; letter-spacing: 1px; }
        
        .ticket-num-box {
            display: flex;
            align-items: baseline;
            gap: 5px;
        }
        .no-label { 
            font-size: 20px; 
            font-weight: bold; 
            color: rgba(255,255,255,0.9); 
        }
        .num-value { 
            font-size: 24px; 
            font-weight: 900; 
        }

        .prize-text { font-size: 28px; font-weight: 800; color: #fff; margin: 5px 0; }
        .date-text { font-size: 16px; font-weight: bold; color: #fff; }
        .purpose-text { font-size: 13px; color: #e2e8f0; margin-bottom: 15px; }

        .footer-terms {
            font-size: 10px;
            border-top: 1px solid rgba(255,255,255,0.3);
            padding-top: 10px;
            color: #cbd5e1;
            line-height: 1.4;
        }

        .price-tag { font-size: 20px; font-weight: 800; margin-top: 10px; }

        #lista-tickets {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 40px;
            margin-top: 20px;
        }

        .ticket-wrapper { position: relative; }
        .actions-bar { display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 8px; }

        @media (max-width: 900px) {
            .ticket-container { width: 100%; height: auto; flex-direction: column; }
            .stub { width: 100%; border-right: none; border-bottom: 2px dashed rgba(255,255,255,0.3); }
            .main-ticket { width: 100%; }
        }
    </style>
</head>
<body onload="init()">

    <a href="admin.html" class="btn-admin-top">⚙️ PANEL ADMIN</a>

    <div class="input-panel">
        <h2 style="margin-top:0; color: var(--main-teal);">GENERADOR DE TICKETS</h2>
        <div class="input-group">
            <label>Nombre del Participante:</label>
            <input type="text" id="inNombre" placeholder="Nombre completo">
        </div>
        <div class="input-group">
            <label>Correo Electrónico:</label>
            <input type="email" id="inEmail" placeholder="ejemplo@correo.com">
        </div>
        <div class="input-group">
            <label>Número de Boleto:</label>
            <input type="text" id="inNum" style="background: #f1f5f9; font-weight: bold;">
        </div>
        <div class="input-group">
            <label>Celular (Opcional):</label>
            <input type="text" id="inTel" placeholder="Ej. 5512345678">
        </div>
        <button class="btn-generate" onclick="processTicket()">GUARDAR E IMPRIMIR ✅</button>
    </div>

    <h3 style="color: var(--main-teal);">// HISTORIAL DE TICKETS GENERADOS</h3>
    <div id="lista-tickets"></div>

    <script>
        let database = [];

        function init() {
            loadFromStorage();
            autoCompletarDesdeVenta();
        }

        function loadFromStorage() {
            const saved = localStorage.getItem('rifa_digital_v7');
            if (saved) {
                database = JSON.parse(saved);
                renderList();
            }
        }

        // --- NUEVA FUNCIÓN: RECUPERA DATOS DE LA OTRA PÁGINA ---
        function autoCompletarDesdeVenta() {
            const datosGuardados = localStorage.getItem('datosUltimoTicket');
            if (datosGuardados) {
                const datos = JSON.parse(datosGuardados);
                
                document.getElementById('inNombre').value = datos.nombre || "";
                document.getElementById('inEmail').value = datos.email || "";
                document.getElementById('inNum').value = datos.numero || "";
                document.getElementById('inTel').value = datos.celular || "";
                
                // Opcional: Limpiar el almacenamiento después de usarlo para que no se quede pegado el mismo nombre siempre
                // localStorage.removeItem('datosUltimoTicket');
            }
        }

        function processTicket() {
            const n = document.getElementById('inNombre').value;
            const t = document.getElementById('inTel').value || "N/A";
            const e = document.getElementById('inEmail').value;
            const num = document.getElementById('inNum').value;

            if(!n || !e || !num) {
                alert("Por favor, completa los campos requeridos.");
                return;
            }

            const nuevoBoleto = {
                id: 'ticket-' + Date.now(),
                nombre: n, tel: t, email: e, num: num
            };

            database.unshift(nuevoBoleto);
            localStorage.setItem('rifa_digital_v7', JSON.stringify(database));
            
            // Limpiar campos y almacenamiento una vez generado
            localStorage.removeItem('datosUltimoTicket');
            document.getElementById('inNombre').value = "";
            document.getElementById('inTel').value = "";
            document.getElementById('inEmail').value = "";
            document.getElementById('inNum').value = "";

            renderList();
        }

        function deleteTicket(id) {
            if(confirm("¿Eliminar este ticket?")) {
                database = database.filter(item => item.id !== id);
                localStorage.setItem('rifa_digital_v7', JSON.stringify(database));
                renderList();
            }
        }

        async function downloadTicket(id, num) {
            const element = document.getElementById(id);
            const canvas = await html2canvas(element, {
                scale: 2,
                backgroundColor: null
            });
            const link = document.createElement('a');
            link.download = `TICKET_CHAYA_${num}.png`;
            link.href = canvas.toDataURL("image/png");
            link.click();
        }

        function renderList() {
            const contenedor = document.getElementById('lista-tickets');
            contenedor.innerHTML = "";

            database.forEach(b => {
                const wrapper = document.createElement('div');
                wrapper.className = "ticket-wrapper";
                wrapper.innerHTML = `
                    <div class="actions-bar">
                        <button style="background:#22c55e; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer;" onclick="downloadTicket('${b.id}', '${b.num}')">Descargar PNG</button>
                        <button style="background:#ef4444; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer;" onclick="deleteTicket('${b.id}')">Borrar</button>
                    </div>
                    <div class="ticket-container" id="${b.id}">
                        <div class="stub">
                            <div class="stub-header">
                                <span style="font-weight:bold; font-size:14px; letter-spacing:1px;">RIFA CHAYA SPARK</span><br>
                                <span style="font-size:22px; font-weight:bold;">#${b.num}</span>
                            </div>
                            <div class="data-group">
                                <span class="label-text">Participante</span>
                                <span class="value-text">${b.nombre}</span>
                            </div>
                            <div class="data-group">
                                <span class="label-text">E-mail</span>
                                <span class="value-text" style="font-size:11px;">${b.email}</span>
                            </div>
                            <div class="data-group">
                                <span class="label-text">Teléfono</span>
                                <span class="value-text">${b.tel}</span>
                            </div>
                        </div>
                        <div class="main-ticket">
                            <div class="ticket-header">
                                <span class="ticket-title">🎟️ RIFA CHAYA SPARK 🎟️</span>
                                <div class="ticket-num-box">
                                    <span class="no-label">No.</span>
                                    <span class="num-value">${b.num}</span>
                                </div>
                            </div>
                            <div>
                                <p class="prize-text">PREMIO MAYOR: $5,000 MXN</p>
                                <p class="date-text">La rifa se realizará el 30 de agosto del 2026</p>
                                <p class="purpose-text">Este sorteo es para apoyar un viaje a Brasil</p>
                            </div>
                            <div class="footer-terms">
                                EL SORTEO SE LLEVARÁ A CABO SEGÚN LOS 3 ÚLTIMOS DÍGITOS DE LA LOTERÍA NACIONAL.<br>
                                RECUERDE CONSERVAR ESTE COMPROBANTE DIGITAL.
                            </div>
                            <p class="price-tag">VALOR: $100.00 MXN</p>
                        </div>
                    </div>
                `;
                contenedor.appendChild(wrapper);
            });
        }
    </script>
</body>
</html>