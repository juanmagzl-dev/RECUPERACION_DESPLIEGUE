/**
 * Script principal del frontend
 * Proyecto de Recuperación - Despliegue
 */

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    initializeNavigation();
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
 * Prueba la conexión con el backend
 */
async function testBackendConnection() {
    try {
        const response = await fetch('../backend/index.php');
        const data = await response.json();
        
        console.log('Conexión con backend exitosa:', data);
        
        // Mostrar estado en la página
        const statusDiv = document.createElement('div');
        statusDiv.innerHTML = `
            <p><strong>Estado del Backend:</strong> ${data.status}</p>
            <p><strong>Mensaje:</strong> ${data.message}</p>
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
        console.error('Error conectando con backend:', error);
        
        // Mostrar error en la página
        const errorDiv = document.createElement('div');
        errorDiv.innerHTML = `
            <p><strong>Error de conexión:</strong> No se pudo conectar con el backend</p>
            <p><strong>Detalles:</strong> ${error.message}</p>
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

/**
 * Funciones placeholder para funcionalidades futuras
 */
const MapGenerator = {
    generate: function() {
        console.log('Generar mapa - Funcionalidad pendiente');
    }
};

const SaveLoad = {
    save: function() {
        console.log('Guardar - Funcionalidad pendiente');
    },
    load: function() {
        console.log('Cargar - Funcionalidad pendiente');
    }
}; 