<?php

return [
    'title' => 'IPv4 subnets berekenen – ChilamVPN',
    'desc' => 'Bereken netwerkadressen, broadcast, wildcardmaskers en bruikbare hosts uit elke IPv4-prefix. Voer een IP met CIDR of een netmasker in en zie direct de resultaten.',
    'hero' => [
        'title' => 'IPv4 subnetcalculator',
        'tagline' => 'Zet CIDR-prefixen om in netwerk-, broadcast- en hostinformatie zonder tools te installeren.',
        'cta' => 'Start berekening',
    ],
    'form' => [
        'heading' => 'Voer een IPv4-adres in',
        'ip_label' => 'IPv4-adres',
        'cidr_label' => 'Prefix (CIDR)',
        'netmask_label' => 'Netmasker (optioneel)',
        'hint' => 'Typ een prefix (bijv. /24) of een dotted netmasker; beide velden blijven automatisch gesynchroniseerd.',
        'helper_mobile' => 'Prefix en masker blijven gelijk; gebruik het veld dat jij het prettigst vindt.',
        'live_indicator' => 'Berekent automatisch tijdens het typen',
        'submit' => 'Berekenen',
    ],
    'results' => [
        'heading' => 'Subnetdetails',
        'network_bits' => 'Bits voor netwerk',
        'host_bits' => 'Bits voor hosts',
        'network_address' => 'Netwerkadres',
        'broadcast_address' => 'Broadcastadres',
        'cidr_netmask' => 'CIDR-netmasker',
        'wildcard_mask' => 'Wildcardmasker',
        'total_hosts' => 'Totaal hosts per subnet',
        'usable_hosts' => 'Bruikbare hosts per subnet',
        'first_host' => 'Eerste bruikbare host',
        'last_host' => 'Laatste bruikbare host',
        'subnet_count' => 'Aantal subnets',
        'error_invalid_ip' => 'Voer een geldig IPv4-adres in, zoals 192.168.0.1.',
        'error_invalid_mask' => 'Geef een prefix tussen 0 en 32 of een geldig netmasker op.',
    ],
    'seo' => [
        'faq_title' => 'Waarom een IPv4-calculator gebruiken?',
        'points' => [
            [
                'title' => 'Plan sneller',
                'body' => 'Directe CIDR-conversies helpen engineers VLAN’s, VPN’s en VPC-subnets te ontwerpen zonder handmatig rekenwerk.',
            ],
            [
                'title' => 'Voorkom adresconflicten',
                'body' => 'Netwerk-, broadcast- en hostreeksen in één overzicht verminderen fouten bij het provisionen van routers of firewalls.',
            ],
            [
                'title' => 'Documenteer implementaties',
                'body' => 'Combineer de resultaten met ChilamVPN-runbooks om tunnels, maskers en hostbudgetten vast te leggen.',
            ],
        ],
    ],
];
