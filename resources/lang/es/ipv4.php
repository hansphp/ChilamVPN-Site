<?php

return [
    'title' => 'Calculadora de subredes IPv4 – ChilamVPN',
    'desc' => 'Calcula al instante rangos de red IPv4, direcciones broadcast y hosts disponibles. Introduce una IP con CIDR para planificar tus subredes.',
    'hero' => [
        'title' => 'Calculadora de subredes IPv4',
        'tagline' => 'Convierte prefijos CIDR en redes, broadcast, máscaras comodín y conteo de hosts sin salir del navegador.',
        'cta' => 'Calcular ahora',
    ],
    'form' => [
        'heading' => 'Ingresa una dirección IPv4',
        'ip_label' => 'Dirección IPv4',
        'cidr_label' => 'Prefijo (CIDR)',
        'netmask_label' => 'Máscara (opcional)',
        'hint' => 'Escribe un prefijo (por ejemplo /24) o una máscara punteada. La calculadora mantiene ambos valores sincronizados.',
        'submit' => 'Calcular',
    ],
    'results' => [
        'heading' => 'Detalles de la subred',
        'network_bits' => 'Bits de red',
        'host_bits' => 'Bits de host',
        'network_address' => 'Dirección de red',
        'broadcast_address' => 'Dirección broadcast',
        'cidr_netmask' => 'Máscara CIDR',
        'wildcard_mask' => 'Máscara comodín',
        'total_hosts' => 'Hosts totales por subred',
        'usable_hosts' => 'Hosts utilizables por subred',
        'first_host' => 'Primer host utilizable',
        'last_host' => 'Último host utilizable',
        'subnet_count' => 'Número de subredes',
        'error_invalid_ip' => 'Introduce una dirección IPv4 válida como 192.168.0.1.',
        'error_invalid_mask' => 'Indica un prefijo entre 0 y 32 o una máscara punteada válida.',
    ],
    'seo' => [
        'faq_title' => '¿Por qué usar una calculadora IPv4?',
        'points' => [
            [
                'title' => 'Planifica redes más rápido',
                'body' => 'Las conversiones CIDR inmediatas ayudan a diseñar VLAN, VPN o subredes VPC sin cálculos manuales.',
            ],
            [
                'title' => 'Evita errores de direccionamiento',
                'body' => 'Ver la broadcast, la máscara comodín y los rangos de hosts en un solo lugar evita solapamientos al configurar routers o firewalls.',
            ],
            [
                'title' => 'Documenta tus despliegues',
                'body' => 'Combina la calculadora IPv4 con ChilamVPN para describir cada túnel, máscara y presupuesto de hosts en tus runbooks.',
            ],
        ],
    ],
];
