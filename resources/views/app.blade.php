<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Collector\'s Hub') }}</title>

        <meta name="description" content="Collector's Hub - Rejoignez des milliers de dresseurs dans cette aventure épique de collection et de combat Pokémon. Gratuit, sans publicité, communauté active." />
        <meta name="keywords" content="collectorshub, collector's hub, collector hub, pokemon, collection, jeu pokemon, dresseur pokemon, marketplace pokemon, pokedex, jeu gratuit" />
        <meta name="author" content="Collector's Hub" />
        <meta name="robots" content="index, follow" />
        
        <meta property="og:title" content="Collector's Hub - La plateforme ultime pour les collectionneurs de Pokémon" />
        <meta property="og:description" content="Rejoignez des milliers de dresseurs dans cette aventure épique de collection et de combat Pokémon. Gratuit, sans publicité, communauté active." />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="https://collectorshub.fr" />
        <meta property="og:site_name" content="Collector's Hub" />
        <meta property="og:locale" content="fr_FR" />
        <meta property="og:image" content="https://collectorshub.fr/favicon.png" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
        <meta property="og:image:alt" content="Collector's Hub - Plateforme de collection Pokémon" />
        
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="Collector's Hub - La plateforme ultime pour les collectionneurs de Pokémon" />
        <meta name="twitter:description" content="Rejoignez des milliers de dresseurs dans cette aventure épique de collection et de combat Pokémon." />
        <meta name="twitter:image" content="https://collectorshub.fr/favicon.png" />
        
        <link rel="canonical" href="https://collectorshub.fr{{ request()->getRequestUri() }}" />
        <link rel="alternate" hreflang="fr" href="https://collectorshub.fr{{ request()->getRequestUri() }}" />
        <link rel="alternate" hreflang="x-default" href="https://collectorshub.fr{{ request()->getRequestUri() }}" />
        
        <link rel="icon" type="image/png" href="/favicon.png" />
        <link rel="apple-touch-icon" sizes="180x180" href="/favicon.png" />
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon.png" />
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon.png" />
        <link rel="manifest" href="/site.webmanifest" />
        
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link rel="dns-prefetch" href="https://fonts.bunny.net" />
        <link rel="preconnect" href="https://collectorshub.matomo.cloud" />
        <link rel="dns-prefetch" href="https://collectorshub.matomo.cloud" />

        <link rel="preload" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'" />
        <noscript><link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /></noscript>

        <script type="application/ld+json">
        @php
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'Collector\'s Hub',
            'url' => 'https://collectorshub.fr',
            'description' => 'La plateforme ultime pour les collectionneurs - CollectorsHub',
            'headline' => 'Collector\'s Hub - La plateforme ultime pour les collectionneurs',
            'alternativeHeadline' => 'CollectorsHub - Rejoignez l\'aventure ultime',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => 'https://collectorshub.fr/search?q={search_term_string}',
                'query-input' => 'required name=search_term_string'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Collector\'s Hub',
                'url' => 'https://collectorshub.fr'
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'EUR',
                'availability' => 'https://schema.org/InStock'
            ],
            'breadcrumb' => [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Accueil',
                        'item' => 'https://collectorshub.fr'
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => 'Collector\'s Hub'
                    ]
                ]
            ]
        ];
        @endphp
        {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
        @routes
        @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
        <script>
            var _paq = window._paq = window._paq || [];
            _paq.push(['trackPageView']);
            _paq.push(['enableLinkTracking']);
            (function() {
                var u="https://collectorshub.matomo.cloud/";
                _paq.push(['setTrackerUrl', u+'matomo.php']);
                _paq.push(['setSiteId', '1']);
                var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
                g.async=true; g.defer=true; g.src='https://cdn.matomo.cloud/collectorshub.matomo.cloud/matomo.js'; s.parentNode.insertBefore(g,s);
            })();
        </script>
    </head>
    <body class="font-sans antialiased">
        @inertia

        <script>
            window.Laravel = {
                csrfToken: '{{ csrf_token() }}'
                @if(session('matomo_event'))
                , matomoEvent: @json(session('matomo_event'))
                @endif
            };
        </script>
    </body>
</html>
