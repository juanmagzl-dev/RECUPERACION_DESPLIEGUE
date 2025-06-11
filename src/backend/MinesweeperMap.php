<?php
/**
 * Clase MinesweeperMap
 * Genera y maneja mapas de buscaminas
 * Proyecto de Recuperación - Despliegue
 */

class MinesweeperMap
{
    private $rows;
    private $cols;
    private $mines;
    private $map;
    private $generated;

    // Constantes para niveles de dificultad
    const DIFFICULTY_EASY = 'easy';
    const DIFFICULTY_MEDIUM = 'medium';
    const DIFFICULTY_EXPERT = 'expert';
    const DIFFICULTY_CUSTOM = 'custom';

    /**
     * Constructor
     * @param int $rows Número de filas
     * @param int $cols Número de columnas
     * @param int $mines Número de minas
     */
    public function __construct($rows, $cols, $mines)
    {
        $this->rows = (int)$rows;
        $this->cols = (int)$cols;
        $this->mines = (int)$mines;
        $this->map = [];
        $this->generated = false;

        // Validaciones básicas
        if ($this->rows <= 0 || $this->cols <= 0) {
            throw new InvalidArgumentException("Las filas y columnas deben ser números positivos");
        }

        if ($this->mines < 0 || $this->mines >= ($this->rows * $this->cols)) {
            throw new InvalidArgumentException("El número de minas debe ser menor que el total de casillas");
        }
    }

    /**
     * Crea un mapa desde un nivel de dificultad predefinido
     * @param string $level Nivel de dificultad
     * @return MinesweeperMap
     */
    public static function fromDifficulty($level)
    {
        switch ($level) {
            case self::DIFFICULTY_EASY:
                return new self(9, 9, 10);
            
            case self::DIFFICULTY_MEDIUM:
                return new self(16, 16, 40);
            
            case self::DIFFICULTY_EXPERT:
                return new self(30, 16, 99);
            
            default:
                throw new InvalidArgumentException("Nivel de dificultad no válido: {$level}");
        }
    }

    /**
     * Genera el mapa con minas distribuidas aleatoriamente
     * @return void
     */
    public function generateMap()
    {
        // Inicializar mapa vacío
        $this->map = array_fill(0, $this->rows, array_fill(0, $this->cols, 0));

        // Colocar minas aleatoriamente
        $minesPlaced = 0;
        while ($minesPlaced < $this->mines) {
            $row = rand(0, $this->rows - 1);
            $col = rand(0, $this->cols - 1);

            // Si la casilla no tiene mina, colocar una
            if ($this->map[$row][$col] !== -1) {
                $this->map[$row][$col] = -1; // -1 representa una mina
                $minesPlaced++;
            }
        }

        // Calcular números para casillas adyacentes a minas
        for ($row = 0; $row < $this->rows; $row++) {
            for ($col = 0; $col < $this->cols; $col++) {
                if ($this->map[$row][$col] !== -1) {
                    $this->map[$row][$col] = $this->countAdjacentMines($row, $col);
                }
            }
        }

        $this->generated = true;
    }

    /**
     * Cuenta las minas adyacentes a una casilla
     * @param int $row Fila
     * @param int $col Columna
     * @return int Número de minas adyacentes
     */
    private function countAdjacentMines($row, $col)
    {
        $count = 0;
        
        // Revisar las 8 casillas adyacentes
        for ($r = $row - 1; $r <= $row + 1; $r++) {
            for ($c = $col - 1; $c <= $col + 1; $c++) {
                // Verificar límites y no contar la casilla actual
                if ($r >= 0 && $r < $this->rows && 
                    $c >= 0 && $c < $this->cols && 
                    !($r === $row && $c === $col)) {
                    
                    if ($this->map[$r][$c] === -1) {
                        $count++;
                    }
                }
            }
        }

        return $count;
    }

    /**
     * Obtiene el mapa generado
     * @return array Matriz del mapa
     */
    public function getMap()
    {
        if (!$this->generated) {
            throw new RuntimeException("El mapa no ha sido generado. Llama a generateMap() primero.");
        }
        
        return $this->map;
    }

    /**
     * Obtiene información básica del mapa
     * @return array
     */
    public function getMapInfo()
    {
        return [
            'rows' => $this->rows,
            'cols' => $this->cols,
            'mines' => $this->mines,
            'generated' => $this->generated
        ];
    }

