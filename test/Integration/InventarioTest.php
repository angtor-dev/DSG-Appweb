<?php
use PHPUnit\Framework\TestCase;

final class InventarioIntTest extends TestCase
{
    private static Usuario $usuario;

    public static function setUpBeforeClass() : void
    {
        parent::setUpBeforeClass();
        self::$usuario = Usuario::cargar(1, true);
        self::$usuario->beginTestTransaction();
        $_SESSION['usuario'] = self::$usuario;
    }

    public static function tearDownAfterClass() : void
    {
        parent::tearDownAfterClass();
        self::$usuario->stopTestTransaction();
        unset($_SESSION['usuario']);
    }

    /** @test */
    public function registrarCategoria() : void
    {
        // Arrange
        $categoria = new Categoria();
        $nombre = "Categoria de prueba";
        $descripcion = "Descripcion de la categoria de prueba";
        $color = "FFFFFF";

        // Act
        $categoria->setDatos(
            null,
            $nombre,
            $descripcion,
            $color
        );
        $esValido = $categoria->esValido();
        $seRegistro = $categoria->registrar();

        // Assert
        $this->assertTrue($esValido, "La categoría debería ser válida.");
        $this->assertTrue($seRegistro, "La categoría debería haberse registrado correctamente.");
    }

    /** @test */
    public function listarCategorias() : void
    {
        // Arrange
        $categoria = new Categoria();

        // Act
        /** @var Categoria[] $categorias */
        $categorias = $categoria->listar();
        $ultimaCategoria = end($categorias);

        // Assert
        $this->assertIsArray($categorias, "El resultado debería ser un array.");
        $this->assertNotEmpty($categorias, "El array de categorías no debería estar vacío.");
        $this->assertEquals(
            "Categoria de prueba",
            $ultimaCategoria->getNombre(),
            "El nombre de la última categoría debería coincidir con el registrado."
        );
    }

    /** @test */
    public function registrarMedida() : void
    {
        // Arrange
        $medida = new Medida();
        $unidad = "Kilogramo prueba";
        $abreviacion = "kg";

        // Act
        $medida->setDatos(
            null,
            $unidad,
            $abreviacion
        );
        $esValido = $medida->esValido();
        $seRegistro = $medida->registrar();

        // Assert
        $this->assertTrue($esValido, "La medida debería ser válida.");
        $this->assertTrue($seRegistro, "La medida debería haberse registrado correctamente.");
    }

    /** @test */
    public function listarMedidas() : void
    {
        // Arrange
        $medida = new Medida();

        // Act
        /** @var Medida[] $medidas */
        $medidas = $medida->listar();
        $ultimaMedida = end($medidas);

        // Assert
        $this->assertIsArray($medidas, "El resultado debería ser un array.");
        $this->assertNotEmpty($medidas, "El array de medidas no debería estar vacío.");
        $this->assertEquals(
            "Kilogramo prueba",
            $ultimaMedida->getUnidad(),
            "El nombre de la última medida debería coincidir con el registrado."
        );
    }

    /** @test */
    public function registrarArticulo() : void
    {
        // Arrange
        $articulo = new Articulo();
        $categoria = new Categoria();
        $medida = new Medida();
        $articulo = new Articulo();
        $nombre = "Artículo de prueba";
        $descripcion = "Descripción del artículo de prueba";
        $cantidad = 0;
        $esConsumible = true;
        
        // Act
        $categoria = $categoria->cargarUltimo();
        $medida = $medida->cargarUltimo();
        $articulo->setDatos(
            null,
            $categoria->id,
            $medida->id,
            $nombre,
            $descripcion,
            $cantidad,
            $esConsumible
        );
        $esValido = $articulo->esValido();
        $seRegistro = $articulo->registrar();

        // Assert
        $this->assertTrue($esValido, "El artículo debería ser válido.");
        $this->assertTrue($seRegistro, "El artículo debería haberse registrado correctamente.");
    }

    /** @test */
    final public function listarArticulos() : void
    {
        // Arrange
        $articulo = new Articulo();

        // Act
        /** @var Articulo[] $articulos */
        $articulos = $articulo->listar();
        $ultimoArticulo = end($articulos);

        // Assert
        $this->assertIsArray($articulos, "El resultado debería ser un array.");
        $this->assertNotEmpty($articulos, "El array de artículos no debería estar vacío.");
        $this->assertEquals(
            "Artículo de prueba",
            $ultimoArticulo->getNombre(),
            "El nombre del último artículo debería coincidir con el registrado."
        );
        $this->assertEquals(
            "Categoria de prueba",
            $ultimoArticulo->categoria->getNombre(),
            "El nombre de la categoría del último artículo debería coincidir con el registrado."
        );
        $this->assertEquals(
            "Kilogramo prueba",
            $ultimoArticulo->medida->getUnidad(),
            "El nombre de la medida del último artículo debería coincidir con el registrado."
        );
    }

    /** @test */
    public function actualizarArticulo() : void
    {
        // Arrange
        $articulo = new Articulo();
        /** @var Articulo */
        $ultimoArticulo = $articulo->cargarUltimo();
        $nuevoNombre = "Artículo de prueba actualizado";
        $nuevaDescripcion = "Descripción del artículo de prueba actualizada";
        $esConsumible = false;

        // Act
        $ultimoArticulo->setDatos(
            $ultimoArticulo->id,
            $ultimoArticulo->idCategoria,
            $ultimoArticulo->idMedida,
            $nuevoNombre,
            $nuevaDescripcion,
            $ultimoArticulo->getCantidad(),
            $esConsumible
        );
        $esValido = $ultimoArticulo->esValido();
        $seActualizo = $ultimoArticulo->actualizar();
        /** @var Articulo */
        $articuloActualizado = $articulo->cargar($ultimoArticulo->id);

        // Assert
        $this->assertTrue($esValido, "El artículo actualizado debería ser válido.");
        $this->assertTrue($seActualizo, "El artículo debería haberse actualizado correctamente.");
        $this->assertEquals(
            $nuevoNombre,
            $articuloActualizado->getNombre(),
            "El nombre del artículo debería haberse actualizado correctamente."
        );
        $this->assertEquals(
            $nuevaDescripcion,
            $articuloActualizado->getDescripcion(),
            "La descripción del artículo debería haberse actualizado correctamente."
        );
        $this->assertEquals(
            $esConsumible,
            $articuloActualizado->getEsConsumible(),
            "El estado de consumible del artículo debería haberse actualizado correctamente."
        );
    }
}
