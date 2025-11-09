<?php

return [
    'title' => 'Calculadora de sub-redes IPv4 – ChilamVPN',
    'desc' => 'Calcule faixas de rede IPv4, endereços de broadcast e hosts utilizáveis em segundos. Informe um IP com CIDR para planejar sub-redes.',
    'hero' => [
        'title' => 'Calculadora de sub-redes IPv4',
        'tagline' => 'Converta prefixos CIDR em rede, broadcast, máscara curinga e contagem de hosts direto no navegador.',
        'cta' => 'Calcular agora',
    ],
    'form' => [
        'heading' => 'Informe um endereço IPv4',
        'ip_label' => 'Endereço IPv4',
        'cidr_label' => 'Prefixo (CIDR)',
        'netmask_label' => 'Máscara (opcional)',
        'hint' => 'Digite o prefixo (/24, /29, etc.) ou a máscara pontilhada. A ferramenta mantém os campos sincronizados automaticamente.',
        'helper_mobile' => 'Os campos ficam sincronizados; escolha prefixo ou máscara, como preferir.',
        'live_indicator' => 'Calcula automaticamente enquanto você digita',
        'submit' => 'Calcular',
    ],
    'results' => [
        'heading' => 'Detalhes da sub-rede',
        'network_bits' => 'Bits de rede',
        'host_bits' => 'Bits de host',
        'network_address' => 'Endereço de rede',
        'broadcast_address' => 'Endereço de broadcast',
        'cidr_netmask' => 'Máscara CIDR',
        'wildcard_mask' => 'Máscara curinga',
        'total_hosts' => 'Hosts totais por sub-rede',
        'usable_hosts' => 'Hosts utilizáveis por sub-rede',
        'first_host' => 'Primeiro host utilizável',
        'last_host' => 'Último host utilizável',
        'subnet_count' => 'Número de sub-redes',
        'error_invalid_ip' => 'Informe um endereço IPv4 válido, como 192.168.0.1.',
        'error_invalid_mask' => 'Use um prefixo entre 0 e 32 ou uma máscara pontilhada válida.',
    ],
    'seo' => [
        'faq_title' => 'Por que usar a calculadora IPv4?',
        'points' => [
            [
                'title' => 'Planejamento mais ágil',
                'body' => 'Conversões CIDR instantâneas aceleram o desenho de VLANs, VPNs e redes corporativas.',
            ],
            [
                'title' => 'Menos erros de IP',
                'body' => 'Visualizar broadcast, wildcard e hosts disponíveis evita conflitos ao configurar roteadores e firewalls.',
            ],
            [
                'title' => 'Documentação clara',
                'body' => 'Os resultados podem ser copiados para runbooks ChilamVPN, indicando máscara, range e orçamento de hosts.',
            ],
        ],
    ],
];
