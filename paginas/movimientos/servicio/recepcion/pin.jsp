<div class="phone-container">
        <div class="screen">
            <h2>Ingresa tu PIN</h2>
            <input type="text" id="pin-display" class="pin-display" maxlength="4" readonly>
            <div class="pin-keypad">
                <!-- Botones del teclado numérico -->
                <button class="pin-btn" data-num="1">1</button>
                <button class="pin-btn" data-num="2">2</button>
                <button class="pin-btn" data-num="3">3</button>
                <button class="pin-btn" data-num="4">4</button>
                <button class="pin-btn" data-num="5">5</button>
                <button class="pin-btn" data-num="6">6</button>
                <button class="pin-btn" data-num="7">7</button>
                <button class="pin-btn" data-num="8">8</button>
                <button class="pin-btn" data-num="9">9</button>
                <button class="pin-btn reset" onclick="resetPIN()">Borrar</button>
                <button class="pin-btn" data-num="0">0</button>
                <button class="pin-btn enter" onclick="savePIN()">Entrar</button>
            </div>
        </div>
    </div>

<style>
    /* Estilo general del "teléfono" */
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

.pin-display {
    width: 80%;
    padding: 10px;
    font-size: 24px;
    text-align: center;
    border: 2px solid #333;
    border-radius: 5px;
    margin-bottom: 20px;
    background-color: #f4f4f4;
}

.pin-keypad {
    display: flex;
    width: 90%;
    flex-wrap: wrap;
    justify-content: space-around;
    align-content: space-around;
    margin: 10px;
}

.pin-btn {
    width: 32%;
    padding: 15px;
    font-size: 18px;
    border: none;
    background-color: #333;
    color: white;
    margin: 1px;
}

</style>

<script>
let pin = '';

// Seleccionar botones del teclado
const buttons = document.querySelectorAll('.pin-btn');
const pinDisplay = document.getElementById('pin-display');

// Añadir evento a cada botón numérico
buttons.forEach(button => {
    button.addEventListener('click', function () {
        const num = this.getAttribute('data-num');
        if (num && pin.length < 6) {
            pin += num;
            pinDisplay.value = pin;
        }
    });
});

// Función para borrar el PIN
function resetPIN() {
    pin = '';
    pinDisplay.value = '';
}

// Función para guardar el PIN
function savePIN() {
    if (pin.length === 6) {
        alert('PIN guardado: ' + pin);
        // Aquí puedes almacenar el PIN en localStorage, enviarlo al servidor, etc.
        localStorage.setItem('savedPIN', pin); // Ejemplo: guardar en localStorage
        resetPIN();
    } else {
        alert('El PIN debe tener 6 dígitos.');
    }
}


</script>