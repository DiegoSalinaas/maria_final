
        <div class="phone-container">
            <input type="text" id="pass" value="" hidden>
            <div class="screen">
                <h2>Ingresa el Patrón</h2>
                <div class="pattern-grid">
                    <!-- Creación de los puntos del patrón -->
                    <div class="pattern-node" data-node="1"></div>
                    <div class="pattern-node" data-node="2"></div>
                    <div class="pattern-node" data-node="3"></div>
                    <div class="pattern-node" data-node="4"></div>
                    <div class="pattern-node" data-node="5"></div>
                    <div class="pattern-node" data-node="6"></div>
                    <div class="pattern-node" data-node="7"></div>
                    <div class="pattern-node" data-node="8"></div>
                    <div class="pattern-node" data-node="9"></div>
                </div>
                <button class="reset-btn" onclick="resetPattern()">Restablecer</button>
            </div>
        </div>

    <style>
        /* Estilo del "teléfono" */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }

        .phone-container {
            width: 300px;
            height: 500px;
            background-color: #333;
            border-radius: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .screen {
            width: 100%;
            height: 100%;
            background-color: #fff;
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            align-items: center;
        }

        h2 {
            font-size: 18px;
            color: #333;
        }

        /* Estilo de la cuadrícula del patrón */
        .pattern-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-gap: 20px;
            width: 80%;
        }

        .pattern-node {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid #333;
            background-color: #fff;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }

        /* Estado del nodo activado */
        .pattern-node.active {
            background-color: #4CAF50;
        }

        /* Botón para restablecer el patrón */
        .reset-btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .reset-btn:hover {
            background-color: #45a049;
        }

    </style>
    <script>
                    // Variables globales
                    let pattern = [];
                    let isDrawing = false;

// Añadir eventos a cada nodo
                    const nodes = document.querySelectorAll('.pattern-node');
                    nodes.forEach(node => {
                        node.addEventListener('mousedown', startDrawing);
                        node.addEventListener('mouseover', trackPattern);
                        node.addEventListener('mouseup', endDrawing);
                    });

// Función para iniciar el dibujo
                    function startDrawing(event) {
                        isDrawing = true;
                        const node = event.target;
                        node.classList.add('active');
                        pattern.push(node.dataset.node);
                    }

// Función para seguir el patrón cuando se mueve sobre otros nodos
                    function trackPattern(event) {
                        if (isDrawing) {
                            const node = event.target;
                            if (!node.classList.contains('active')) {
                                node.classList.add('active');
                                pattern.push(node.dataset.node);
                            }
                        }
                    }

// Función para finalizar el dibujo
                    function endDrawing() {
                        isDrawing = false;
                        console.log("Patrón ingresado:", pattern);
                        
                        //alert("Patrón ingresado: " + pattern.join('-'));
                        $("#pass").val(pattern.join('-'));
                    }

// Función para restablecer el patrón
                    function resetPattern() {
                        pattern = [];
                        nodes.forEach(node => node.classList.remove('active'));
                    }

    </script>


