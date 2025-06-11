<?php
/**
 * Prueba de ejemplo para PHPUnit
 * Proyecto de Recuperación - Despliegue
 */

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test básico de ejemplo
     */
    public function testBasicExample()
    {
        $this->assertTrue(true, 'Este test siempre debe pasar');
    }

    /**
     * Test de configuración inicial
     */
    public function testInitialSetup()
    {
        $this->assertDirectoryExists(__DIR__ . '/../src/backend', 'El directorio backend debe existir');
        $this->assertDirectoryExists(__DIR__ . '/../src/frontend', 'El directorio frontend debe existir');
        $this->assertFileExists(__DIR__ . '/../src/backend/index.php', 'El archivo index.php del backend debe existir');
        $this->assertFileExists(__DIR__ . '/../src/frontend/index.html', 'El archivo index.html del frontend debe existir');
    }

    /**
     * Test de estructura de proyecto
     */
    public function testProjectStructure()
    {
        $expectedFiles = [
            'README.md',
            '.gitignore',
            'src/backend/index.php',
            'src/frontend/index.html',
            'src/frontend/styles.css',
            'src/frontend/script.js'
        ];

        foreach ($expectedFiles as $file) {
            $this->assertFileExists(__DIR__ . '/../' . $file, "El archivo {$file} debe existir");
        }
    }
}
?> 