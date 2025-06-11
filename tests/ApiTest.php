<?php
/**
 * Tests para la API REST
 * Proyecto de Recuperación - Despliegue
 */

require_once __DIR__ . '/../src/backend/MinesweeperMap.php';
require_once __DIR__ . '/../src/backend/api.php';
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    /**
     * Test de función sendJsonResponse
     */
    public function testSendJsonResponseFunction()
    {
        // Como la función hace exit(), necesitamos usar output buffering
        // y capturar la salida antes del exit
        $this->assertTrue(function_exists('sendJsonResponse'));
    }

    /**
     * Test de función sendError
     */
    public function testSendErrorFunction()
    {
        $this->assertTrue(function_exists('sendError'));
    }

    /**
     * Test de función handlePostRequest
     */
    public function testHandlePostRequestFunction()
    {
        $this->assertTrue(function_exists('handlePostRequest'));
    }

    /**
     * Test de función handleGetRequest
     */
    public function testHandleGetRequestFunction()
    {
        $this->assertTrue(function_exists('handleGetRequest'));
    }

    /**
     * Test de función generateMap
     */
    public function testGenerateMapFunction()
    {
        $this->assertTrue(function_exists('generateMap'));
    }

    /**
     * Test de función generateMapFromDifficulty
     */
    public function testGenerateMapFromDifficultyFunction()
    {
        $this->assertTrue(function_exists('generateMapFromDifficulty'));
    }

    /**
     * Test de función saveMap
     */
    public function testSaveMapFunction()
    {
        $this->assertTrue(function_exists('saveMap'));
    }

    /**
     * Test de función loadMap
     */
    public function testLoadMapFunction()
    {
        $this->assertTrue(function_exists('loadMap'));
    }

    /**
     * Test de función getDifficulties
     */
    public function testGetDifficultiesFunction()
    {
        $this->assertTrue(function_exists('getDifficulties'));
    }

    /**
     * Test de función getApiInfo
     */
    public function testGetApiInfoFunction()
    {
        $this->assertTrue(function_exists('getApiInfo'));
    }

    /**
     * Test básico de inclusión de archivos
     */
    public function testRequiredFilesExist()
    {
        $this->assertFileExists(__DIR__ . '/../src/backend/MinesweeperMap.php');
        $this->assertFileExists(__DIR__ . '/../src/backend/api.php');
        $this->assertFileExists(__DIR__ . '/../src/backend/index.php');
    }

    /**
     * Test de estructura de archivos frontend
     */
    public function testFrontendFilesExist()
    {
        $this->assertFileExists(__DIR__ . '/../src/frontend/index.html');
        $this->assertFileExists(__DIR__ . '/../src/frontend/styles.css');
        $this->assertFileExists(__DIR__ . '/../src/frontend/script.js');
    }

    /**
     * Test de contenido HTML básico
     */
    public function testHtmlContent()
    {
        $htmlContent = file_get_contents(__DIR__ . '/../src/frontend/index.html');
        
        $this->assertStringContainsString('<!DOCTYPE html>', $htmlContent);
        $this->assertStringContainsString('<title>', $htmlContent);
        $this->assertStringContainsString('Proyecto Recuperación', $htmlContent);
        $this->assertStringContainsString('difficulty-btn', $htmlContent);
        $this->assertStringContainsString('generate-btn', $htmlContent);
    }

    /**
     * Test de contenido CSS básico
     */
    public function testCssContent()
    {
        $cssContent = file_get_contents(__DIR__ . '/../src/frontend/styles.css');
        
        $this->assertStringContainsString('.difficulty-btn', $cssContent);
        $this->assertStringContainsString('.action-btn', $cssContent);
        $this->assertStringContainsString('.map-grid', $cssContent);
        $this->assertStringContainsString('.map-cell', $cssContent);
    }

    /**
     * Test de contenido JavaScript básico
     */
    public function testJavaScriptContent()
    {
        $jsContent = file_get_contents(__DIR__ . '/../src/frontend/script.js');
        
        $this->assertStringContainsString('initializeMapGenerator', $jsContent);
        $this->assertStringContainsString('generateMap', $jsContent);
        $this->assertStringContainsString('saveMap', $jsContent);
        $this->assertStringContainsString('loadMap', $jsContent);
        $this->assertStringContainsString('displayMap', $jsContent);
    }

    /**
     * Test de configuración del proyecto
     */
    public function testProjectConfiguration()
    {
        $this->assertFileExists(__DIR__ . '/../composer.json');
        $this->assertFileExists(__DIR__ . '/../phpunit.xml');
        $this->assertFileExists(__DIR__ . '/../.gitignore');
        $this->assertFileExists(__DIR__ . '/../README.md');
    }

    /**
     * Test de contenido composer.json
     */
    public function testComposerConfiguration()
    {
        $composerContent = file_get_contents(__DIR__ . '/../composer.json');
        $composerData = json_decode($composerContent, true);
        
        $this->assertIsArray($composerData);
        $this->assertArrayHasKey('name', $composerData);
        $this->assertArrayHasKey('require-dev', $composerData);
        $this->assertArrayHasKey('phpunit/phpunit', $composerData['require-dev']);
    }

    /**
     * Test de validación de estructura README
     */
    public function testReadmeStructure()
    {
        $readmeContent = file_get_contents(__DIR__ . '/../README.md');
        
        $this->assertStringContainsString('# Proyecto de Recuperación', $readmeContent);
        $this->assertStringContainsString('## Funcionalidades Implementadas', $readmeContent);
        $this->assertStringContainsString('### Fase 1:', $readmeContent);
        $this->assertStringContainsString('### Fase 2:', $readmeContent);
        $this->assertStringContainsString('### Fase 3:', $readmeContent);
        $this->assertStringContainsString('## Cómo usar el proyecto', $readmeContent);
    }
}
?> 