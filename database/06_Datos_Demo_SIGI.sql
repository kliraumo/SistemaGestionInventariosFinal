/*
    Archivo: 06_Datos_Demo_SIGI.sql
    Proyecto: SIGI Enterprise
    Motor: SQL Server
    Objetivo:
      - Crear 60 productos de demostración.
      - Crear o actualizar sus existencias iniciales.
      - Evitar productos duplicados mediante el campo Codigo.

    Requisitos:
      Categorias: IdCategoria 1=General, 2=Tecnología, 3=Limpieza
      Unidades:   IdUnidad 1=Unidad, 2=Caja, 3=Kilogramo, 4=Litro
      Usuarios:   IdUsuario 1=admin
*/

SET NOCOUNT ON;
SET XACT_ABORT ON;

BEGIN TRY
    BEGIN TRANSACTION;

    IF OBJECT_ID(N'dbo.Productos', N'U') IS NULL
        THROW 50001, 'No existe la tabla dbo.Productos.', 1;

    IF OBJECT_ID(N'dbo.Existencias', N'U') IS NULL
        THROW 50002, 'No existe la tabla dbo.Existencias.', 1;

    IF NOT EXISTS (SELECT 1 FROM dbo.Categorias WHERE IdCategoria = 1)
        THROW 50003, 'No existe la categoría General con IdCategoria = 1.', 1;

    IF NOT EXISTS (SELECT 1 FROM dbo.Categorias WHERE IdCategoria = 2)
        THROW 50004, 'No existe la categoría Tecnología con IdCategoria = 2.', 1;

    IF NOT EXISTS (SELECT 1 FROM dbo.Categorias WHERE IdCategoria = 3)
        THROW 50005, 'No existe la categoría Limpieza con IdCategoria = 3.', 1;

    IF NOT EXISTS (SELECT 1 FROM dbo.Usuarios WHERE IdUsuario = 1)
        THROW 50006, 'No existe el usuario administrador con IdUsuario = 1.', 1;

    DECLARE @ProductosDemo TABLE
    (
        Codigo             VARCHAR(30)    NOT NULL,
        CodigoBarras       VARCHAR(30)    NULL,
        Nombre             VARCHAR(200)   NOT NULL,
        Descripcion        VARCHAR(500)   NULL,
        IdCategoria        INT            NOT NULL,
        IdUnidad           INT            NOT NULL,
        StockMinimo        DECIMAL(18,2)  NOT NULL,
        StockMaximo        DECIMAL(18,2)  NOT NULL,
        StockInicial       DECIMAL(18,2)  NOT NULL
    );

    INSERT INTO @ProductosDemo
    (
        Codigo,
        CodigoBarras,
        Nombre,
        Descripcion,
        IdCategoria,
        IdUnidad,
        StockMinimo,
        StockMaximo,
        StockInicial
    )
    VALUES
        (N'TEC-0001', N'7861000000011', N'Laptop Dell Latitude 3540', N'Laptop empresarial para tareas administrativas', 2, 1, 5.00, 25.00, 12.00),
        (N'TEC-0002', N'7861000000028', N'Laptop HP ProBook 440', N'Equipo portátil para oficina y gestión', 2, 1, 5.00, 25.00, 9.00),
        (N'TEC-0003', N'7861000000035', N'Monitor Samsung 24 pulgadas', N'Monitor LED Full HD para estación de trabajo', 2, 1, 8.00, 40.00, 18.00),
        (N'TEC-0004', N'7861000000042', N'Monitor LG 27 pulgadas', N'Monitor IPS Full HD de 27 pulgadas', 2, 1, 5.00, 30.00, 7.00),
        (N'TEC-0005', N'7861000000059', N'Mouse Logitech M185', N'Mouse inalámbrico para oficina', 2, 1, 15.00, 100.00, 45.00),
        (N'TEC-0006', N'7861000000066', N'Teclado Logitech K120', N'Teclado USB de uso corporativo', 2, 1, 15.00, 100.00, 38.00),
        (N'TEC-0007', N'7861000000073', N'Combo Teclado y Mouse MK270', N'Kit inalámbrico Logitech', 2, 1, 10.00, 60.00, 22.00),
        (N'TEC-0008', N'7861000000080', N'Disco SSD 480 GB', N'Unidad de estado sólido SATA', 2, 1, 8.00, 50.00, 15.00),
        (N'TEC-0009', N'7861000000097', N'Disco SSD 1 TB', N'Unidad de estado sólido de alta capacidad', 2, 1, 5.00, 30.00, 6.00),
        (N'TEC-0010', N'7861000000103', N'Memoria RAM DDR4 8 GB', N'Memoria para computadores de escritorio', 2, 1, 10.00, 60.00, 24.00),
        (N'TEC-0011', N'7861000000110', N'Memoria RAM DDR4 16 GB', N'Memoria para equipos corporativos', 2, 1, 8.00, 40.00, 14.00),
        (N'TEC-0012', N'7861000000127', N'Router TP-Link Gigabit', N'Router inalámbrico doble banda', 2, 1, 5.00, 25.00, 11.00),
        (N'TEC-0013', N'7861000000134', N'Switch TP-Link 8 Puertos', N'Switch de red no administrable', 2, 1, 5.00, 30.00, 13.00),
        (N'TEC-0014', N'7861000000141', N'Cable de Red Cat6 3 m', N'Cable UTP categoría 6', 2, 1, 20.00, 200.00, 85.00),
        (N'TEC-0015', N'7861000000158', N'Cable HDMI 2 m', N'Cable HDMI para conexión audiovisual', 2, 1, 20.00, 150.00, 62.00),
        (N'TEC-0016', N'7861000000165', N'Webcam Logitech C270', N'Cámara web HD para videollamadas', 2, 1, 8.00, 40.00, 16.00),
        (N'TEC-0017', N'7861000000172', N'Auriculares USB con Micrófono', N'Diadema para reuniones virtuales', 2, 1, 10.00, 50.00, 19.00),
        (N'TEC-0018', N'7861000000189', N'Impresora HP LaserJet', N'Impresora láser monocromática', 2, 1, 3.00, 15.00, 5.00),
        (N'TEC-0019', N'7861000000196', N'UPS 1000 VA', N'Sistema de alimentación ininterrumpida', 2, 1, 5.00, 25.00, 8.00),
        (N'TEC-0020', N'7861000000202', N'Regleta Eléctrica 6 Tomas', N'Protector eléctrico para oficina', 2, 1, 12.00, 80.00, 31.00),
        (N'LIM-0001', N'7862000000014', N'Alcohol Antiséptico 70% 1L', N'Solución desinfectante de uso general', 3, 4, 20.00, 120.00, 55.00),
        (N'LIM-0002', N'7862000000021', N'Desinfectante Multiuso 1L', N'Producto para limpieza de superficies', 3, 4, 20.00, 120.00, 48.00),
        (N'LIM-0003', N'7862000000038', N'Cloro 1 Galón', N'Cloro líquido para desinfección', 3, 4, 15.00, 80.00, 36.00),
        (N'LIM-0004', N'7862000000045', N'Detergente Líquido 1L', N'Detergente para limpieza general', 3, 4, 20.00, 100.00, 42.00),
        (N'LIM-0005', N'7862000000052', N'Jabón Líquido para Manos 1L', N'Jabón antibacterial para dispensador', 3, 4, 20.00, 100.00, 63.00),
        (N'LIM-0006', N'7862000000069', N'Limpiavidrios 500 ml', N'Limpiador para vidrios y espejos', 3, 1, 20.00, 120.00, 51.00),
        (N'LIM-0007', N'7862000000076', N'Ambientador Aerosol', N'Aromatizante para oficinas', 3, 1, 20.00, 100.00, 44.00),
        (N'LIM-0008', N'7862000000083', N'Papel Higiénico Industrial', N'Rollo de papel higiénico institucional', 3, 1, 30.00, 200.00, 96.00),
        (N'LIM-0009', N'7862000000090', N'Toalla de Papel Industrial', N'Rollo de toalla absorbente', 3, 1, 25.00, 150.00, 78.00),
        (N'LIM-0010', N'7862000000106', N'Fundas de Basura Grandes', N'Paquete de fundas resistentes', 3, 1, 25.00, 150.00, 67.00),
        (N'LIM-0011', N'7862000000113', N'Guantes de Nitrilo Caja x100', N'Guantes desechables para limpieza', 3, 2, 10.00, 60.00, 28.00),
        (N'LIM-0012', N'7862000000120', N'Mascarillas Desechables Caja x50', N'Mascarillas de protección personal', 3, 2, 10.00, 60.00, 23.00),
        (N'LIM-0013', N'7862000000137', N'Escoba Industrial', N'Escoba para áreas amplias', 3, 1, 8.00, 40.00, 17.00),
        (N'LIM-0014', N'7862000000144', N'Trapeador Industrial', N'Trapeador de alta absorción', 3, 1, 8.00, 40.00, 12.00),
        (N'LIM-0015', N'7862000000151', N'Recogedor Plástico', N'Recogedor para limpieza general', 3, 1, 10.00, 50.00, 21.00),
        (N'LIM-0016', N'7862000000168', N'Balde Plástico 12L', N'Balde resistente para limpieza', 3, 1, 8.00, 40.00, 15.00),
        (N'LIM-0017', N'7862000000175', N'Paño Microfibra', N'Paño reutilizable para superficies', 3, 1, 30.00, 200.00, 110.00),
        (N'LIM-0018', N'7862000000182', N'Esponja Multiuso', N'Esponja para limpieza de superficies', 3, 1, 40.00, 250.00, 135.00),
        (N'LIM-0019', N'7862000000199', N'Cepillo de Limpieza', N'Cepillo manual de cerdas resistentes', 3, 1, 10.00, 60.00, 24.00),
        (N'LIM-0020', N'7862000000205', N'Señal Piso Mojado', N'Señal preventiva plegable', 3, 1, 5.00, 25.00, 9.00),
        (N'GEN-0001', N'7863000000017', N'Resma Papel Bond A4', N'Papel blanco de 75 gramos', 1, 2, 20.00, 150.00, 73.00),
        (N'GEN-0002', N'7863000000024', N'Bolígrafo Azul Caja x12', N'Bolígrafos de tinta azul', 1, 2, 15.00, 100.00, 54.00),
        (N'GEN-0003', N'7863000000031', N'Bolígrafo Negro Caja x12', N'Bolígrafos de tinta negra', 1, 2, 15.00, 100.00, 47.00),
        (N'GEN-0004', N'7863000000048', N'Marcador Permanente Negro', N'Marcador de punta gruesa', 1, 1, 20.00, 120.00, 66.00),
        (N'GEN-0005', N'7863000000055', N'Marcador de Pizarra Azul', N'Marcador borrable para pizarra', 1, 1, 20.00, 120.00, 58.00),
        (N'GEN-0006', N'7863000000062', N'Carpeta Archivadora Oficio', N'Carpeta de cartón con palanca', 1, 1, 20.00, 150.00, 82.00),
        (N'GEN-0007', N'7863000000079', N'Cuaderno Universitario 100 Hojas', N'Cuaderno de líneas para oficina', 1, 1, 20.00, 120.00, 49.00),
        (N'GEN-0008', N'7863000000086', N'Notas Adhesivas 76x76', N'Bloc de notas autoadhesivas', 1, 1, 25.00, 150.00, 76.00),
        (N'GEN-0009', N'7863000000093', N'Cinta Adhesiva Transparente', N'Rollo de cinta para oficina', 1, 1, 25.00, 150.00, 69.00),
        (N'GEN-0010', N'7863000000109', N'Grapadora Metálica', N'Grapadora de escritorio', 1, 1, 10.00, 60.00, 27.00),
        (N'GEN-0011', N'7863000000116', N'Caja de Grapas 26/6', N'Grapas estándar para oficina', 1, 2, 20.00, 120.00, 64.00),
        (N'GEN-0012', N'7863000000123', N'Perforadora Metálica', N'Perforadora de dos agujeros', 1, 1, 8.00, 40.00, 18.00),
        (N'GEN-0013', N'7863000000130', N'Tijera de Oficina', N'Tijera de acero inoxidable', 1, 1, 10.00, 60.00, 26.00),
        (N'GEN-0014', N'7863000000147', N'Calculadora de Escritorio', N'Calculadora de 12 dígitos', 1, 1, 8.00, 40.00, 14.00),
        (N'GEN-0015', N'7863000000154', N'Archivador Plástico A4', N'Archivador para documentos', 1, 1, 15.00, 100.00, 41.00),
        (N'GEN-0016', N'7863000000161', N'Sobres Manila A4 Paquete x50', N'Sobres para archivo y correspondencia', 1, 2, 10.00, 60.00, 29.00),
        (N'GEN-0017', N'7863000000178', N'Etiquetas Adhesivas A4', N'Paquete de hojas autoadhesivas', 1, 2, 10.00, 60.00, 22.00),
        (N'GEN-0018', N'7863000000185', N'Tóner HP 85A Compatible', N'Cartucho de tóner para impresora', 1, 1, 5.00, 30.00, 11.00),
        (N'GEN-0019', N'7863000000192', N'Tinta Epson Negra 544', N'Botella de tinta negra para impresora', 1, 1, 8.00, 40.00, 16.00),
        (N'GEN-0020', N'7863000000208', N'Caja Organizadora Plástica', N'Contenedor para suministros', 1, 1, 8.00, 40.00, 13.00);

    /* Insertar únicamente los productos que todavía no existen. */
    INSERT INTO dbo.Productos
    (
        Codigo,
        CodigoBarras,
        Nombre,
        Descripcion,
        IdCategoria,
        IdUnidad,
        StockMinimo,
        StockMaximo,
        Estado,
        FechaCreacion,
        IdUsuarioCreacion
    )
    SELECT
        D.Codigo,
        D.CodigoBarras,
        D.Nombre,
        D.Descripcion,
        D.IdCategoria,
        D.IdUnidad,
        D.StockMinimo,
        D.StockMaximo,
        1,
        SYSDATETIME(),
        1
    FROM @ProductosDemo AS D
    WHERE NOT EXISTS
    (
        SELECT 1
        FROM dbo.Productos AS P
        WHERE P.Codigo = D.Codigo
    );

    /*
       Crear la existencia inicial o actualizarla cuando el producto ya exista.
       RowVersion no se incluye porque SQL Server lo genera automáticamente.
    */
    MERGE dbo.Existencias AS DESTINO
    USING
    (
        SELECT
            P.IdProducto,
            D.StockInicial
        FROM @ProductosDemo AS D
        INNER JOIN dbo.Productos AS P
            ON P.Codigo = D.Codigo
    ) AS ORIGEN
        ON DESTINO.IdProducto = ORIGEN.IdProducto
    WHEN MATCHED THEN
        UPDATE SET
            DESTINO.StockActual = ORIGEN.StockInicial,
            DESTINO.FechaUltimaActualizacion = SYSDATETIME()
    WHEN NOT MATCHED BY TARGET THEN
        INSERT
        (
            IdProducto,
            StockActual,
            FechaUltimaActualizacion
        )
        VALUES
        (
            ORIGEN.IdProducto,
            ORIGEN.StockInicial,
            SYSDATETIME()
        );

    COMMIT TRANSACTION;

    SELECT
        COUNT(*) AS ProductosDemoProcesados,
        SUM(CASE WHEN P.Estado = 1 THEN 1 ELSE 0 END) AS ProductosActivos,
        SUM(CASE WHEN E.StockActual = 0 THEN 1 ELSE 0 END) AS ProductosAgotados,
        SUM(CASE WHEN E.StockActual > 0
                  AND E.StockActual <= P.StockMinimo THEN 1 ELSE 0 END) AS ProductosBajoMinimo,
        SUM(E.StockActual) AS StockTotalDemo
    FROM dbo.Productos AS P
    INNER JOIN dbo.Existencias AS E
        ON E.IdProducto = P.IdProducto
    INNER JOIN @ProductosDemo AS D
        ON D.Codigo = P.Codigo;

    SELECT
        P.IdProducto,
        P.Codigo,
        P.Nombre,
        C.Nombre AS Categoria,
        U.Nombre AS Unidad,
        E.StockActual,
        P.StockMinimo,
        P.StockMaximo
    FROM dbo.Productos AS P
    INNER JOIN dbo.Existencias AS E
        ON E.IdProducto = P.IdProducto
    INNER JOIN dbo.Categorias AS C
        ON C.IdCategoria = P.IdCategoria
    INNER JOIN dbo.Unidades AS U
        ON U.IdUnidad = P.IdUnidad
    INNER JOIN @ProductosDemo AS D
        ON D.Codigo = P.Codigo
    ORDER BY P.Codigo;

END TRY
BEGIN CATCH
    IF @@TRANCOUNT > 0
        ROLLBACK TRANSACTION;

    DECLARE @Mensaje NVARCHAR(4000) = ERROR_MESSAGE();
    DECLARE @Numero INT = ERROR_NUMBER();
    DECLARE @Linea INT = ERROR_LINE();

    RAISERROR(
        'Error al ejecutar 06_Datos_Demo_SIGI.sql. Número: %d. Línea: %d. Detalle: %s',
        16,
        1,
        @Numero,
        @Linea,
        @Mensaje
    );
END CATCH;
