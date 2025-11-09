<?php

return [
    'title' => 'Calculadora de subredes IPv4 – ChilamVPN',
    'desc' => 'Calcula rangos de red IPv4, direcciones broadcast y hosts disponibles en segundos. Ingresa una IP con CIDR para planificar tus subredes.',
    'hero' => [
        'title' => 'Calculadora IPv4',
        'tagline' => 'Convierte prefijos CIDR en redes, broadcast, máscaras comodín y conteo de hosts sin instalar software.',
        'cta' => 'Calcular ahora',
    ],
    'form' => [
        'heading' => 'Ingresa una dirección IPv4',
        'ip_label' => 'Dirección IPv4',
        'cidr_label' => 'Prefijo (CIDR)',
        'netmask_label' => 'Máscara (opcional)',
        'hint' => 'Puedes escribir el prefijo (por ejemplo /24) o la máscara punteada. El formulario sincroniza ambos valores automáticamente.',
        'helper_mobile' => 'Prefijo y máscara se sincronizan solos; usa el campo que prefieras.',
        'live_indicator' => 'Calcula automáticamente mientras escribes',
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
        'error_invalid_ip' => 'Escribe una IPv4 válida como 192.168.0.1.',
        'error_invalid_mask' => 'Indica un prefijo entre 0 y 32 o una máscara punteada válida.',
    ],
    'seo' => [
        'faq_title' => 'Beneficios de la calculadora IPv4',
        'points' => [
            [
                'title' => 'Diseña redes corporativas',
                'body' => 'Transforma rápidamente cualquier CIDR en datos accionables para VLAN, oficinas remotas o túneles ChilamVPN.',
            ],
            [
                'title' => 'Previene conflictos de IP',
                'body' => 'Visualizar broadcast, comodín y hosts libres reduce los errores al asignar segmentos en firewalls y routers.',
            ],
            [
                'title' => 'Documenta tus cambios',
                'body' => 'Incluye los resultados en actas o runbooks para rastrear máscaras, rangos y presupuestos de hosts.',
            ],
        ],
    ],
];
