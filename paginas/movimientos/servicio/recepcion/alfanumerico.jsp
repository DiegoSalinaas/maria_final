
 <div class="phone-container">
        <div class="screen">
            <h2>Ingresa Datos Alfanuméricos</h2>
            <input type="text" id="input-display" class="input-display" maxlength="16" >
            <div class="qwerty-keyboard">
                <!-- Primera fila del teclado QWERTY -->
                <button class="key-btn" data-char="Q">Q</button>
                <button class="key-btn" data-char="W">W</button>
                <button class="key-btn" data-char="E">E</button>
                <button class="key-btn" data-char="R">R</button>
                <button class="key-btn" data-char="T">T</button>
                <button class="key-btn" data-char="Y">Y</button>
                <button class="key-btn" data-char="U">U</button>
                <button class="key-btn" data-char="I">I</button>
                <button class="key-btn" data-char="O">O</button>
                <button class="key-btn" data-char="P">P</button>

                <!-- Segunda fila del teclado QWERTY -->
                <button class="key-btn" data-char="A">A</button>
                <button class="key-btn" data-char="S">S</button>
                <button class="key-btn" data-char="D">D</button>
                <button class="key-btn" data-char="F">F</button>
                <button class="key-btn" data-char="G">G</button>
                <button class="key-btn" data-char="H">H</button>
                <button class="key-btn" data-char="J">J</button>
                <button class="key-btn" data-char="K">K</button>
                <button class="key-btn" data-char="L">L</button>

                <!-- Tercera fila del teclado QWERTY -->
                <button class="key-btn" data-char="Z">Z</button>
                <button class="key-btn" data-char="X">X</button>
                <button class="key-btn" data-char="C">C</button>
                <button class="key-btn" data-char="V">V</button>
                <button class="key-btn" data-char="B">B</button>
                <button class="key-btn" data-char="N">N</button>
                <button class="key-btn" data-char="M">M</button>

                <!-- Fila numérica -->
                <button class="key-btn" data-char="1">1</button>
                <button class="key-btn" data-char="2">2</button>
                <button class="key-btn" data-char="3">3</button>
                <button class="key-btn" data-char="4">4</button>
                <button class="key-btn" data-char="5">5</button>
                <button class="key-btn" data-char="6">6</button>
                <button class="key-btn" data-char="7">7</button>
                <button class="key-btn" data-char="8">8</button>
                <button class="key-btn" data-char="9">9</button>
                <button class="key-btn" data-char="0">0</button>

                <!-- Botones de control -->
                <button class="key-btn reset" style="width: 20%" onclick="resetInput()">Borrar</button>
                <button class="key-btn enter" style="width: 20%" onclick="saveInput()">Entrar</button>
            </div>
        </div>
    </div>
<style>
/* Estilo general del "teléfono" */


.phone-container {
    width: 350px;
    height: 600px;
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
    justify-content: center;
    align-items: center;
}

h2 {
    font-size: 18px;
    color: #333;
}

.input-display {
    width: 80%;
    padding: 10px;
    font-size: 24px;
    text-align: center;
    border: 2px solid #333;
    border-radius: 5px;
    margin-bottom: 20px;
    background-color: #f4f4f4;
}

.qwerty-keyboard {
    display: flex;
    grid-template-columns: repeat(10, 0fr);
    flex-wrap: wrap;
    width: 90%;
    box-sizing: border-box;
}

.key-btn {
    width: 10%;
    font-size: 18px;
    border: none;
    background-color: #333;
    color: white;
    border-radius: 10px;
    cursor: pointer;
    text-align: center;
    outline: none;
}

.key-btn.reset {
    background-color: #f44336;
}

.key-btn.enter {
    background-color: #4CAF50;
}

.key-btn:hover {
    background-color: #555;
}

</style>
<script>
   let inputData = '';

// Seleccionar los botones del teclado
const buttons = document.querySelectorAll('.key-btn');
const inputDisplay = document.getElementById('input-display');

// Añadir eventos a cada botón numérico y alfabético
buttons.forEach(button => {
    button.addEventListener('click', function () {
        const char = this.getAttribute('data-char');
        if (char && inputData.length < 16) {
            inputData += char;
            inputDisplay.value = inputData;
        }
    });
});

// Función para borrar el contenido del input
function resetInput() {
    inputData = '';
    inputDisplay.value = '';
}

// Función para guardar el contenido del input
function saveInput() {
    if (inputData.length > 0) {
        alert('Datos guardados: ' + inputData);
        // Aquí puedes almacenar los datos alfanuméricos en localStorage, enviarlos al servidor, etc.
        localStorage.setItem('savedInput', inputData); // Ejemplo: guardar en localStorage
        resetInput();
    } else {
        alert('Por favor, ingresa datos alfanuméricos.');
    }
}

</script>