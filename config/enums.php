<?php

return [
    'listar_destino_final' => [
        ['nombre' => 'Baja', 'valor' => 'B'],
        ['nombre' => 'Archivo Histórico', 'valor' => 'AH'],
        ['nombre' => 'Muestreo', 'valor' => 'M'],
    ],
    'validar_destino_final' => ['B','AH','M'],
    'tipos_documento_firmas' => [
        'inventario_documental' => 1,
        'cadido' => 2,
        'transferencia_primaria' => 3,
    ],
    'estados_transferencias' => [
        'abierto' => ['valor' => 1,'texto' => 'Captura abierta'],
        'pendiente_revision' => ['valor' => 2,'texto' => 'Revisión pendiente'],
        'rechazado' => ['valor' => 3,'texto' => 'Validación rechazada'],
        'cerrado' => ['valor' => 0,'texto' => 'Captura cerrada'],
    ],
];
