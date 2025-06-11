/**
 * Script principal del frontend
 * Proyecto de Recuperación - Despliegue - Generador de Mapas Buscaminas
 */

// Variables globales
let currentMap = null;
let currentMapInfo = null;
let selectedDifficulty = null;

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    initializeNavigation();
    initializeMapGenerator();
    testBackendConnection();
});

/**
 * Inicializa la navegación entre secciones
 */
function initializeNavigation() {
    const navLinks = document.querySelectorAll('nav a');
    const sections = document.querySelectorAll('main section');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Ocultar todas las secciones
            sections.forEach(section => {
                section.style.display = 'none';
            });
            
            // Mostrar la sección seleccionada
            const targetId = this.getAttribute('href').substring(1);
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.style.display = 'block';
            }
            
            // Actualizar estado activo del enlace
            navLinks.forEach(navLink => {
                navLink.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
}

/**
 * Inicializa el generador de mapas
 */
function initializeMapGenerator() {
    // Botones de dificultad
    const difficultyButtons = document.querySelectorAll('.difficulty-btn');
    difficultyButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            selectDifficulty(this.dataset.difficulty);
        });
    });

    // Botón generar
    document.getElementById('generate-btn').addEventListener('click', generateMap);

    // Botón guardar
    document.getElementById('save-btn').addEventListener('click', saveMap);

    // Input de carga de archivo
    document.getElementById('load-file').addEventListener('change', loadMap);

    // Validación de inputs personalizados
    const customInputs = ['custom-rows', 'custom-cols', 'custom-mines'];
    customInputs.forEach(id => {
        document.getElementById(id).addEventListener('input', validateCustomInputs);
    });
}

/**
 * Selecciona nivel de dificultad
 */
function selectDifficulty(difficulty) {
    selectedDifficulty = difficulty;
    
    // Actualizar botones
    document.querySelectorAll('.difficulty-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-difficulty="${difficulty}"]`).classList.add('active');

    // Mostrar/ocultar configuración personalizada
    const customConfig = document.getElementById('custom-config');
    if (difficulty === 'custom') {
        customConfig.style.display = 'block';
    } else {
        customConfig.style.display = 'none';
    }

    console.log('Dificultad seleccionada:', difficulty);
}

/**
 * Valida inputs personalizados
 */
function validateCustomInputs() {
    const rows = parseInt(document.getElementById('custom-rows').value) || 0;
    const cols = parseInt(document.getElementById('custom-cols').value) || 0;
    const mines = parseInt(document.getElementById('custom-mines').value) || 0;

    const maxMines = Math.max(1, (rows * cols) - 1);
    const minesInput = document.getElementById('custom-mines');
    
    minesInput.max = maxMines;
    if (mines >= maxMines) {
        minesInput.value = maxMines;
    }
}

/**
 * Genera un mapa
 */
async function generateMap() {
    try {
        showLoading(true);
        
        let response;
        if (selectedDifficulty === 'custom') {
            // Generar mapa personalizado
            const rows = parseInt(document.getElementById('custom-rows').value);
            const cols = parseInt(document.getElementById('custom-cols').value);
            const mines = parseInt(document.getElementById('custom-mines').value);

            response = await fetch('src/backend/api.php?action=generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ rows, cols, mines })
            });
        } else if (selectedDifficulty) {
            // Generar mapa por dificultad
            response = await fetch('src/backend/api.php?action=generate_difficulty', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ difficulty: selectedDifficulty })
            });
        } else {
            throw new Error('Por favor selecciona una dificultad primero');
        }

        const data = await response.json();
        
        if (data.success) {
            currentMap = data.data.map;
            currentMapInfo = data.data.info;
            displayMap(currentMap, currentMapInfo);
            document.getElementById('save-btn').disabled = false;
            showMessage('Mapa generado exitosamente', 'success');
        } else {
            throw new Error(data.message || 'Error generando el mapa');
        }

    } catch (error) {
        console.error('Error:', error);
        showMessage('Error: ' + error.message, 'error');
    } finally {
        showLoading(false);
    }
}

/**
 * Muestra el mapa generado
 */
function displayMap(map, info) {
    const mapGrid = document.getElementById('map-grid');
    const mapInfo = document.getElementById('map-info');

    // Configurar CSS Grid
    mapGrid.style.gridTemplateColumns = `repeat(${info.cols}, 1fr)`;
    mapGrid.innerHTML = '';

    // Crear celdas
    for (let row = 0; row < info.rows; row++) {
        for (let col = 0; col < info.cols; col++) {
            const cell = document.createElement('div');
            cell.className = 'map-cell';
            cell.dataset.row = row;
            cell.dataset.col = col;

            const value = map[row][col];
            if (value === -1) {
                // Mina
                cell.textContent = '💣';
                cell.classList.add('mine');
            } else if (value > 0) {
                // Número
                cell.textContent = value;
                cell.classList.add('number', `number-${value}`);
            }
            // Si es 0, se deja vacío

            mapGrid.appendChild(cell);
        }
    }

    // Mostrar información
    document.getElementById('info-dimensions').textContent = `${info.rows} x ${info.cols}`;
    document.getElementById('info-mines').textContent = info.mines;
    document.getElementById('info-difficulty').textContent = selectedDifficulty === 'custom' ? 'Personalizado' : getDifficultyName(selectedDifficulty);
    
    mapInfo.style.display = 'block';
}