    /**
     * Exporta el mapa a formato JSON
     * @return string JSON del mapa
     */
    public function toJson()
    {
        if (!$this->generated) {
            throw new RuntimeException("El mapa no ha sido generado. Llama a generateMap() primero.");
        }

        $data = [
            'rows' => $this->rows,
            'cols' => $this->cols,
            'mines' => $this->mines,
            'map' => $this->map,
            'generated_at' => date('Y-m-d H:i:s')
        ];

        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * Importa un mapa desde formato JSON
     * @param string $json Cadena JSON
     * @return MinesweeperMap
     */
    public static function fromJson($json)
    {
        $data = json_decode($json, true);
        
        if ($data === null) {
            throw new InvalidArgumentException("JSON inválido");
        }

        if (!isset($data['rows'], $data['cols'], $data['mines'], $data['map'])) {
            throw new InvalidArgumentException("Formato JSON inválido para MinesweeperMap");
        }

        $map = new self($data['rows'], $data['cols'], $data['mines']);
        $map->map = $data['map'];
        $map->generated = true;

        return $map;
    }

    /**
     * Exporta el mapa a formato XML
     * @return string XML del mapa
     */
    public function toXml()
    {
        if (!$this->generated) {
            throw new RuntimeException("El mapa no ha sido generado. Llama a generateMap() primero.");
        }

        $xml = new SimpleXMLElement('<minesweeper_map></minesweeper_map>');
        $xml->addChild('rows', $this->rows);
        $xml->addChild('cols', $this->cols);
        $xml->addChild('mines', $this->mines);
        $xml->addChild('generated_at', date('Y-m-d H:i:s'));

        $mapNode = $xml->addChild('map');
        
        foreach ($this->map as $rowIndex => $row) {
            $rowNode = $mapNode->addChild('row');
            $rowNode->addAttribute('index', $rowIndex);
            
            foreach ($row as $colIndex => $cell) {
                $cellNode = $rowNode->addChild('cell', $cell);
                $cellNode->addAttribute('col', $colIndex);
            }
        }

        return $xml->asXML();
    }

    /**
     * Importa un mapa desde formato XML
     * @param string $xml Cadena XML
     * @return MinesweeperMap
     */
    public static function fromXml($xml)
    {
        $xmlObj = simplexml_load_string($xml);
        
        if ($xmlObj === false) {
            throw new InvalidArgumentException("XML inválido");
        }

        if (!isset($xmlObj->rows, $xmlObj->cols, $xmlObj->mines, $xmlObj->map)) {
            throw new InvalidArgumentException("Formato XML inválido para MinesweeperMap");
        }

        $rows = (int)$xmlObj->rows;
        $cols = (int)$xmlObj->cols;
        $mines = (int)$xmlObj->mines;

        $map = new self($rows, $cols, $mines);
        $mapArray = [];

        foreach ($xmlObj->map->row as $row) {
            $rowIndex = (int)$row['index'];
            $mapArray[$rowIndex] = [];
            
            foreach ($row->cell as $cell) {
                $colIndex = (int)$cell['col'];
                $mapArray[$rowIndex][$colIndex] = (int)$cell;
            }
        }

        $map->map = $mapArray;
        $map->generated = true;

        return $map;
    }

    /**
     * Guarda el mapa en un archivo
     * @param string $filename Nombre del archivo
     * @param string $format Formato: 'json' o 'xml'
     * @return bool
     */
    public function saveToFile($filename, $format = 'json')
    {
        if (!$this->generated) {
            throw new RuntimeException("El mapa no ha sido generado. Llama a generateMap() primero.");
        }

        $content = '';
        switch (strtolower($format)) {
            case 'json':
                $content = $this->toJson();
                break;
            
            case 'xml':
                $content = $this->toXml();
                break;
            
            default:
                throw new InvalidArgumentException("Formato no soportado: {$format}");
        }

        return file_put_contents($filename, $content) !== false;
    }

    /**
     * Carga un mapa desde un archivo
     * @param string $filename Nombre del archivo
     * @param string $format Formato: 'json' o 'xml'
     * @return MinesweeperMap
     */
    public static function loadFromFile($filename, $format = 'json')
    {
        if (!file_exists($filename)) {
            throw new InvalidArgumentException("El archivo no existe: {$filename}");
        }

        $content = file_get_contents($filename);
        
        if ($content === false) {
            throw new RuntimeException("No se pudo leer el archivo: {$filename}");
        }

        switch (strtolower($format)) {
            case 'json':
                return self::fromJson($content);
            
            case 'xml':
                return self::fromXml($content);
            
            default:
                throw new InvalidArgumentException("Formato no soportado: {$format}");
        }
    }
}
?> 