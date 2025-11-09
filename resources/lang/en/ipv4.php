<?php

return [
    'title' => 'IPv4 subnet calculator – ChilamVPN',
    'desc' => 'Calculate IPv4 network ranges, broadcast addresses and usable hosts instantly. Enter an IP with CIDR to plan your subnets.',
    'hero' => [
        'title' => 'IPv4 subnet calculator',
        'tagline' => 'Convert CIDR prefixes into network, broadcast, wildcard masks and host counts without leaving the browser.',
        'cta' => 'Calculate now',
    ],
    'form' => [
        'heading' => 'Enter an IPv4 address',
        'ip_label' => 'IPv4 address',
        'cidr_label' => 'Prefix (CIDR)',
        'netmask_label' => 'Netmask (optional)',
        'hint' => 'Type a prefix (for example /24) or a dotted netmask. The calculator keeps both values in sync automatically.',
        'helper_mobile' => 'Fields stay synchronized, so you can type either CIDR or netmask.',
        'live_indicator' => 'Auto-calculates as you type',
        'submit' => 'Calculate',
    ],
    'results' => [
        'heading' => 'Subnet details',
        'network_bits' => 'Network bits',
        'host_bits' => 'Host bits',
        'network_address' => 'Network address',
        'broadcast_address' => 'Broadcast address',
        'cidr_netmask' => 'CIDR netmask',
        'wildcard_mask' => 'Wildcard mask',
        'total_hosts' => 'Total hosts per subnet',
        'usable_hosts' => 'Usable hosts per subnet',
        'first_host' => 'First usable host',
        'last_host' => 'Last usable host',
        'subnet_count' => 'Number of subnets',
        'error_invalid_ip' => 'Enter a valid IPv4 address such as 192.168.0.1.',
        'error_invalid_mask' => 'Provide a prefix between 0 and 32 or a valid dotted netmask.',
    ],
    'seo' => [
        'faq_title' => 'Why use an IPv4 subnet calculator?',
        'points' => [
            [
                'title' => 'Plan networks faster',
                'body' => 'Instant CIDR conversions help engineers design VLANs, VPNs and VPC subnets without manual math.',
            ],
            [
                'title' => 'Avoid addressing errors',
                'body' => 'Seeing broadcast, wildcard and host ranges in one place prevents overlaps when provisioning routers or firewalls.',
            ],
            [
                'title' => 'Document deployments',
                'body' => 'Pair this IPv4 calculator with ChilamVPN to describe each tunnel, subnet mask and host budget in your runbooks.',
            ],
        ],
    ],
];