/**
 * Obtiene el nombre de la dificultad
 */
function getDifficultyName(difficulty) {
    const names = {
        'easy': 'Fácil',
        'medium': 'Medio',
        'expert': 'Experto'
    };
    return names[difficulty] || difficulty;
}

/**
 * Guarda el mapa actual
 */
async function saveMap() {
    if (!currentMap || !currentMapInfo) {
        showMessage('No hay mapa generado para guardar', 'error');
        return;
    }

    try {
        showLoading(true);

        const response = await fetch('src/backend/api.php?action=save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                mapData: currentMapInfo,
                format: 'json'
            })
        });

        const data = await response.json();
        
        if (data.success) {
            // Descargar archivo
            downloadFile(data.data.content, data.data.filename, data.data.mimeType);
            showMessage('Mapa guardado exitosamente', 'success');
        } else {
            throw new Error(data.message || 'Error guardando el mapa');
        }

    } catch (error) {
        console.error('Error:', error);
        showMessage('Error: ' + error.message, 'error');
    } finally {
        showLoading(false);
    }
}

/**
 * Carga un mapa desde archivo
 */
async function loadMap(event) {
    const file = event.target.files[0];
    if (!file) return;

    try {
        showLoading(true);

        const content = await readFileContent(file);
        const format = file.name.endsWith('.xml') ? 'xml' : 'json';

        const response = await fetch('src/backend/api.php?action=load', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                content: content,
                format: format
            })
        });

        const data = await response.json();
        
        if (data.success) {
            currentMap = data.data.map;
            currentMapInfo = data.data.info;
            displayMap(currentMap, currentMapInfo);
            document.getElementById('save-btn').disabled = false;
            showMessage('Mapa cargado exitosamente', 'success');
            
            // Limpiar el input
            event.target.value = '';
        } else {
            throw new Error(data.message || 'Error cargando el mapa');
        }

    } catch (error) {
        console.error('Error:', error);
        showMessage('Error: ' + error.message, 'error');
        event.target.value = '';
    } finally {
        showLoading(false);
    }
}

/**
 * Lee el contenido de un archivo
 */
function readFileContent(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = e => resolve(e.target.result);
        reader.onerror = e => reject(new Error('Error leyendo archivo'));
        reader.readAsText(file);
    });
}

/**
 * Descarga un archivo
 */
function downloadFile(content, filename, mimeType) {
    const blob = new Blob([content], { type: mimeType });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

/**
 * Muestra un mensaje al usuario
 */
function showMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.textContent = message;
    messageDiv.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 6px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        background: ${type === 'success' ? '#28a745' : '#dc3545'};
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    `;
    
    document.body.appendChild(messageDiv);
    
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.parentNode.removeChild(messageDiv);
        }
    }, 3000);
}

/**
 * Muestra/oculta indicador de carga
 */
function showLoading(show) {
    const generateBtn = document.getElementById('generate-btn');
    const saveBtn = document.getElementById('save-btn');
    
    if (show) {
        generateBtn.disabled = true;
        generateBtn.textContent = 'Generando...';
        saveBtn.disabled = true;
    } else {
        generateBtn.disabled = false;
        generateBtn.textContent = 'Generar Mapa';
        if (currentMap) {
            saveBtn.disabled = false;
        }
    }
}

/**
 * Prueba la conexión con el backend
 */
async function testBackendConnection() {
    try {
        const response = await fetch('src/backend/api.php?action=info');
        const data = await response.json();
        
        console.log('Conexión con API exitosa:', data);
        
        // Mostrar estado en la página
        const statusDiv = document.createElement('div');
        statusDiv.innerHTML = `
            <p><strong>Estado de la API:</strong> ${data.success ? 'Conectado' : 'Error'}</p>
            <p><strong>Mensaje:</strong> ${data.message}</p>
            <p><strong>Versión:</strong> ${data.version || 'N/A'}</p>
            <p><strong>Timestamp:</strong> ${data.timestamp}</p>
        `;
        statusDiv.style.cssText = `
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 1rem;
            border-radius: 4px;
            margin: 1rem 0;
        `;
        
        const homeSection = document.getElementById('home');
        homeSection.appendChild(statusDiv);
        
    } catch (error) {
        console.error('Error conectando con API:', error);
        
        // Mostrar error en la página
        const errorDiv = document.createElement('div');
        errorDiv.innerHTML = `
            <p><strong>Error de conexión:</strong> No se pudo conectar con la API</p>
            <p><strong>Detalles:</strong> ${error.message}</p>
            <p><strong>Nota:</strong> Asegúrate de que el servidor PHP esté ejecutándose</p>
        `;
        errorDiv.style.cssText = `
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 1rem;
            border-radius: 4px;
            margin: 1rem 0;
        `;
        
        const homeSection = document.getElementById('home');
        homeSection.appendChild(errorDiv);
    }
} 