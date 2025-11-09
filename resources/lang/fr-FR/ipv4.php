<?php

return [
    'title' => 'Calculatrice de sous-réseau IPv4 – ChilamVPN',
    'desc' => 'Calculez instantanément les plages réseau IPv4, les adresses de broadcast et le nombre d’hôtes disponibles. Saisissez une IP avec son CIDR.',
    'hero' => [
        'title' => 'Calculatrice de sous-réseau IPv4',
        'tagline' => 'Transformez vos préfixes CIDR en réseaux, broadcast, masques génériques et comptage d’hôtes directement dans le navigateur.',
        'cta' => 'Calculer',
    ],
    'form' => [
        'heading' => 'Saisissez une adresse IPv4',
        'ip_label' => 'Adresse IPv4',
        'cidr_label' => 'Préfixe (CIDR)',
        'netmask_label' => 'Masque (optionnel)',
        'hint' => 'Indiquez soit le préfixe (/24, /29…), soit le masque pointé. Les deux champs restent synchronisés.',
        'helper_mobile' => 'Préfixe et masque restent synchronisés : complétez simplement le champ qui vous convient.',
        'submit' => 'Calculer',
    ],
    'results' => [
        'heading' => 'Détails de la sous-réseau',
        'network_bits' => 'Bits réseau',
        'host_bits' => 'Bits hôte',
        'network_address' => 'Adresse réseau',
        'broadcast_address' => 'Adresse de broadcast',
        'cidr_netmask' => 'Masque CIDR',
        'wildcard_mask' => 'Masque générique',
        'total_hosts' => 'Hôtes totaux par sous-réseau',
        'usable_hosts' => 'Hôtes utilisables par sous-réseau',
        'first_host' => 'Premier hôte utilisable',
        'last_host' => 'Dernier hôte utilisable',
        'subnet_count' => 'Nombre de sous-réseaux',
        'error_invalid_ip' => 'Indiquez une adresse IPv4 valide, par exemple 192.168.0.1.',
        'error_invalid_mask' => 'Fournissez un préfixe entre 0 et 32 ou un masque pointé valide.',
    ],
    'seo' => [
        'faq_title' => 'Atouts de la calculatrice IPv4',
        'points' => [
            [
                'title' => 'Planification accélérée',
                'body' => 'Les conversions CIDR instantanées facilitent la conception de VLAN, VPN et réseaux d’entreprise.',
            ],
            [
                'title' => 'Moins de conflits d’adresses',
                'body' => 'Visualiser broadcast, wildcard et hôtes disponibles évite les chevauchements lors du provisionnement.',
            ],
            [
                'title' => 'Documentation précise',
                'body' => 'Copiez les résultats dans vos procédures ChilamVPN pour décrire masques, plages et budgets d’hôtes.',
            ],
        ],
    ],
];
