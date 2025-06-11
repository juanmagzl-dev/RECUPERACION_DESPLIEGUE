<?php
/**
 * Tests para la clase MinesweeperMap
 * Proyecto de Recuperación - Despliegue
 * Cobertura completa de funcionalidades
 */

require_once __DIR__ . '/../src/backend/MinesweeperMap.php';
use PHPUnit\Framework\TestCase;

class MinesweeperMapTest extends TestCase
{
    private $tempFiles = [];

    /**
     * Cleanup temporal files after each test
     */
    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $this->tempFiles = [];
    }

    /**
     * Helper para crear archivos temporales
     */
    private function createTempFile($content, $extension = 'json')
    {
        $filename = tempnam(sys_get_temp_dir(), 'minesweeper_test_') . '.' . $extension;
        file_put_contents($filename, $content);
        $this->tempFiles[] = $filename;
        return $filename;
    }

    /**
     * Test de creación básica de mapa
     */
    public function testBasicMapCreation()
    {
        $map = new MinesweeperMap(10, 10, 15);
        $info = $map->getMapInfo();
        
        $this->assertEquals(10, $info['rows']);
        $this->assertEquals(10, $info['cols']);
        $this->assertEquals(15, $info['mines']);
        $this->assertFalse($info['generated']);
    }

    /**
     * Test de validación de parámetros inválidos - filas
     */
    public function testInvalidRowsParameter()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Las filas y columnas deben ser números positivos");
        new MinesweeperMap(0, 10, 5);
    }

    /**
     * Test de validación de parámetros inválidos - columnas
     */
    public function testInvalidColsParameter()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Las filas y columnas deben ser números positivos");
        new MinesweeperMap(10, -1, 5);
    }

    /**
     * Test de validación de minas excesivas
     */
    public function testTooManyMines()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("El número de minas debe ser menor que el total de casillas");
        new MinesweeperMap(3, 3, 10); // 10 minas en 9 casillas
    }

    /**
     * Test de validación de minas negativas
     */
    public function testNegativeMines()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("El número de minas debe ser menor que el total de casillas");
        new MinesweeperMap(5, 5, -1);
    }

    /**
     * Test de generación de mapa básico
     */
    public function testMapGeneration()
    {
        $map = new MinesweeperMap(5, 5, 5);
        $map->generateMap();
        
        $info = $map->getMapInfo();
        $this->assertTrue($info['generated']);
        
        $mapData = $map->getMap();
        $this->assertIsArray($mapData);
        $this->assertCount(5, $mapData);
        
        // Verificar que cada fila tiene 5 columnas
        foreach ($mapData as $row) {
            $this->assertCount(5, $row);
        }
    }

    /**
     * Test de conteo correcto de minas
     */
    public function testMineCountAfterGeneration()
    {
        $map = new MinesweeperMap(5, 5, 5);
        $map->generateMap();
        
        $mapData = $map->getMap();
        
        // Contar minas
        $mineCount = 0;
        foreach ($mapData as $row) {
            foreach ($row as $cell) {
                if ($cell === -1) {
                    $mineCount++;
                }
            }
        }
        $this->assertEquals(5, $mineCount);
    }

    /**
     * Test de números adyacentes correctos
     */
    public function testAdjacentNumbers()
    {
        $map = new MinesweeperMap(3, 3, 1);
        $map->generateMap();
        
        $mapData = $map->getMap();
        
        // Verificar que todos los números son válidos (0-8)
        foreach ($mapData as $row) {
            foreach ($row as $cell) {
                $this->assertTrue($cell >= -1 && $cell <= 8);
            }
        }
    }

    /**
     * Test de niveles de dificultad - Fácil
     */
    public function testEasyDifficulty()
    {
        $easy = MinesweeperMap::fromDifficulty('easy');
        $easyInfo = $easy->getMapInfo();
        
        $this->assertEquals(9, $easyInfo['rows']);
        $this->assertEquals(9, $easyInfo['cols']);
        $this->assertEquals(10, $easyInfo['mines']);
        $this->assertFalse($easyInfo['generated']);
    }

    /**
     * Test de niveles de dificultad - Medio
     */
    public function testMediumDifficulty()
    {
        $medium = MinesweeperMap::fromDifficulty('medium');
        $mediumInfo = $medium->getMapInfo();
        
        $this->assertEquals(16, $mediumInfo['rows']);
        $this->assertEquals(16, $mediumInfo['cols']);
        $this->assertEquals(40, $mediumInfo['mines']);
        $this->assertFalse($mediumInfo['generated']);
    }

    /**
     * Test de niveles de dificultad - Experto
     */
    public function testExpertDifficulty()
    {
        $expert = MinesweeperMap::fromDifficulty('expert');
        $expertInfo = $expert->getMapInfo();
        
        $this->assertEquals(30, $expertInfo['rows']);
        $this->assertEquals(16, $expertInfo['cols']);
        $this->assertEquals(99, $expertInfo['mines']);
        $this->assertFalse($expertInfo['generated']);
    }

    /**
     * Test de dificultad inválida
     */
    public function testInvalidDifficulty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Nivel de dificultad no válido: invalid_level");
        MinesweeperMap::fromDifficulty('invalid_level');
    }

    /**
     * Test de exportación JSON
     */
    public function testJsonExport()
    {
        $map = new MinesweeperMap(3, 3, 2);
        $map->generateMap();
        
        $json = $map->toJson();
        $this->assertIsString($json);
        
        $data = json_decode($json, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('rows', $data);
        $this->assertArrayHasKey('cols', $data);
        $this->assertArrayHasKey('mines', $data);
        $this->assertArrayHasKey('map', $data);
        $this->assertArrayHasKey('generated_at', $data);
        
        $this->assertEquals(3, $data['rows']);
        $this->assertEquals(3, $data['cols']);
        $this->assertEquals(2, $data['mines']);
    }

    /**
     * Test de importación JSON
     */
    public function testJsonImport()
    {
        $originalMap = new MinesweeperMap(3, 3, 2);
        $originalMap->generateMap();
        
        $json = $originalMap->toJson();
        $importedMap = MinesweeperMap::fromJson($json);
        
        $this->assertEquals($originalMap->getMapInfo(), $importedMap->getMapInfo());
        $this->assertEquals($originalMap->getMap(), $importedMap->getMap());
    }

    /**
     * Test de exportación XML
     */
    public function testXmlExport()
    {
        $map = new MinesweeperMap(3, 3, 2);
        $map->generateMap();
        
        $xml = $map->toXml();
        $this->assertIsString($xml);
        $this->assertStringContainsString('<?xml', $xml);
        $this->assertStringContainsString('<minesweeper_map>', $xml);
        $this->assertStringContainsString('<rows>3</rows>', $xml);
        $this->assertStringContainsString('<cols>3</cols>', $xml);
        $this->assertStringContainsString('<mines>2</mines>', $xml);
    }

    /**
     * Test de importación XML
     */
    public function testXmlImport()
    {
        $originalMap = new MinesweeperMap(3, 3, 2);
        $originalMap->generateMap();
        
        $xml = $originalMap->toXml();
        $importedMap = MinesweeperMap::fromXml($xml);
        
        $this->assertEquals($originalMap->getMapInfo(), $importedMap->getMapInfo());
        $this->assertEquals($originalMap->getMap(), $importedMap->getMap());
    }

    /**
     * Test de JSON inválido
     */
    public function testInvalidJsonImport()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("JSON inválido");
        MinesweeperMap::fromJson('invalid json {');
    }

    /**
     * Test de JSON con formato inválido
     */
    public function testInvalidJsonFormat()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Formato JSON inválido para MinesweeperMap");
        MinesweeperMap::fromJson('{"invalid": "format"}');
    }

    /**
     * Test de XML inválido
     */
         public function testInvalidXmlImport()
     {
         // Suprimir warnings de simplexml para este test específico
         $originalLevel = error_reporting(E_ALL & ~E_WARNING);
         
         try {
             $this->expectException(InvalidArgumentException::class);
             $this->expectExceptionMessage("XML inválido");
             MinesweeperMap::fromXml('invalid xml');
         } finally {
             error_reporting($originalLevel);
         }
     }

    /**
     * Test de XML con formato inválido
     */
    public function testInvalidXmlFormat()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Formato XML inválido para MinesweeperMap");
        MinesweeperMap::fromXml('<?xml version="1.0"?><invalid></invalid>');
    }

    /**
     * Test de guardado en archivo JSON
     */
    public function testSaveToFileJson()
    {
        $map = new MinesweeperMap(3, 3, 2);
        $map->generateMap();
        
        $filename = tempnam(sys_get_temp_dir(), 'minesweeper_test_') . '.json';
        $this->tempFiles[] = $filename;
        
        $result = $map->saveToFile($filename, 'json');
        $this->assertTrue($result);
        $this->assertFileExists($filename);
        
        $content = file_get_contents($filename);
        $data = json_decode($content, true);
        $this->assertIsArray($data);
        $this->assertEquals(3, $data['rows']);
    }

    /**
     * Test de guardado en archivo XML
     */
    public function testSaveToFileXml()
    {
        $map = new MinesweeperMap(3, 3, 2);
        $map->generateMap();
        
        $filename = tempnam(sys_get_temp_dir(), 'minesweeper_test_') . '.xml';
        $this->tempFiles[] = $filename;
        
        $result = $map->saveToFile($filename, 'xml');
        $this->assertTrue($result);
        $this->assertFileExists($filename);
        
        $content = file_get_contents($filename);
        $this->assertStringContainsString('<?xml', $content);
    }

    /**
     * Test de carga desde archivo JSON
     */
    public function testLoadFromFileJson()
    {
        $originalMap = new MinesweeperMap(3, 3, 2);
        $originalMap->generateMap();
        
        $filename = tempnam(sys_get_temp_dir(), 'minesweeper_test_') . '.json';
        $this->tempFiles[] = $filename;
        
        $originalMap->saveToFile($filename, 'json');
        $loadedMap = MinesweeperMap::loadFromFile($filename, 'json');
        
        $this->assertEquals($originalMap->getMapInfo(), $loadedMap->getMapInfo());
        $this->assertEquals($originalMap->getMap(), $loadedMap->getMap());
    }

    /**
     * Test de carga desde archivo XML
     */
    public function testLoadFromFileXml()
    {
        $originalMap = new MinesweeperMap(3, 3, 2);
        $originalMap->generateMap();
        
        $filename = tempnam(sys_get_temp_dir(), 'minesweeper_test_') . '.xml';
        $this->tempFiles[] = $filename;
        
        $originalMap->saveToFile($filename, 'xml');
        $loadedMap = MinesweeperMap::loadFromFile($filename, 'xml');
        
        $this->assertEquals($originalMap->getMapInfo(), $loadedMap->getMapInfo());
        $this->assertEquals($originalMap->getMap(), $loadedMap->getMap());
    }

    /**
     * Test de archivo inexistente
     */
    public function testLoadNonExistentFile()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("El archivo no existe:");
        MinesweeperMap::loadFromFile('/non/existent/file.json');
    }

    /**
     * Test de formato no soportado en save
     */
    public function testUnsupportedSaveFormat()
    {
        $map = new MinesweeperMap(3, 3, 2);
        $map->generateMap();
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Formato no soportado: txt");
        $map->saveToFile('test.txt', 'txt');
    }

    /**
     * Test de formato no soportado en load
     */
    public function testUnsupportedLoadFormat()
    {
        $filename = $this->createTempFile('dummy content', 'txt');
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Formato no soportado: txt");
        MinesweeperMap::loadFromFile($filename, 'txt');
    }

    /**
     * Test de acceso a mapa no generado
     */
    public function testAccessUngeneratedMap()
    {
        $map = new MinesweeperMap(3, 3, 2);
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("El mapa no ha sido generado");
        $map->getMap();
    }

    /**
     * Test de exportación JSON sin generar mapa
     */
    public function testJsonExportUngeneratedMap()
    {
        $map = new MinesweeperMap(3, 3, 2);
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("El mapa no ha sido generado");
        $map->toJson();
    }

    /**
     * Test de exportación XML sin generar mapa
     */
    public function testXmlExportUngeneratedMap()
    {
        $map = new MinesweeperMap(3, 3, 2);
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("El mapa no ha sido generado");
        $map->toXml();
    }

    /**
     * Test de guardado sin generar mapa
     */
    public function testSaveUngeneratedMap()
    {
        $map = new MinesweeperMap(3, 3, 2);
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("El mapa no ha sido generado");
        $map->saveToFile('test.json', 'json');
    }

    /**
     * Test de caso extremo: mapa 1x1 con 0 minas
     */
    public function testMinimumMap()
    {
        $map = new MinesweeperMap(1, 1, 0);
        $map->generateMap();
        
        $mapData = $map->getMap();
        $this->assertEquals(0, $mapData[0][0]);
    }

    /**
     * Test de caso extremo: mapa grande
     */
    public function testLargeMap()
    {
        $map = new MinesweeperMap(20, 20, 50);
        $map->generateMap();
        
        $mapData = $map->getMap();
        $this->assertCount(20, $mapData);
        
        // Contar minas
        $mineCount = 0;
        foreach ($mapData as $row) {
            foreach ($row as $cell) {
                if ($cell === -1) {
                    $mineCount++;
                }
            }
        }
        $this->assertEquals(50, $mineCount);
    }

    /**
     * Test de consistencia: múltiples generaciones
     */
    public function testMultipleGenerations()
    {
        $map = new MinesweeperMap(5, 5, 5);
        
        for ($i = 0; $i < 10; $i++) {
            $map->generateMap();
            $mapData = $map->getMap();
            
            // Verificar estructura
            $this->assertCount(5, $mapData);
            
            // Contar minas
            $mineCount = 0;
            foreach ($mapData as $row) {
                foreach ($row as $cell) {
                    if ($cell === -1) {
                        $mineCount++;
                    }
                }
            }
            $this->assertEquals(5, $mineCount);
        }
    }

    /**
     * Test de integridad de datos JSON
     */
    public function testJsonDataIntegrity()
    {
        $map = new MinesweeperMap(4, 4, 3);
        $map->generateMap();
        
        $json = $map->toJson();
        $data = json_decode($json, true);
        
        // Verificar que el mapa tiene la estructura correcta
        $this->assertCount(4, $data['map']);
        foreach ($data['map'] as $row) {
            $this->assertCount(4, $row);
        }
        
        // Verificar que la fecha está presente
        $this->assertNotEmpty($data['generated_at']);
        $this->assertIsString($data['generated_at']);
    }

    /**
     * Test de integridad de datos XML
     */
    public function testXmlDataIntegrity()
    {
        $map = new MinesweeperMap(4, 4, 3);
        $map->generateMap();
        
        $xml = $map->toXml();
        $xmlObj = simplexml_load_string($xml);
        
        $this->assertNotFalse($xmlObj);
        $this->assertEquals('4', (string)$xmlObj->rows);
        $this->assertEquals('4', (string)$xmlObj->cols);
        $this->assertEquals('3', (string)$xmlObj->mines);
        $this->assertNotEmpty((string)$xmlObj->generated_at);
        
        // Verificar estructura del mapa
        $this->assertCount(4, $xmlObj->map->row);
    }
}
?>
