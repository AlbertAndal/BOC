<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bureau of Customs | Client Issue Tracking & Resolution Management System</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/boc-logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Merriweather:wght@400;700;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        boc: {
                            blue: '#002B49',
                            darkblue: '#001A2C',
                            navy: '#003366',
                            accent: '#0D47A1',
                            gold: '#F2A900',
                            goldhover: '#D48800',
                            lightgold: '#FFF8E7',
                            graybg: '#F8FAFC',
                            red: '#C62828',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        serif: ['Merriweather', 'Georgia', 'serif'],
                    }
                }
            }
        }
    </script>

    <!-- Flowbite CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

    <style>
        .boc-pattern {
            background-color: #002B49;
            background-image: radial-gradient(#0A3B66 1px, transparent 1px), radial-gradient(#0A3B66 1px, #002B49 1px);
            background-size: 40px 40px;
            background-position: 0 0, 20px 20px;
        }
        .boc-gold-gradient {
            background: linear-gradient(135deg, #F2A900 0%, #D48800 100%);
        }
        .boc-border-gold {
            border-color: #F2A900;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased flex flex-col min-h-screen">

    <!-- Top GovPH Official Bar -->
    <div class="bg-slate-900 text-slate-300 text-xs py-1.5 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center gap-1.5 text-slate-300 font-medium">
                    <!-- Philippine Flag Symbol -->
                    <span class="w-4 h-2.5 inline-block rounded-xs overflow-hidden border border-slate-700 bg-white relative">
                        <span class="block h-1/2 bg-blue-700"></span>
                        <span class="block h-1/2 bg-red-600"></span>
                    </span>
                    GOVPH
                </span>
                <span class="hidden md:inline text-slate-600">&bull;</span>
                <span class="hidden md:inline text-slate-400">Department of Finance</span>
            </div>
            <div class="flex items-center space-x-4 text-[11px]">
                <span id="philippine-standard-time" class="text-slate-400 font-mono">
                    Philippine Standard Time: {{ now()->timezone('Asia/Manila')->format('l, d F Y | h:i A') }} PHT
                </span>
                <span class="hidden sm:inline text-slate-600">|</span>
                <a href="#about" class="hover:text-amber-400 transition-colors hidden sm:inline">Citizen's Charter</a>
                <a href="#services" class="hover:text-amber-400 transition-colors hidden sm:inline">Transparency Seal</a>
            </div>
        </div>
    </div>

    <!-- Main Official Bureau Header -->
    <header class="bg-white border-b-4 border-boc-gold sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-3">
                <!-- BOC Brand and Logo -->
                <a href="{{ url('/') }}" class="flex items-center space-x-3.5 group">
                    <img src="{{ asset('images/boc-logo.png') }}" 
                         alt="Bureau of Customs Official Seal" 
                         class="h-16 w-auto object-contain drop-shadow-sm group-hover:scale-105 transition-transform duration-200"
                         onerror="this.src='https://customs.gov.ph/wp-content/uploads/2021/04/boc-logo.png'">
                    <div class="border-l-2 border-slate-200 pl-3.5">
                        <span class="block text-xs font-semibold text-boc-goldhover uppercase tracking-wider">
                            Client Issue Tracking & Resolution Management System
                        </span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden lg:flex items-center space-x-1 font-medium text-sm text-slate-700">
                    <a href="#home" class="px-3 py-2 rounded-md hover:text-boc-blue hover:bg-slate-100 transition-colors">Home</a>
                    <a href="#about" class="px-3 py-2 rounded-md hover:text-boc-blue hover:bg-slate-100 transition-colors">About</a>
                    <a href="#services" class="px-3 py-2 rounded-md hover:text-boc-blue hover:bg-slate-100 transition-colors">Services</a>
                    <a href="#contact" class="px-3 py-2 rounded-md hover:text-boc-blue hover:bg-slate-100 transition-colors">Helpdesk</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="hidden sm:flex items-center space-x-3">
                    @auth
                        <a href="{{ url('/admin') }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-boc-blue hover:bg-boc-darkblue rounded-md shadow-sm transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Access Portal
                        </a>
                    @else
                        <a href="{{ url('/login') }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-semibold text-boc-blue bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded-md transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-1.5 text-boc-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Login
                        </a>
                        <a href="{{ url('/admin/register') }}" 
                                    class="inline-flex h-9.5 items-center whitespace-nowrap px-4 py-2 text-sm font-semibold text-slate-900 boc-gold-gradient hover:opacity-90 rounded-md shadow-sm transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            Create Account
                        </a>
                    @endauth
                </div>

                <!-- Mobile Hamburger Menu Button -->
                <button data-collapse-toggle="mobile-menu" type="button" class="lg:hidden inline-flex items-center p-2 text-slate-500 rounded-lg hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-boc-blue" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Navigation Accordion -->
            <div class="hidden lg:hidden border-t border-slate-200 py-3" id="mobile-menu">
                <div class="flex flex-col space-y-2 pb-3">
                    <a href="#home" class="px-3 py-2 text-slate-800 font-medium rounded-md hover:bg-slate-100">Home</a>
                    <a href="#about" class="px-3 py-2 text-slate-800 font-medium rounded-md hover:bg-slate-100">About</a>
                    <a href="#services" class="px-3 py-2 text-slate-800 font-medium rounded-md hover:bg-slate-100">Services</a>
                    <a href="#contact" class="px-3 py-2 text-slate-800 font-medium rounded-md hover:bg-slate-100">Helpdesk</a>
                </div>
                <div class="pt-3 border-t border-slate-200 flex flex-col gap-2">
                    @auth
                        <a href="{{ url('/admin') }}" class="w-full text-center py-2.5 font-bold text-white bg-boc-blue rounded-md">Access Portal</a>
                    @else
                        <a href="{{ url('/login') }}" class="w-full text-center py-2.5 font-semibold text-boc-blue bg-slate-100 border border-slate-300 rounded-md">Login</a>
                        <a href="{{ url('/register') }}" class="w-full text-center py-2.5 font-bold text-slate-900 boc-gold-gradient rounded-md">Create Account</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="relative boc-pattern text-white overflow-hidden py-16 lg:py-24 border-b-8 border-boc-gold">
        <!-- Subtle BOC Flag Accent Stripes on background -->
        <div class="absolute inset-0 opacity-15 pointer-events-none">
            <div class="absolute -right-20 -top-20 w-96 h-96 rounded-full bg-boc-gold filter blur-3xl"></div>
            <div class="absolute right-1/3 -bottom-20 w-80 h-80 rounded-full bg-blue-600 filter blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left Hero Content -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black font-serif tracking-tight text-white leading-tight">
                        <span class="text-white">Track. Coordinate. Resolve.</span>
                    </h1>

                    <p class="text-base sm:text-lg lg:text-xl text-slate-200 leading-relaxed font-normal max-w-2xl mx-auto lg:mx-0">
                        The official centralized web-based platform of the Bureau of Customs for managing client concerns, enhancing trade facilitation, and ensuring transparent public service.
                    </p>

                </div>

                <!-- Right Hero Feature Display / Portal Card -->
                <div class="lg:col-span-5">
                    <div class="bg-slate-900/80 backdrop-blur-md rounded-2xl border-2 border-boc-gold/60 p-6 sm:p-8 shadow-2xl relative">
                        <div class="absolute -top-3.5 right-6 px-3 py-1 bg-boc-gold text-slate-950 text-xs font-black rounded-full uppercase tracking-wider shadow">
                            Official Service
                        </div>

                        <div class="flex items-center space-x-3 mb-5 pb-4 border-b border-slate-700">
                            <div class="w-12 h-12 rounded-xl bg-boc-blue flex items-center justify-center border border-boc-gold/40">
                                <svg class="w-6 h-6 text-boc-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Central Resolution Desk</h3>
                                <p class="text-xs text-slate-400">Inter-Office Coordination & Tracking</p>
                            </div>
                        </div>

                        <!-- Sample Barcode & Issue Preview Badge -->
                        <div class="bg-slate-950/70 rounded-xl p-4 border border-slate-800 mb-5 space-y-2.5">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-400">Sample Reference Number</span>
                                <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-mono font-bold text-[11px]">ACTIVE RESOLUTION</span>
                            </div>
                            <div class="font-mono text-xl font-bold tracking-wider text-amber-300 flex items-center justify-between">
                                <span>TIO-09-042</span>
                                <span class="text-xs text-slate-400 font-sans font-normal">Port of Manila (POM)</span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-boc-gold h-1.5 rounded-full" style="width: 70%"></div>
                            </div>
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span>Step 7: BOC Office Coordination</span>
                                <span class="text-amber-300 font-semibold">In Progress</span>
                            </div>
                        </div>

                        <!-- Fast Feature Bullets -->
                        <ul class="space-y-2.5 text-xs text-slate-300 mb-6">
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-emerald-400 mr-2 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Automated unique barcode generation for every lodged concern</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-emerald-400 mr-2 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Multi-office routing between Assessment, Assessment Div, and Port Operations</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-emerald-400 mr-2 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Official PDF and Excel summary generation for trade compliance audits</span>
                            </li>
                        </ul>

                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <a href="{{ url('/login') }}" class="w-full text-center py-2.5 text-xs font-bold rounded-lg text-white bg-boc-blue hover:bg-boc-darkblue border border-boc-gold/30 transition-colors">
                                Sign In Now
                            </a>
                            <a href="{{ url('/register') }}" class="w-full text-center py-2.5 text-xs font-bold rounded-lg text-slate-900 bg-boc-gold hover:bg-boc-goldhover transition-colors">
                                Register as Broker
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- System Roles Section (Mandated Requirement) -->
    <section id="roles" class="py-16 lg:py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest text-boc-goldhover bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                    Role-Based Access Control
                </span>
                <h2 class="text-3xl sm:text-4xl font-bold font-serif text-boc-blue mt-3">
                    Tailored Systems for Key Stakeholders
                </h2>
                <p class="text-slate-600 mt-3 text-sm sm:text-base">
                    The platform enforces stringent security and segregated permissions to streamline workflow between trade brokers, technical handlers, and executive managers.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Role 1: Customs Broker -->
                <div class="bg-white rounded-2xl p-8 border-2 border-slate-200 hover:border-boc-gold shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-amber-100 group-hover:bg-boc-gold text-boc-blue group-hover:text-slate-900 flex items-center justify-center transition-colors mb-6 shadow-sm">
                            <!-- Broker Badge Icon -->
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="inline-block text-xs font-bold uppercase tracking-wider text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded mb-2">
                            Stakeholder Role
                        </div>
                        <h3 class="text-xl font-bold text-boc-blue font-serif mb-3">Customs Broker</h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-6">
                            Accredited customs brokers and authorized trade representatives can directly lodge client concerns, upload shipment documents, and track resolution timelines in real-time.
                        </p>
                        <ul class="space-y-2.5 text-xs text-slate-700 font-medium mb-6">
                            <li class="flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-boc-gold mr-2.5"></span>
                                Submit new trade & clearance concerns
                            </li>
                            <li class="flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-boc-gold mr-2.5"></span>
                                Upload supporting invoices & BOL attachments
                            </li>
                            <li class="flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-boc-gold mr-2.5"></span>
                                Monitor unique barcode status (e.g. TIO-09-xxx)
                            </li>
                            <li class="flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-boc-gold mr-2.5"></span>
                                Transparent timeline of official actions taken
                            </li>
                        </ul>
                    </div>
                    <div class="pt-4 border-t border-slate-100">
                        <a href="{{ url('/register') }}" class="inline-flex items-center text-xs font-bold text-boc-blue hover:text-boc-gold transition-colors">
                            Register as Broker &rarr;
                        </a>
                    </div>
                </div>

                <!-- Role 2: Accounts Manager -->
                <div class="bg-white rounded-2xl p-8 border-2 border-boc-blue relative shadow-md hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    <div class="absolute -top-3 left-8 px-3 py-0.5 bg-boc-blue text-white text-[11px] font-bold rounded-full uppercase tracking-wider">
                        Operational Core
                    </div>
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-blue-100 group-hover:bg-boc-blue text-boc-blue group-hover:text-white flex items-center justify-center transition-colors mb-6 shadow-sm">
                            <!-- Specialist Icon -->
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div class="inline-block text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded mb-2">
                            BOC Specialist
                        </div>
                        <h3 class="text-xl font-bold text-boc-blue font-serif mb-3">Accounts Manager</h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-6">
                            Dedicated BOC resolution managers assigned to evaluate technical concerns, coordinate with assessment units and port offices, and advance cases through the 10-step pipeline.
                        </p>
                        <ul class="space-y-2.5 text-xs text-slate-700 font-medium mb-6">
                            <li class="flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-2.5"></span>
                                Access cases assigned specifically to portfolio
                            </li>
                            <li class="flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-2.5"></span>
                                Advance workflow through 10 resolution steps
                            </li>
                            <li class="flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-2.5"></span>
                                Coordinate directly with BOC offices & ports
                            </li>
                            <li class="flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-2.5"></span>
                                Maintain prompt resolution SLAs & follow-ups
                            </li>
                        </ul>
                    </div>
                    <div class="pt-4 border-t border-slate-100">
                        <a href="{{ url('/login') }}" class="inline-flex items-center text-xs font-bold text-boc-blue hover:text-boc-gold transition-colors">
                            Manager Portal &rarr;
                        </a>
                    </div>
                </div>

                <!-- Role 3: Administrator -->
                <div class="bg-white rounded-2xl p-8 border-2 border-slate-200 hover:border-boc-gold shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-amber-100 group-hover:bg-boc-gold text-boc-blue group-hover:text-slate-900 flex items-center justify-center transition-colors mb-6 shadow-sm">
                            <!-- Shield / Admin Icon -->
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div class="inline-block text-xs font-bold uppercase tracking-wider text-slate-700 bg-slate-100 px-2.5 py-0.5 rounded mb-2">
                            Executive Authority
                        </div>
                        <h3 class="text-xl font-bold text-boc-blue font-serif mb-3">Administrator</h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-6">
                            Executive leadership and supervisory officers hold full oversight of the nationwide resolution matrix, vetting new registrations, assigning managers, and exporting reports.
                        </p>
                        <ul class="space-y-2.5 text-xs text-slate-700 font-medium mb-6">
                            <li class="flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-900 mr-2.5"></span>
                                Full CRUD over Ports, Offices, Users, & Importers
                            </li>
                            <li class="flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-900 mr-2.5"></span>
                                User security approval for newly registered accounts
                            </li>
                            <li class="flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-900 mr-2.5"></span>
                                Case intake dispatch to designated Accounts Managers
                            </li>
                            <li class="flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-900 mr-2.5"></span>
                                Comprehensive PDF & Excel multi-metric reporting
                            </li>
                        </ul>
                    </div>
                    <div class="pt-4 border-t border-slate-100">
                        <a href="{{ url('/login') }}" class="inline-flex items-center text-xs font-bold text-boc-blue hover:text-boc-gold transition-colors">
                            Executive Console &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 10-Step Resolution Workflow Section -->
    <section id="workflow" class="py-16 lg:py-24 bg-white border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest text-boc-blue bg-blue-50 px-3 py-1 rounded-full border border-blue-200">
                    Standard Operating Procedure
                </span>
                <h2 class="text-3xl sm:text-4xl font-bold font-serif text-boc-blue mt-3">
                    The 10-Step Issue Resolution Workflow
                </h2>
                <p class="text-slate-600 mt-3 text-sm sm:text-base">
                    Every filed concern transitions through a rigorous, accountable, and auditable 10-step lifecycle to guarantee expeditious and compliant case disposition.
                </p>
            </div>

            <!-- Workflow Visual Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @php
                    $steps = [
                        ['num' => '01', 'title' => 'Concern Submission', 'desc' => 'Customs Broker files concern with reference & documents.'],
                        ['num' => '02', 'title' => 'Intake & Verification', 'desc' => 'Admin validates completeness and verifies importer record.'],
                        ['num' => '03', 'title' => 'Manager Assignment', 'desc' => 'Dedicated Accounts Manager assigned based on port/category.'],
                        ['num' => '04', 'title' => 'Initial Evaluation', 'desc' => 'Manager reviews technical tariff & procedural merits.'],
                        ['num' => '05', 'title' => 'BOC Office Coordination', 'desc' => 'Formal referral sent to relevant BOC office/assessment unit.'],
                        ['num' => '06', 'title' => 'Assessment & Action', 'desc' => 'Office provides technical findings and disposition.'],
                        ['num' => '07', 'title' => 'Client Clarification', 'desc' => 'Broker provides supplemental information if required.'],
                        ['num' => '08', 'title' => 'Draft Resolution', 'desc' => 'Manager formulates official resolution and findings.'],
                        ['num' => '09', 'title' => 'Executive Concurrence', 'desc' => 'Supervisor/Admin validates and approves proposed resolution.'],
                        ['num' => '10', 'title' => 'Closure & Audit', 'desc' => 'Case officially closed with complete immutable audit trail.'],
                    ];
                @endphp

                @foreach($steps as $step)
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 hover:border-boc-gold hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="w-8 h-8 rounded-lg bg-boc-blue text-amber-400 font-mono font-bold text-xs flex items-center justify-center">
                                    {{ $step['num'] }}
                                </span>
                                <span class="text-[10px] font-semibold text-slate-400 uppercase">Stage {{ $loop->iteration }}</span>
                            </div>
                            <h4 class="font-bold text-slate-900 text-sm mb-1.5">{{ $step['title'] }}</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">{{ $step['desc'] }}</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-200/70 flex items-center text-[10px] text-boc-blue font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                            Logged in Audit Trail
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 lg:py-24 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-6">
                    <span class="text-xs font-bold uppercase tracking-widest text-boc-blue bg-blue-50 px-3 py-1 rounded-full border border-blue-200">
                        About the Agency
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-bold font-serif text-boc-blue mt-3 mb-6 leading-tight">
                        Bureau of Customs: Modernized, Credible, and Trade-Facilitating
                    </h2>
                    <div class="space-y-4 text-slate-600 text-sm sm:text-base leading-relaxed">
                        <p>
                            The <strong>Bureau of Customs (BOC)</strong> is a lead agency under the Department of Finance tasked with customs administration, revenue collection, border protection, and the facilitation of international commerce.
                        </p>
                        <p>
                            Through this centralized <strong>Client Issue Tracking & Resolution Management System</strong>, the Bureau operationalizes its commitment to Section 103 of the Customs Modernization and Tariff Act (RA 10863) and Republic Act No. 11032 (Ease of Doing Business and Efficient Government Service Delivery Act of 2018).
                        </p>
                        <p>
                            Clients and stakeholders benefit from accountable response times, direct escalation matrices, and structured inter-agency dispute resolution.
                        </p>
                    </div>

                    <div class="mt-8 grid grid-cols-2 gap-4">
                        <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                            <span class="block text-boc-blue font-bold text-sm">Vision</span>
                            <span class="text-xs text-slate-600">A modernized and credible customs administration among the world's best.</span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                            <span class="block text-boc-blue font-bold text-sm">Mission</span>
                            <span class="text-xs text-slate-600">To strengthen border control, enhance trade facilitation, and improve revenue collection.</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-6">
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl border-4 border-boc-gold/30">
                        <img src="{{ asset('images/boc-cover.png') }}" 
                             alt="Bureau of Customs Headquarters and Port Operations" 
                             class="w-full h-auto object-cover max-h-115"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div class="hidden bg-boc-blue text-white p-12 text-center">
                            <img src="{{ asset('images/boc-logo.png') }}" class="h-24 mx-auto mb-4" alt="BOC Logo">
                            <h3 class="text-2xl font-serif font-bold text-boc-gold">Bureau of Customs</h3>
                            <p class="text-slate-300 text-sm mt-2">South Harbor, Port Area, Manila, Philippines</p>
                        </div>
                        <div class="absolute bottom-0 inset-x-0 bg-linear-to-t from-slate-950/90 via-slate-900/60 to-transparent p-6 text-white">
                            <span class="text-xs uppercase tracking-widest text-amber-400 font-bold">Public Service Excellence</span>
                            <p class="text-sm font-semibold mt-1">Ensuring Swift, Transparent, and Professional Resolution of Every Cargo Inquiry.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Helpdesk / Contact Section -->
    <section id="contact" class="py-16 bg-slate-900 text-white border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-center">
                <div class="lg:col-span-2 space-y-3">
                    <span class="text-xs font-bold uppercase tracking-widest text-boc-gold">Bureau of Customs Helpdesk & CARES</span>
                    <h3 class="text-2xl sm:text-3xl font-bold font-serif">Have an urgent customs clearance inquiry?</h3>
                    <p class="text-slate-300 text-sm leading-relaxed max-w-2xl">
                        Authorized brokers with active registered accounts may file a ticket directly through the portal. For general public inquiries, reach out through the official BOC CARES communication channels.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row lg:flex-col gap-3">
                    <a href="tel:+63287056000" class="inline-flex justify-center items-center px-6 py-3.5 text-sm font-semibold text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-lg text-center">
                        <svg class="w-4 h-4 mr-2 text-boc-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        BOC Hotline: (02) 8705-6000
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Official GovPH Footer -->
    <footer class="bg-slate-950 text-slate-400 text-xs border-t-4 border-boc-gold py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                <!-- Col 1: BOC Info -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('images/boc-logo.png') }}" class="h-10 w-auto" alt="BOC Seal">
                    </div>
                    <p class="text-slate-400 text-xs leading-relaxed">
                        South Harbor, Gate 3, Port Area, Manila 1018, Philippines
                    </p>
                    <p class="text-slate-400 text-xs">
                        Email: <a href="mailto:boc.cares@customs.gov.ph" class="text-amber-400 hover:underline">boc.cares@customs.gov.ph</a>
                    </p>
                    <p class="text-slate-400 text-xs">
                        Website: <a href="https://customs.gov.ph" target="_blank" class="text-amber-400 hover:underline">customs.gov.ph</a>
                    </p>
                </div>

                <!-- Col 2: Quick Links -->
                <div>
                    <h4 class="font-bold text-white uppercase tracking-wider text-xs mb-3">Portal Navigation</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#home" class="hover:text-white transition-colors">Home Page</a></li>
                        <li><a href="{{ url('/login') }}" class="hover:text-white transition-colors">Portal Login</a></li>
                        <li><a href="{{ url('/register') }}" class="hover:text-white transition-colors">Broker Account Registration</a></li>
                        <li><a href="#workflow" class="hover:text-white transition-colors">10-Step Resolution Workflow</a></li>
                        <li><a href="#roles" class="hover:text-white transition-colors">User Roles & Access Matrix</a></li>
                    </ul>
                </div>

            </div>

            <div class="pt-8 border-t border-slate-800 text-center sm:flex sm:justify-between sm:text-left text-slate-500 text-[11px]">
                <p>&copy; {{ date('Y') }} All Rights Reserved.</p>
                <p class="mt-2 sm:mt-0">Trade Facilitation, Valuation, and Client Resolution Portal v1.0</p>
            </div>
        </div>
    </footer>

</body>
</html>
