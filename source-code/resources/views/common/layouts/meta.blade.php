@php
    $appName    = getOption('app_name', 'JCP Property Management');
    $metaDesc   = getOption('meta_description', 'Comprehensive property management platform for shareholders, owners, and tenants.');
    $metaKw     = getOption('meta_keyword', '');
    $metaAuthor = getOption('meta_author', $appName);
    $ogImage    = getSettingImage('app_logo');
    $canonical  = url()->current();
    $fullTitle  = isset($pageTitle) && $pageTitle ? $appName . ' – ' . $pageTitle : $appName;
    $robots     = ($indexable ?? false) ? 'index, follow' : 'noindex, nofollow';
@endphp
<meta name="description" content="{{ $metaDesc }}">
@if($metaKw)
<meta name="keywords" content="{{ $metaKw }}">
@endif
<meta name="author" content="{{ $metaAuthor }}">
<meta name="robots" content="{{ $robots }}">
<meta name="referrer" content="no-referrer-when-downgrade">
<link rel="canonical" href="{{ $canonical }}">

<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $appName }}">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $metaDesc }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:url" content="{{ $canonical }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ $metaDesc }}">
<meta name="twitter:image" content="{{ $ogImage }}">

<meta name="msapplication-TileImage" content="{{ $ogImage }}">
<meta name="msapplication-TileColor" content="#3686FC">
<meta name="theme-color" content="#3686FC">
