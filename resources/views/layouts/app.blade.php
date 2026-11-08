{{-- Shared shell: theme styles, icon symbols, navigation, and flash messages. --}}
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#102b22">
    <title>@yield('title', 'LibTrack | Library, thoughtfully organized')</title>
    <style>
        /* Shared palette and raised/inset shadows keep the interface visually consistent. */
        :root {
            color-scheme: light;
            --ink: #14231d;
            --ink-soft: #52645a;
            --green: #176b4c;
            --green-dark: #102b22;
            --green-bright: #39a879;
            --surface: #edf3ef;
            --surface-white: #fbfdfb;
            --line: #dce7df;
            --shadow-raised: 10px 12px 25px #d5dfd8, -9px -9px 22px #ffffff;
            --shadow-inset: inset 4px 4px 9px #d6e0d9, inset -4px -4px 9px #ffffff;
            --radius: 22px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            min-width: 320px;
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: radial-gradient(ellipse at 10% 0%, #f9fcfa 0, transparent 38%),
                linear-gradient(145deg, #f2f7f3 0%, #e7efea 55%, #f4f8f5 100%);
            color: var(--ink);
            font: 15px/1.6 'Instrument Sans', Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        a {
            color: var(--green);
        }

        a:focus-visible,
        button:focus-visible,
        input:focus-visible,
        select:focus-visible,
        textarea:focus-visible {
            outline: 3px solid #5ac493;
            outline-offset: 3px;
        }

        .icon-library {
            position: absolute;
            width: 0;
            height: 0;
            overflow: hidden;
        }

        .icon {
            width: 18px;
            height: 18px;
            flex: 0 0 18px;
            display: inline-block;
        }

        /* Brand and navigation share one responsive header row. */
        .site-header {
            position: relative;
            z-index: 1;
            color: #f5fbf7;
            background: linear-gradient(112deg, #0d241c 0%, #14392c 55%, #1d6245 100%);
            box-shadow: 0 12px 30px #0d241c2b;
        }

        .header-inner {
            width: min(1180px, calc(100% - 40px));
            min-height: 78px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 22px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: #fff;
            font-size: 18px;
            font-weight: 750;
            letter-spacing: -.035em;
            text-decoration: none;
            white-space: nowrap;
        }

        .brand-mark {
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            border: 1px solid #ffffff35;
            border-radius: 14px;
            background: linear-gradient(145deg, #ffffff24, #ffffff0a);
            box-shadow: inset 1px 1px 4px #ffffff25, 5px 6px 14px #07180f40;
            color: #a9f1cc;
        }

        .brand-mark .icon {
            width: 21px;
            height: 21px;
        }

        .header-nav {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 7px;
        }

        .nav-link {
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border: 1px solid transparent;
            border-radius: 13px;
            color: #e2f0e8;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background .18s ease, border-color .18s ease, color .18s ease;
        }

        .nav-link:hover,
        .nav-link.is-active {
            border-color: #ffffff20;
            background: #ffffff14;
            color: #fff;
        }

        .nav-link .icon {
            width: 16px;
            height: 16px;
            flex-basis: 16px;
            color: #9ee4bb;
        }

        .header-user {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-left: 5px;
            padding: 6px 10px;
            border-left: 1px solid #ffffff25;
            color: #c8ddd0;
            font-size: 13px;
        }

        .header-user .icon {
            width: 16px;
            height: 16px;
            flex-basis: 16px;
        }

        main {
            width: min(1120px, calc(100% - 40px));
            min-height: calc(100vh - 155px);
            margin: 0 auto;
            padding: 42px 0 64px;
        }

        h1,
        h2,
        h3,
        p {
            margin-top: 0;
        }

        h1 {
            margin-bottom: 8px;
            color: #14251d;
            font-size: clamp(28px, 4vw, 38px);
            font-weight: 750;
            letter-spacing: -.05em;
            line-height: 1.15;
        }

        h2 {
            color: #1b3025;
            font-size: 21px;
            letter-spacing: -.025em;
        }

        h3 {
            margin-bottom: 7px;
            font-size: 18px;
            letter-spacing: -.02em;
            line-height: 1.3;
        }

        p {
            margin-bottom: 14px;
        }

        .page-heading {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 26px;
        }

        .page-heading p {
            margin: 0;
        }

        .eyebrow {
            margin-bottom: 8px;
            color: var(--green);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .15em;
            text-transform: uppercase;
        }

        .muted {
            color: var(--ink-soft);
        }

        /* Raised surfaces and inset fields establish the neomorphic component system. */
        .card {
            min-width: 0;
            margin: 16px 0;
            padding: 23px;
            border: 1px solid #ffffffc9;
            border-radius: var(--radius);
            background: linear-gradient(145deg, #f8fbf9 0%, #edf3ef 100%);
            box-shadow: var(--shadow-raised);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 230px), 1fr));
            gap: 18px;
        }

        .grid>.card {
            margin: 0;
        }

        .catalog-tools {
            margin-bottom: 26px;
        }

        .catalog-tools .grid {
            align-items: end;
            gap: 14px;
        }

        .catalog-grid {
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 245px), 1fr));
        }

        .book-card {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .book-card .cover {
            margin-bottom: 12px;
        }

        .book-card .card-actions {
            margin-top: auto;
            padding-top: 14px;
        }

        .cover {
            width: 100%;
            height: 165px;
            display: block;
            border: 1px solid #dce7df;
            border-radius: 16px;
            object-fit: cover;
            background: linear-gradient(145deg, #e2eee7, #f9fcfa);
        }

        .availability {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--ink-soft);
            font-size: 13px;
        }

        .availability-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #39a879;
            box-shadow: 0 0 0 4px #39a87920;
        }

        .availability-dot.is-empty {
            background: #a95445;
            box-shadow: 0 0 0 4px #a9544520;
        }

        label {
            display: inline-block;
            margin-bottom: 5px;
            color: #34483c;
            font-size: 13px;
            font-weight: 700;
        }

        input,
        select,
        textarea {
            width: 100%;
            min-height: 46px;
            margin: 0 0 15px;
            padding: 11px 14px;
            border: 1px solid #d5e1d9;
            border-radius: 13px;
            background: linear-gradient(145deg, #eaf0ec, #f7faf8);
            box-shadow: var(--shadow-inset);
            color: var(--ink);
            font: inherit;
            transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }

        input::placeholder,
        textarea::placeholder {
            color: #87958c;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #6db990;
            background: #f8fcf9;
            box-shadow: inset 2px 2px 5px #dce6df, 0 0 0 4px #42a5751c;
            outline: none;
        }

        input[type="file"] {
            padding: 8px;
        }

        input[type="file"]::file-selector-button {
            margin-right: 10px;
            padding: 7px 11px;
            border: 0;
            border-radius: 9px;
            background: #dce9e0;
            color: #28573d;
            font: inherit;
            font-weight: 650;
            cursor: pointer;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .form-actions,
        .button-row,
        .card-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .button-row>input {
            width: auto;
            min-width: 180px;
            flex: 1 1 220px;
            margin: 0;
        }

        button,
        .btn {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px 15px;
            border: 1px solid #24754f;
            border-radius: 13px;
            background: linear-gradient(135deg, #25845a 0%, #14593c 100%);
            box-shadow: 5px 6px 12px #cbd8cf, -4px -4px 10px #ffffff;
            color: #fff;
            cursor: pointer;
            font: inherit;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.2;
            text-decoration: none;
            transition: transform .16s ease, box-shadow .16s ease, filter .16s ease;
        }

        button:hover,
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 6px 8px 15px #c2d1c7, -4px -4px 10px #ffffff;
            filter: brightness(1.06);
        }

        button:active,
        .btn:active {
            transform: translateY(0);
            box-shadow: var(--shadow-inset);
        }

        .btn .icon,
        button .icon {
            width: 16px;
            height: 16px;
            flex-basis: 16px;
        }

        .secondary {
            border-color: #bdcec2;
            background: linear-gradient(135deg, #f9fcfa, #e3ece6);
            color: #284b37;
        }

        .danger {
            border-color: #8b4039;
            background: linear-gradient(135deg, #ad5b50, #773a34);
            color: #fff;
        }

        .inline {
            display: inline-flex;
            align-items: center;
            margin: 0;
        }

        .inline button {
            min-height: 36px;
            padding: 7px 11px;
        }

        .action-stack {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 9px;
        }

        .action-stack form {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }

        .table-wrap {
            overflow-x: auto;
            overscroll-behavior-x: contain;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        th,
        td {
            padding: 13px 12px;
            border-bottom: 1px solid #dce6df;
            text-align: left;
            vertical-align: middle;
        }

        th {
            color: #52645a;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .09em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        tbody tr:hover td {
            background: #eaf2ec80;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            position: absolute;
            right: -24px;
            bottom: -40px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(145deg, #44ad7a25, #123d2910);
            content: '';
        }

        .stat-value {
            margin: 0;
            color: var(--green-dark);
            font-size: 34px;
            font-weight: 800;
            letter-spacing: -.055em;
        }

        /* Monitoring cards give the admin's operational metrics visual priority. */
        .monitor-overview {
            margin-bottom: 23px;
            padding: 23px 26px;
            border: 1px solid #327957;
            border-radius: var(--radius);
            background: linear-gradient(118deg, #10281f 0%, #16452f 62%, #20754c 100%);
            box-shadow: 12px 15px 28px #cbd8cf, -7px -7px 18px #ffffff;
            color: #f1f8f3;
        }

        .monitor-overview h2 {
            margin: 0 0 5px;
            color: #fff;
            font-size: 22px;
        }

        .monitor-overview p {
            margin: 0;
            color: #c0d9c9;
        }

        .monitor-overview-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .monitor-overview-detail {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px 20px;
            margin-top: 17px;
            padding-top: 15px;
            border-top: 1px solid #ffffff24;
            color: #d1e4d7;
            font-size: 12px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 11px;
            border: 1px solid #ffffff36;
            border-radius: 999px;
            background: #ffffff12;
            color: #f5fbf6;
            font-size: 12px;
            font-weight: 750;
        }

        .status-pill::before {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #80dda8;
            box-shadow: 0 0 0 4px #80dda825;
            content: '';
        }

        .status-pill.is-degraded::before {
            background: #ff9b82;
            box-shadow: 0 0 0 4px #ff9b8225;
        }

        .status-pill.is-stale::before {
            background: #f0c76d;
            box-shadow: 0 0 0 4px #f0c76d25;
        }

        .monitor-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 17px;
            margin: 18px 0 25px;
        }

        .monitor-card {
            display: flex;
            min-height: 175px;
            flex-direction: column;
            margin: 0;
        }

        .monitor-card-head {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #506359;
            font-size: 12px;
            font-weight: 750;
        }

        .monitor-card-head .icon {
            color: #28784e;
        }

        .monitor-value {
            margin: 17px 0 4px;
            color: #12271d;
            font-size: clamp(27px, 3vw, 36px);
            font-weight: 800;
            letter-spacing: -.06em;
            line-height: 1.1;
        }

        .monitor-note {
            margin: auto 0 0;
            padding-top: 9px;
            color: #68776e;
            font-size: 12px;
        }

        .monitor-progress {
            width: 100%;
            height: 10px;
            overflow: hidden;
            margin: 13px 0 7px;
            border: 1px solid #d4e0d7;
            border-radius: 999px;
            background: #e4ece6;
            box-shadow: var(--shadow-inset);
        }

        .monitor-progress>span {
            height: 100%;
            display: block;
            border-radius: inherit;
            background: linear-gradient(90deg, #4abb7d, #176542);
        }

        .monitor-progress.is-coverage>span {
            background: linear-gradient(90deg, #77bd91, #1d6440);
        }

        .monitor-card-storage {
            min-height: 175px;
        }

        .monitor-card-storage .monitor-value {
            font-size: 24px;
        }

        .library-summary {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin: 12px 0 24px;
        }

        .library-summary .card {
            padding: 17px 20px;
        }

        .library-summary .stat-value {
            font-size: 28px;
        }

        .alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .alert p:last-child {
            margin-bottom: 0;
        }

        .success {
            border-color: #b8ddc5;
            color: #1b6843;
        }

        .error {
            border-color: #e5c5bf;
            color: #8c372f;
        }

        .success .icon {
            color: #238353;
        }

        .error .icon {
            color: #a4483d;
        }

        .empty-state {
            padding: 26px;
            color: var(--ink-soft);
            text-align: center;
        }

        .empty-state .icon {
            width: 26px;
            height: 26px;
            margin-bottom: 8px;
            color: #3b8a61;
        }

        .auth-card {
            width: min(100%, 460px);
            margin: 30px auto;
        }

        .detail-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(260px, .75fr);
            gap: 24px;
        }

        .detail-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin: 22px 0;
        }

        .detail-item {
            padding: 14px;
            border: 1px solid #dce7df;
            border-radius: 14px;
            background: #edf3ef;
            box-shadow: var(--shadow-inset);
        }

        .detail-item strong {
            display: block;
            margin-bottom: 4px;
            color: var(--ink-soft);
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .recommendation-card {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .recommendation-mark {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: linear-gradient(145deg, #dcefe3, #f9fcfa);
            box-shadow: var(--shadow-raised);
            color: #24764d;
        }

        nav[role="navigation"] {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 22px 0;
            color: var(--ink-soft);
        }

        nav[role="navigation"]>div:first-child {
            display: none !important;
        }

        nav[role="navigation"]>div:last-child {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex: 1;
            gap: 10px;
        }

        nav[role="navigation"] a,
        nav[role="navigation"] span[aria-current] {
            min-width: 38px;
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 7px 11px;
            border: 1px solid #dbe6df;
            border-radius: 12px;
            background: linear-gradient(145deg, #f9fcfa, #e8f0ea);
            box-shadow: 4px 5px 10px #d4ded7, -3px -3px 8px #ffffff;
            color: #315441;
            font-size: 13px;
            text-decoration: none;
        }

        nav[role="navigation"] span[aria-current] {
            background: linear-gradient(135deg, #25845a, #14593c);
            color: white;
        }

        nav[role="navigation"] a:hover {
            border-color: #9cc8ac;
            color: #155c3d;
        }

        nav[role="navigation"] svg {
            width: 16px;
            height: 16px;
        }

        nav[role="navigation"] p {
            margin: 0;
            font-size: 13px;
        }

        footer {
            padding: 22px 20px 28px;
            color: #75847a;
            font-size: 12px;
            text-align: center;
        }

        /* Collapse navigation, cards, and detail layouts for narrower screens. */
        @media (max-width: 760px) {
            .header-inner {
                width: min(100% - 28px, 600px);
                align-items: flex-start;
                flex-direction: column;
                gap: 10px;
                padding: 14px 0;
            }

            .header-nav {
                justify-content: flex-start;
                gap: 4px;
            }

            .nav-link {
                min-height: 36px;
                padding: 7px 9px;
                font-size: 12px;
            }

            .header-user {
                margin-left: 0;
                padding-left: 8px;
            }

            main {
                width: min(100% - 28px, 600px);
                padding: 30px 0 46px;
            }

            .page-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .detail-layout {
                grid-template-columns: 1fr;
            }

            .monitor-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .library-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .monitor-overview-top {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        @media (max-width: 520px) {

            .form-grid,
            .detail-list {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .card {
                padding: 18px;
                border-radius: 18px;
            }

            .catalog-tools .grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .grid {
                gap: 14px;
            }

            .monitor-grid,
            .library-summary {
                grid-template-columns: 1fr;
            }

            .button-row,
            .form-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .button-row>*,
            .form-actions>*,
            .card-actions>* {
                width: 100%;
            }

            .inline {
                width: auto;
            }

            .table-wrap {
                margin: -4px -7px;
                padding: 0 7px;
            }

            th,
            td {
                padding: 11px 9px;
            }
        }

        @media (min-width: 640px) {
            nav[role="navigation"]>div:first-child {
                display: flex !important;
                align-items: center;
                justify-content: space-between;
                flex: 1;
                gap: 16px;
            }

            nav[role="navigation"]>div:last-child {
                display: none;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>

<body>
    <svg class="icon-library" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <symbol id="icon-book" viewBox="0 0 24 24">
            <path
                d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21V5.5Zm0 13A2.5 2.5 0 0 1 6.5 16H20M8 7h8M8 11h6"
                fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                stroke-width="1.7" />
        </symbol>
        <symbol id="icon-search" viewBox="0 0 24 24">
            <circle cx="10.8" cy="10.8" r="6.8" fill="none" stroke="currentColor" stroke-width="1.8" />
            <path d="m16 16 4.2 4.2" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8" />
        </symbol>
        <symbol id="icon-arrow" viewBox="0 0 24 24">
            <path d="M5 12h14m-6-6 6 6-6 6" fill="none" stroke="currentColor" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="1.8" />
        </symbol>
        <symbol id="icon-plus" viewBox="0 0 24 24">
            <path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8" />
        </symbol>
        <symbol id="icon-sparkle" viewBox="0 0 24 24">
            <path
                d="m12 3 1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3Zm7 12 .9 2.1L22 18l-2.1.9L19 21l-.9-2.1L16 18l2.1-.9L19 15Z"
                fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                stroke-width="1.6" />
        </symbol>
        <symbol id="icon-list" viewBox="0 0 24 24">
            <path d="M9 6h11M9 12h11M9 18h11M4.5 6h.01M4.5 12h.01M4.5 18h.01" fill="none" stroke="currentColor"
                stroke-linecap="round" stroke-width="2" />
        </symbol>
        <symbol id="icon-grid" viewBox="0 0 24 24">
            <rect x="4" y="4" width="6" height="6" rx="1.5" fill="none" stroke="currentColor"
                stroke-width="1.7" />
            <rect x="14" y="4" width="6" height="6" rx="1.5" fill="none" stroke="currentColor"
                stroke-width="1.7" />
            <rect x="4" y="14" width="6" height="6" rx="1.5" fill="none" stroke="currentColor"
                stroke-width="1.7" />
            <rect x="14" y="14" width="6" height="6" rx="1.5" fill="none" stroke="currentColor"
                stroke-width="1.7" />
        </symbol>
        <symbol id="icon-user" viewBox="0 0 24 24">
            <circle cx="12" cy="8" r="3.5" fill="none" stroke="currentColor" stroke-width="1.7" />
            <path d="M5 20a7 7 0 0 1 14 0" fill="none" stroke="currentColor" stroke-linecap="round"
                stroke-width="1.7" />
        </symbol>
        <symbol id="icon-logout" viewBox="0 0 24 24">
            <path d="M10 5H5v14h5m4-3 4-4-4-4m4 4H9" fill="none" stroke="currentColor" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="1.7" />
        </symbol>
        <symbol id="icon-check" viewBox="0 0 24 24">
            <path d="m5 12 4.5 4.5L19 7" fill="none" stroke="currentColor" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="2" />
        </symbol>
        <symbol id="icon-alert" viewBox="0 0 24 24">
            <path d="M12 3 2.8 20h18.4L12 3Z" fill="none" stroke="currentColor" stroke-linejoin="round"
                stroke-width="1.7" />
            <path d="M12 9v4m0 3h.01" fill="none" stroke="currentColor" stroke-linecap="round"
                stroke-width="1.8" />
        </symbol>
        <symbol id="icon-clock" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="8.5" fill="none" stroke="currentColor"
                stroke-width="1.7" />
            <path d="M12 7v5l3.2 2" fill="none" stroke="currentColor" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="1.7" />
        </symbol>
        <symbol id="icon-pulse" viewBox="0 0 24 24">
            <path d="M3 12h4l2.4-6 4.2 12 2.4-6H21" fill="none" stroke="currentColor" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="1.7" />
        </symbol>
        <symbol id="icon-users" viewBox="0 0 24 24">
            <circle cx="9" cy="8" r="3" fill="none" stroke="currentColor" stroke-width="1.7" />
            <path d="M3.5 20a5.5 5.5 0 0 1 11 0M16 5.5a3 3 0 0 1 0 5.8m1.2 3a5.2 5.2 0 0 1 3.3 4.8" fill="none"
                stroke="currentColor" stroke-linecap="round" stroke-width="1.7" />
        </symbol>
        <symbol id="icon-drive" viewBox="0 0 24 24">
            <rect x="3" y="5" width="18" height="14" rx="2.5" fill="none" stroke="currentColor"
                stroke-width="1.7" />
            <path d="M3 10h18m-4 5h.01m-3 0h.01" fill="none" stroke="currentColor" stroke-linecap="round"
                stroke-width="1.7" />
        </symbol>
        <symbol id="icon-refresh" viewBox="0 0 24 24">
            <path d="M20 7v5h-5M4.8 9A7.5 7.5 0 0 1 18 6l2 2M4 17v-5h5m10.2 3A7.5 7.5 0 0 1 6 18l-2-2" fill="none"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" />
        </symbol>
    </svg>

    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="{{ route('catalog') }}">
                <span class="brand-mark"><svg class="icon">
                        <use href="#icon-book" />
                    </svg></span>
                <span>LibTrack</span>
            </a>
            <nav class="header-nav" aria-label="Main navigation">
                @auth
                    <a class="nav-link {{ request()->routeIs('catalog', 'books.show') ? 'is-active' : '' }}"
                        href="{{ route('catalog') }}">
                        <svg class="icon">
                            <use href="#icon-book" />
                        </svg>Catalog
                    </a>
                    @if (auth()->user()->role === 'student')
                        <a class="nav-link {{ request()->routeIs('mine') ? 'is-active' : '' }}"
                            href="{{ route('mine') }}">
                            <svg class="icon">
                                <use href="#icon-list" />
                            </svg>My requests
                        </a>
                    @endif
                    @if (auth()->user()->role === 'librarian')
                        <a class="nav-link {{ request()->routeIs('lib.books.*') ? 'is-active' : '' }}"
                            href="{{ route('lib.books.index') }}">
                            <svg class="icon">
                                <use href="#icon-book" />
                            </svg>Books
                        </a>
                        <a class="nav-link {{ request()->routeIs('lib.categories.*') ? 'is-active' : '' }}"
                            href="{{ route('lib.categories.index') }}">
                            <svg class="icon">
                                <use href="#icon-grid" />
                            </svg>Categories
                        </a>
                        <a class="nav-link {{ request()->routeIs('lib.requests') ? 'is-active' : '' }}"
                            href="{{ route('lib.requests') }}">
                            <svg class="icon">
                                <use href="#icon-list" />
                            </svg>Requests
                        </a>
                    @endif
                    @if (auth()->user()->role === 'admin')
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}"
                            href="{{ route('admin.dashboard') }}">
                            <svg class="icon">
                                <use href="#icon-grid" />
                            </svg>Dashboard
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.activities') ? 'is-active' : '' }}"
                            href="{{ route('admin.activities') }}">
                            <svg class="icon">
                                <use href="#icon-list" />
                            </svg>Activity
                        </a>
                    @endif
                    <span class="header-user"><svg class="icon">
                            <use href="#icon-user" />
                        </svg>{{ auth()->user()->name }}</span>
                    <form class="inline" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="secondary" type="submit"><svg class="icon">
                                <use href="#icon-logout" />
                            </svg>Log out</button>
                    </form>
                @else
                    <a class="nav-link {{ request()->routeIs('login') ? 'is-active' : '' }}"
                        href="{{ route('login') }}">
                        <svg class="icon">
                            <use href="#icon-user" />
                        </svg>Sign in
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        @if (session('success'))
            <div class="card alert success" role="status">
                <svg class="icon">
                    <use href="#icon-check" />
                </svg>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if (session('recommendation'))
            <div class="card alert" role="status">
                <svg class="icon">
                    <use href="#icon-sparkle" />
                </svg>
                <div><strong>Recommendation</strong>
                    <p>{{ session('recommendation') }}</p>
                </div>
            </div>
        @endif
        @if ($errors->any())
            <div class="card alert error" role="alert">
                <svg class="icon">
                    <use href="#icon-alert" />
                </svg>
                <div>
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif
        @yield('content')
    </main>

</body>

</html>
