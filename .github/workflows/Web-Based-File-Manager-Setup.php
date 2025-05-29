<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setting Up a Web-Based File Manager on Linux Servers</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:'#3B82F6',secondary:'#10B981'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <meta name="description" content="A comprehensive, SEO-friendly guide to setting up a web-based file manager on Linux servers (Ubuntu, CentOS, RHEL) with File Browser, Nextcloud, and elFinder. Secure, modern, and responsive.">
    <meta name="keywords" content="web file manager, linux, ubuntu, centos, rhel, file browser, nextcloud, elFinder, tutorial, setup, nginx, https, docker, systemd, firewall, security">
    <meta name="author" content="MohdAkmal">
    <meta property="og:title" content="Setting Up a Web-Based File Manager on Linux Servers">
    <meta property="og:description" content="A comprehensive, SEO-friendly guide to setting up a web-based file manager on Linux servers (Ubuntu, CentOS, RHEL) with File Browser, Nextcloud, and elFinder.">
    <meta property="og:type" content="article">
    <meta property="og:image" content="https://readdy.ai/api/search-image?query=screenshot%20of%20File%20Browser%20web%20interface%20showing%20file%20listing%20with%20folders%20and%20files%2C%20clean%20modern%20UI&width=375&height=250&seq=1&orientation=landscape">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Setting Up a Web-Based File Manager on Linux Servers">
    <meta name="twitter:description" content="A comprehensive, SEO-friendly guide to setting up a web-based file manager on Linux servers (Ubuntu, CentOS, RHEL) with File Browser, Nextcloud, and elFinder.">
    <meta name="twitter:image" content="https://readdy.ai/api/search-image?query=screenshot%20of%20File%20Browser%20web%20interface%20showing%20file%20listing%20with%20folders%20and%20files%2C%20clean%20modern%20UI&width=375&height=250&seq=1&orientation=landscape">
    <style>
        :where([class^="ri-"])::before { content: "\f3c2"; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }
        .code-block {
            font-family: 'Fira Code', monospace;
            white-space: pre-wrap;
            overflow-x: auto;
        }
        .progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background-color: #3B82F6;
            z-index: 100;
            transition: width 0.3s;
        }
        .animate-fade-in {
            animation: fadeIn 0.5s;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: none; }
        }
        .collapsible-step .step-content {
            transition: max-height 0.4s cubic-bezier(0.4,0,0.2,1), opacity 0.4s;
            overflow: hidden;
            max-height: 2000px;
            opacity: 1;
        }
        .collapsible-step.collapsed .step-content {
            max-height: 0;
            opacity: 0;
            pointer-events: none;
        }
        .collapsible-step .step-header {
            user-select: none;
        }
        .collapsible-step.collapsed .ri-arrow-down-s-line {
            transform: rotate(-90deg);
        }
        /* Theme classes will be injected dynamically */
        @media (max-width: 900px) {
            .grid.grid-cols-1.gap-4.mb-6, .grid.grid-cols-1.gap-4 {
                grid-template-columns: 1fr !important;
            }
            .bg-white.rounded-lg.shadow-sm.overflow-hidden.border.border-gray-100 {
                margin-bottom: 1rem;
            }
        }
        @media (max-width: 600px) {
            main, .main {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
            .bg-white.rounded-lg.shadow-sm.overflow-hidden.border.border-gray-100 {
                margin-bottom: 0.75rem;
            }
            .text-2xl, .text-xl {
                font-size: 1.25rem !important;
            }
            .text-lg {
                font-size: 1.1rem !important;
            }
            .p-4 {
                padding: 0.75rem !important;
            }
            .mb-6 {
                margin-bottom: 1rem !important;
            }
            .mb-10 {
                margin-bottom: 1.5rem !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">
    <div id="progress-bar" class="progress-bar" style="width: 0%;"></div>
    
    <!-- Navigation Bar -->
    <nav class="fixed top-0 w-full bg-white shadow-sm z-50 px-4 py-3 flex items-center justify-between">
        <div class="flex items-center">
            <span class="text-xl font-['Pacifico'] text-primary">ATZ</span>
        </div>
        <div class="flex items-center space-x-3">
            <button id="share-btn" class="w-8 h-8 flex items-center justify-center text-gray-600">
                <i class="ri-share-line ri-lg"></i>
            </button>
            <button id="theme-picker-btn" class="ml-2 w-8 h-8 flex items-center justify-center text-gray-600" title="Change theme">
                <i class="ri-palette-line ri-lg"></i>
            </button>
            <div class="relative ml-2">
                <button id="profile-avatar" class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white focus:outline-none" aria-haspopup="true" aria-expanded="false">
                    <i class="ri-user-star-line"></i>
                </button>
                <div id="profile-dropdown" class="hidden absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg py-2 z-50 animate-fade-in">
                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Settings</a>
                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</a>
                </div>
            </div>
            <button id="dark-mode-toggle" class="ml-2 w-8 h-8 flex items-center justify-center text-gray-600" title="Toggle dark mode">
                <i class="ri-moon-line ri-lg"></i>
            </button>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="pt-16 pb-20 px-4">
        <!-- Header Section -->
        <header class="mb-6 mt-2">
            <div class="flex items-center space-x-2 mb-2">
                <span class="text-xs bg-blue-100 text-primary px-2 py-1 rounded-full">Tutorial</span>
                <span class="text-xs text-gray-500">10 min read</span>
            </div>
            <h1 class="text-2xl font-bold mb-2">Setting Up a Web-Based File Manager on Linux Servers</h1>
            <p class="text-gray-600 text-sm mb-4">A comprehensive guide for Ubuntu and CentOS/RHEL systems</p>
            
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white">
                    <i class="ri-user-star-line"></i>
                </div>
                <div>
                    <p class="text-sm font-medium">MohdAkmal</p>
                    <p class="text-xs text-gray-500">May 29, 2025</p>
                </div>
            </div>
        </header>
        
        <!-- Introduction -->
        <section class="mb-8">
            <p class="mb-4">Managing files on a remote Linux server can be challenging, especially for users who are more comfortable with graphical interfaces. A web-based file manager provides an intuitive way to browse, upload, download, and manage files through your browser, eliminating the need for FTP clients or command-line expertise.</p>
            
            <div class="bg-white p-4 rounded-lg shadow-sm mb-4 border border-gray-100">
                <h3 class="font-medium mb-2 text-gray-800">Prerequisites</h3>
                <ul class="list-disc pl-5 text-sm text-gray-700 space-y-1">
                    <li>A Linux server (Ubuntu/Debian or CentOS/RHEL)</li>
                    <li>Root or sudo access to the server</li>
                    <li>Basic familiarity with the command line</li>
                    <li>A domain name pointing to your server (for HTTPS setup)</li>
                </ul>
            </div>
            
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 mb-6">
                <div class="flex items-start">
                    <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center text-primary mr-2">
                        <i class="ri-information-line"></i>
                    </div>
                    <p class="text-sm text-gray-700">In this tutorial, we'll be setting up <strong>File Browser</strong>, a lightweight, single-binary file manager that's easy to install and configure. We'll also cover alternatives like Nextcloud and elFinder.</p>
                </div>
            </div>
            
            <!-- Table of Contents -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6 border border-gray-100">
                <h3 class="font-medium mb-3 text-gray-800">Table of Contents</h3>
                <input id="toc-search" type="text" placeholder="Search..." class="mb-2 px-2 py-1 border border-gray-200 rounded w-full text-sm" />
                <ol class="list-decimal pl-5 text-sm text-gray-700 space-y-2" id="toc-list">
                    <li><a href="#step1" class="text-primary hover:underline">Installing Prerequisites</a></li>
                    <li><a href="#step2" class="text-primary hover:underline">Choosing a Web File Manager</a></li>
                    <li><a href="#step3" class="text-primary hover:underline">Installation Process</a></li>
                    <li><a href="#step4" class="text-primary hover:underline">Configuring for Secure Access</a></li>
                    <li><a href="#step5" class="text-primary hover:underline">User Authentication & Permissions</a></li>
                    <li><a href="#step6" class="text-primary hover:underline">Setting Up HTTPS with Let's Encrypt</a></li>
                    <li><a href="#step7" class="text-primary hover:underline">Configuring Firewall Rules</a></li>
                    <li><a href="#step8" class="text-primary hover:underline">Running as a Service or with Docker</a></li>
                </ol>
            </div>
        </section>
        
        <!-- Step 1: Installing Prerequisites -->
        <section id="step1" class="mb-10 collapsible-step" draggable="true">
            <div class="flex items-center mb-4 cursor-pointer step-header">
                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center mr-3">
                    <span>1</span>
                </div>
                <h2 class="text-xl font-bold flex-1">Installing Prerequisites</h2>
                <i class="ri-arrow-down-s-line text-2xl transition-transform"></i>
            </div>
            <div class="step-content">
            <p class="mb-4">Before installing a web file manager, we need to set up some basic requirements on our server. The exact prerequisites depend on which file manager you choose, but most require a web server and sometimes PHP or Node.js.</p>
            
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6 border border-gray-100">
                <div class="tabs flex border-b border-gray-200">
                    <button class="tab-btn active py-2 px-4 text-sm font-medium text-primary border-b-2 border-primary" data-tab="ubuntu">Ubuntu/Debian</button>
                    <button class="tab-btn py-2 px-4 text-sm font-medium text-gray-600" data-tab="centos">CentOS/RHEL</button>
                </div>
                
                <div class="tab-content p-4" id="ubuntu-content">
                    <p class="text-sm mb-3">Update your package lists and install basic tools:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo apt update
sudo apt upgrade -y
sudo apt install -y curl wget unzip nginx</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Install Node.js (required for some file managers):</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Install PHP (required for some file managers like elFinder):</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm relative">
                        <pre>sudo apt install -y php-fpm php-cli php-json php-common php-mbstring php-zip php-gd php-xml php-curl</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                </div>
                
                <div class="tab-content p-4 hidden" id="centos-content">
                    <p class="text-sm mb-3">Update your system and install basic tools:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo yum update -y
sudo yum install -y epel-release
sudo yum install -y curl wget unzip nginx</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Install Node.js (required for some file managers):</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>curl -fsSL https://rpm.nodesource.com/setup_18.x | sudo bash -
sudo yum install -y nodejs</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Install PHP (required for some file managers like elFinder):</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm relative">
                        <pre>sudo yum install -y php-fpm php-cli php-json php-common php-mbstring php-zip php-gd php-xml php-curl</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100 mb-4">
                <div class="flex items-start">
                    <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center text-yellow-500 mr-2">
                        <i class="ri-alert-line"></i>
                    </div>
                    <p class="text-sm text-gray-700">Make sure your server has at least 1GB of RAM and 10GB of free disk space for a smooth experience. Lower specs may work but might result in performance issues.</p>
                </div>
            </div>
            </div>
        </section>
        
        <!-- Step 2: Choosing a Web File Manager -->
        <section id="step2" class="mb-10 collapsible-step" draggable="true">
            <div class="flex items-center mb-4 cursor-pointer step-header">
                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center mr-3">
                    <span>2</span>
                </div>
                <h2 class="text-xl font-bold flex-1">Choosing a Web File Manager</h2>
                <i class="ri-arrow-down-s-line text-2xl transition-transform"></i>
            </div>
            <div class="step-content">
            <p class="mb-4">There are several excellent web-based file managers available. Let's compare the most popular options:</p>
            
            <div class="grid grid-cols-1 gap-4 mb-6">
                <!-- File Browser -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex items-center">
                            <div class="w-10 h-10 flex items-center justify-center bg-blue-100 rounded-lg text-primary mr-3">
                                <i class="ri-folder-line ri-lg"></i>
                            </div>
                            <h3 class="font-medium">File Browser</h3>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex flex-col space-y-2 text-sm text-gray-700 mb-3">
                            <div class="flex items-center">
                                <div class="w-6 h-6 flex items-center justify-center text-green-500 mr-2">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span>Lightweight single binary (no dependencies)</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 flex items-center justify-center text-green-500 mr-2">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span>Easy to install and configure</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 flex items-center justify-center text-green-500 mr-2">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span>User management and permissions</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 flex items-center justify-center text-green-500 mr-2">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span>Modern, responsive UI</span>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button class="text-sm text-white bg-primary px-4 py-2 rounded !rounded-button">Recommended</button>
                        </div>
                    </div>
                </div>
                
                <!-- Nextcloud -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex items-center">
                            <div class="w-10 h-10 flex items-center justify-center bg-blue-100 rounded-lg text-primary mr-3">
                                <i class="ri-cloud-line ri-lg"></i>
                            </div>
                            <h3 class="font-medium">Nextcloud</h3>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex flex-col space-y-2 text-sm text-gray-700 mb-3">
                            <div class="flex items-center">
                                <div class="w-6 h-6 flex items-center justify-center text-green-500 mr-2">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span>Full-featured collaboration platform</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 flex items-center justify-center text-green-500 mr-2">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span>File sharing, calendars, contacts, etc.</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 flex items-center justify-center text-yellow-500 mr-2">
                                    <i class="ri-error-warning-line"></i>
                                </div>
                                <span>Requires more resources (PHP, database)</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 flex items-center justify-center text-yellow-500 mr-2">
                                    <i class="ri-error-warning-line"></i>
                                </div>
                                <span>More complex setup</span>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button class="text-sm text-gray-600 bg-gray-100 px-4 py-2 rounded !rounded-button">Advanced Option</button>
                        </div>
                    </div>
                </div>
                
                <!-- elFinder -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex items-center">
                            <div class="w-10 h-10 flex items-center justify-center bg-blue-100 rounded-lg text-primary mr-3">
                                <i class="ri-file-list-line ri-lg"></i>
                            </div>
                            <h3 class="font-medium">elFinder</h3>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex flex-col space-y-2 text-sm text-gray-700 mb-3">
                            <div class="flex items-center">
                                <div class="w-6 h-6 flex items-center justify-center text-green-500 mr-2">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span>Open-source file manager script</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 flex items-center justify-center text-green-500 mr-2">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span>Integration with CKEditor and TinyMCE</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 flex items-center justify-center text-yellow-500 mr-2">
                                    <i class="ri-error-warning-line"></i>
                                </div>
                                <span>Requires PHP</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 flex items-center justify-center text-yellow-500 mr-2">
                                    <i class="ri-error-warning-line"></i>
                                </div>
                                <span>More setup for authentication</span>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button class="text-sm text-gray-600 bg-gray-100 px-4 py-2 rounded !rounded-button">Alternative Option</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <p class="mb-4">For this tutorial, we'll focus on <strong>File Browser</strong> because it's lightweight, easy to set up, and doesn't require a database or complex dependencies. It's perfect for basic file management needs.</p>
            </div>
        </section>
        
        <!-- Step 3: Installation Process -->
        <section id="step3" class="mb-10 collapsible-step" draggable="true">
            <div class="flex items-center mb-4 cursor-pointer step-header">
                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center mr-3">
                    <span>3</span>
                </div>
                <h2 class="text-xl font-bold flex-1">Installation Process</h2>
                <i class="ri-arrow-down-s-line text-2xl transition-transform"></i>
            </div>
            <div class="step-content">
            <p class="mb-4">Now that we've chosen File Browser, let's install it on our server. File Browser is distributed as a single binary file, making installation straightforward.</p>
            
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6 border border-gray-100">
                <div class="tabs flex border-b border-gray-200">
                    <button class="tab-btn active py-2 px-4 text-sm font-medium text-primary border-b-2 border-primary" data-tab="install-ubuntu">Ubuntu/Debian</button>
                    <button class="tab-btn py-2 px-4 text-sm font-medium text-gray-600" data-tab="install-centos">CentOS/RHEL</button>
                </div>
                
                <div class="tab-content p-4" id="install-ubuntu-content">
                    <p class="text-sm mb-3">Create a directory for File Browser:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo mkdir -p /opt/filebrowser
cd /opt/filebrowser</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Download the latest version of File Browser:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>curl -fsSL https://github.com/filebrowser/filebrowser/releases/latest/download/linux-amd64-filebrowser.tar.gz | sudo tar -xzf - -C /opt/filebrowser</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Create a configuration directory:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo mkdir -p /etc/filebrowser
sudo touch /etc/filebrowser/settings.json</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Initialize the File Browser database:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm relative">
                        <pre>sudo /opt/filebrowser/filebrowser -d /etc/filebrowser/filebrowser.db config init</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                </div>
                
                <div class="tab-content p-4 hidden" id="install-centos-content">
                    <p class="text-sm mb-3">Create a directory for File Browser:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo mkdir -p /opt/filebrowser
cd /opt/filebrowser</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Download the latest version of File Browser:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>curl -fsSL https://github.com/filebrowser/filebrowser/releases/latest/download/linux-amd64-filebrowser.tar.gz | sudo tar -xzf - -C /opt/filebrowser</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Create a configuration directory:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo mkdir -p /etc/filebrowser
sudo touch /etc/filebrowser/settings.json</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Initialize the File Browser database:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm relative">
                        <pre>sudo /opt/filebrowser/filebrowser -d /etc/filebrowser/filebrowser.db config init</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm mb-6 border border-gray-100">
                <h3 class="font-medium mb-3">Verify Installation</h3>
                <p class="text-sm mb-3">Let's start File Browser temporarily to verify it works:</p>
                <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                    <pre>sudo /opt/filebrowser/filebrowser -d /etc/filebrowser/filebrowser.db -a 0.0.0.0 -p 8080 -r /path/to/your/files</pre>
                    <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                </div>
                <p class="text-sm">Replace <code>/path/to/your/files</code> with the directory you want to manage (e.g., <code>/home/user</code> or <code>/var/www</code>).</p>
            </div>
            
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 mb-4">
                <div class="flex items-start">
                    <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center text-primary mr-2">
                        <i class="ri-information-line"></i>
                    </div>
                    <p class="text-sm text-gray-700">After running the command above, you should be able to access File Browser by visiting <code>http://your-server-ip:8080</code> in your browser. The default login is username: <code>admin</code> and password: <code>admin</code>. Press <code>Ctrl+C</code> to stop the temporary server after testing.</p>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm mb-4 border border-gray-100">
                <h3 class="font-medium mb-3">File Browser Screenshots</h3>
                <div class="mb-3">
                    <img src="https://readdy.ai/api/search-image?query=screenshot%20of%20File%20Browser%20web%20interface%20showing%20file%20listing%20with%20folders%20and%20files%2C%20clean%20modern%20UI&width=375&height=250&seq=1&orientation=landscape" alt="File Browser Interface" class="w-full h-auto rounded-lg">
                    <p class="text-xs text-gray-500 mt-1 text-center">File Browser main interface</p>
                </div>
            </div>
            </div>
        </section>
        
        <!-- Step 4: Configuring for Secure Access -->
        <section id="step4" class="mb-10 collapsible-step" draggable="true">
            <div class="flex items-center mb-4 cursor-pointer step-header">
                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center mr-3">
                    <span>4</span>
                </div>
                <h2 class="text-xl font-bold flex-1">Configuring for Secure Access</h2>
                <i class="ri-arrow-down-s-line text-2xl transition-transform"></i>
            </div>
            <div class="step-content">
            <p class="mb-4">Now let's configure File Browser for secure access. We'll set up a reverse proxy with Nginx to add an extra layer of security and flexibility.</p>
            
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6 border border-gray-100">
                <div class="tabs flex border-b border-gray-200">
                    <button class="tab-btn active py-2 px-4 text-sm font-medium text-primary border-b-2 border-primary" data-tab="config-ubuntu">Ubuntu/Debian</button>
                    <button class="tab-btn py-2 px-4 text-sm font-medium text-gray-600" data-tab="config-centos">CentOS/RHEL</button>
                </div>
                
                <div class="tab-content p-4" id="config-ubuntu-content">
                    <p class="text-sm mb-3">Create an Nginx configuration file for File Browser:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo nano /etc/nginx/sites-available/filebrowser</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Add the following configuration (replace <code>your-domain.com</code> with your actual domain):</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>server {
    listen 80;
    server_name your-domain.com;

    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        
        client_max_body_size 100M;
        proxy_read_timeout 300;
    }
}</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Enable the site and test Nginx configuration:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo ln -s /etc/nginx/sites-available/filebrowser /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                </div>
                
                <div class="tab-content p-4 hidden" id="config-centos-content">
                    <p class="text-sm mb-3">Create an Nginx configuration file for File Browser:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo nano /etc/nginx/conf.d/filebrowser.conf</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Add the following configuration (replace <code>your-domain.com</code> with your actual domain):</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>server {
    listen 80;
    server_name your-domain.com;

    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        
        client_max_body_size 100M;
        proxy_read_timeout 300;
    }
}</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Test Nginx configuration and restart:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo nginx -t
sudo systemctl restart nginx</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100 mb-6">
                <div class="flex items-start">
                    <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center text-yellow-500 mr-2">
                        <i class="ri-alert-line"></i>
                    </div>
                    <p class="text-sm text-gray-700">The configuration above sets the maximum upload file size to 100MB. You can adjust <code>client_max_body_size</code> if you need to upload larger files.</p>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm mb-4 border border-gray-100">
                <h3 class="font-medium mb-3">Modify File Browser Configuration</h3>
                <p class="text-sm mb-3">Configure File Browser to only listen on localhost (for security):</p>
                <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm relative">
                    <pre>sudo /opt/filebrowser/filebrowser -d /etc/filebrowser/filebrowser.db config set --address 127.0.0.1 --port 8080 --root /path/to/your/files --baseurl ""</pre>
                    <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                </div>
            </div>
            </div>
        </section>
        
        <!-- Step 5: User Authentication & Permissions -->
        <section id="step5" class="mb-10 collapsible-step" draggable="true">
            <div class="flex items-center mb-4 cursor-pointer step-header">
                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center mr-3">
                    <span>5</span>
                </div>
                <h2 class="text-xl font-bold flex-1">User Authentication & Permissions</h2>
                <i class="ri-arrow-down-s-line text-2xl transition-transform"></i>
            </div>
            <div class="step-content">
            <p class="mb-4">Now let's set up proper user authentication and permissions for File Browser. First, we'll change the default admin password and then create additional users if needed.</p>
            
            <div class="bg-white p-4 rounded-lg shadow-sm mb-6 border border-gray-100">
                <h3 class="font-medium mb-3">Change Admin Password</h3>
                <p class="text-sm mb-3">For security reasons, change the default admin password:</p>
                <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm relative">
                    <pre>sudo /opt/filebrowser/filebrowser -d /etc/filebrowser/filebrowser.db users update admin --password "your-secure-password"</pre>
                    <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm mb-6 border border-gray-100">
                <h3 class="font-medium mb-3">Create Additional Users</h3>
                <p class="text-sm mb-3">Create a new user with restricted access:</p>
                <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                    <pre>sudo /opt/filebrowser/filebrowser -d /etc/filebrowser/filebrowser.db users add username "password" --scope /path/to/restricted/folder</pre>
                    <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                </div>
                
                <p class="text-sm mb-3">Set user permissions (example for read-only access):</p>
                <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm relative">
                    <pre>sudo /opt/filebrowser/filebrowser -d /etc/filebrowser/filebrowser.db users update username --perm.admin=false --perm.create=false --perm.rename=false --perm.modify=false --perm.delete=false --perm.share=false</pre>
                    <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm mb-4 border border-gray-100">
                <h3 class="font-medium mb-3">Permission Options Explained</h3>
                <div class="grid grid-cols-1 gap-2 text-sm">
                    <div class="flex items-center">
                        <div class="w-6 h-6 flex items-center justify-center text-primary mr-2">
                            <i class="ri-shield-check-line"></i>
                        </div>
                        <span><strong>admin</strong>: Can access the admin panel</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-6 h-6 flex items-center justify-center text-primary mr-2">
                            <i class="ri-file-add-line"></i>
                        </div>
                        <span><strong>create</strong>: Can create files and directories</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-6 h-6 flex items-center justify-center text-primary mr-2">
                            <i class="ri-edit-line"></i>
                        </div>
                        <span><strong>rename</strong>: Can rename files and directories</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-6 h-6 flex items-center justify-center text-primary mr-2">
                            <i class="ri-file-edit-line"></i>
                        </div>
                        <span><strong>modify</strong>: Can modify files (edit content)</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-6 h-6 flex items-center justify-center text-primary mr-2">
                            <i class="ri-delete-bin-line"></i>
                        </div>
                        <span><strong>delete</strong>: Can delete files and directories</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-6 h-6 flex items-center justify-center text-primary mr-2">
                            <i class="ri-share-line"></i>
                        </div>
                        <span><strong>share</strong>: Can share files with public links</span>
                    </div>
                </div>
            </div>
            </div>
        </section>
        
        <!-- Step 6: Setting Up HTTPS with Let's Encrypt -->
        <section id="step6" class="mb-10 collapsible-step" draggable="true">
            <div class="flex items-center mb-4 cursor-pointer step-header">
                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center mr-3">
                    <span>6</span>
                </div>
                <h2 class="text-xl font-bold flex-1">Setting Up HTTPS with Let's Encrypt</h2>
                <i class="ri-arrow-down-s-line text-2xl transition-transform"></i>
            </div>
            <div class="step-content">
            <p class="mb-4">Securing your file manager with HTTPS is essential, especially when handling sensitive files. Let's set up a free SSL certificate using Let's Encrypt.</p>
            
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6 border border-gray-100">
                <div class="tabs flex border-b border-gray-200">
                    <button class="tab-btn active py-2 px-4 text-sm font-medium text-primary border-b-2 border-primary" data-tab="ssl-ubuntu">Ubuntu/Debian</button>
                    <button class="tab-btn py-2 px-4 text-sm font-medium text-gray-600" data-tab="ssl-centos">CentOS/RHEL</button>
                </div>
                
                <div class="tab-content p-4" id="ssl-ubuntu-content">
                    <p class="text-sm mb-3">Install Certbot (Let's Encrypt client):</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo apt install -y certbot python3-certbot-nginx</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Obtain and install SSL certificate:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo certbot --nginx -d your-domain.com</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                </div>
                
                <div class="tab-content p-4 hidden" id="ssl-centos-content">
                    <p class="text-sm mb-3">Install Certbot (Let's Encrypt client):</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo yum install -y certbot python3-certbot-nginx</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Obtain and install SSL certificate:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo certbot --nginx -d your-domain.com</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 mb-6">
                <div class="flex items-start">
                    <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center text-primary mr-2">
                        <i class="ri-information-line"></i>
                    </div>
                    <p class="text-sm text-gray-700">Certbot will automatically modify your Nginx configuration to use HTTPS. Follow the prompts during the certificate installation process. Let's Encrypt certificates are valid for 90 days, but Certbot sets up automatic renewal.</p>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm mb-4 border border-gray-100">
                <h3 class="font-medium mb-3">Verify SSL Setup</h3>
                <p class="text-sm mb-3">After completing the Certbot setup, verify that your site is accessible via HTTPS by visiting <code>https://your-domain.com</code> in your browser. You should see a secure padlock icon in the address bar.</p>
                <div class="mb-3">
                    <img src="https://readdy.ai/api/search-image?query=browser%20address%20bar%20showing%20https%20secure%20padlock%20icon%2C%20clean%20modern%20UI&width=375&height=100&seq=2&orientation=landscape" alt="HTTPS Secure Connection" class="w-full h-auto rounded-lg">
                    <p class="text-xs text-gray-500 mt-1 text-center">Secure HTTPS connection with padlock icon</p>
                </div>
            </div>
            </div>
        </section>
        
        <!-- Step 7: Configuring Firewall Rules -->
        <section id="step7" class="mb-10 collapsible-step" draggable="true">
            <div class="flex items-center mb-4 cursor-pointer step-header">
                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center mr-3">
                    <span>7</span>
                </div>
                <h2 class="text-xl font-bold flex-1">Configuring Firewall Rules</h2>
                <i class="ri-arrow-down-s-line text-2xl transition-transform"></i>
            </div>
            <div class="step-content">
            <p class="mb-4">To secure your server, you should configure the firewall to only allow traffic on necessary ports. For our web file manager, we only need to allow HTTP (port 80) and HTTPS (port 443) traffic.</p>
            
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6 border border-gray-100">
                <div class="tabs flex border-b border-gray-200">
                    <button class="tab-btn active py-2 px-4 text-sm font-medium text-primary border-b-2 border-primary" data-tab="fw-ubuntu">Ubuntu/Debian (UFW)</button>
                    <button class="tab-btn py-2 px-4 text-sm font-medium text-gray-600" data-tab="fw-centos">CentOS/RHEL (firewalld)</button>
                </div>
                
                <div class="tab-content p-4" id="fw-ubuntu-content">
                    <p class="text-sm mb-3">Install UFW if not already installed:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo apt install -y ufw</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Configure UFW to allow SSH, HTTP, and HTTPS:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo ufw allow ssh
sudo ufw allow http
sudo ufw allow https</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Enable the firewall:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo ufw enable</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Check the status of the firewall:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm relative">
                        <pre>sudo ufw status</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                </div>
                
                <div class="tab-content p-4 hidden" id="fw-centos-content">
                    <p class="text-sm mb-3">Check if firewalld is running:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo systemctl status firewalld</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">If not running, start and enable it:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo systemctl start firewalld
sudo systemctl enable firewalld</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Allow SSH, HTTP, and HTTPS traffic:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo firewall-cmd --permanent --add-service=ssh
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Reload the firewall to apply changes:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm relative">
                        <pre>sudo firewall-cmd --reload</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100 mb-4">
                <div class="flex items-start">
                    <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center text-yellow-500 mr-2">
                        <i class="ri-alert-line"></i>
                    </div>
                    <p class="text-sm text-gray-700"><strong>Important:</strong> Always make sure to allow SSH access before enabling a firewall to avoid locking yourself out of the server. If you're connecting on a non-standard SSH port, make sure to allow that specific port instead.</p>
                </div>
            </div>
            </div>
        </section>
        
        <!-- Step 8: Running as a Service or with Docker -->
        <section id="step8" class="mb-10 collapsible-step" draggable="true">
            <div class="flex items-center mb-4 cursor-pointer step-header">
                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center mr-3">
                    <span>8</span>
                </div>
                <h2 class="text-xl font-bold flex-1">Running as a Service or with Docker</h2>
                <i class="ri-arrow-down-s-line text-2xl transition-transform"></i>
            </div>
            <div class="step-content">
            <p class="mb-4">To ensure File Browser runs automatically at system startup and restarts if it crashes, let's set it up as a systemd service. Alternatively, you can run it using Docker for easier deployment and isolation.</p>
            
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6 border border-gray-100">
                <div class="tabs flex border-b border-gray-200">
                    <button class="tab-btn active py-2 px-4 text-sm font-medium text-primary border-b-2 border-primary" data-tab="service">Systemd Service</button>
                    <button class="tab-btn py-2 px-4 text-sm font-medium text-gray-600" data-tab="docker">Docker</button>
                </div>
                
                <div class="tab-content p-4" id="service-content">
                    <p class="text-sm mb-3">Create a systemd service file:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo nano /etc/systemd/system/filebrowser.service</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Add the following content to the service file:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>[Unit]
Description=File Browser
After=network.target

[Service]
Type=simple
User=root
Group=root
ExecStart=/opt/filebrowser/filebrowser -d /etc/filebrowser/filebrowser.db -a 127.0.0.1 -p 8080 -r /path/to/your/files
Restart=on-failure
RestartSec=5

[Install]
WantedBy=multi-user.target</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Enable and start the service:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo systemctl daemon-reload
sudo systemctl enable filebrowser
sudo systemctl start filebrowser</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Check the service status:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm relative">
                        <pre>sudo systemctl status filebrowser</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                </div>
                
                <div class="tab-content p-4 hidden" id="docker-content">
                    <p class="text-sm mb-3">Install Docker if not already installed:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>curl -fsSL https://get.docker.com | sh</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Create directories for File Browser:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo mkdir -p /opt/filebrowser/data
sudo mkdir -p /opt/filebrowser/database</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Create a Docker Compose file:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>sudo nano /opt/filebrowser/docker-compose.yml</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Add the following content to the Docker Compose file:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm mb-3 relative">
                        <pre>version: '3'
services:
  filebrowser:
    image: filebrowser/filebrowser:latest
    container_name: filebrowser
    user: "${UID}:${GID}"
    ports:
      - "127.0.0.1:8080:80"
    volumes:
      - /path/to/your/files:/srv
      - /opt/filebrowser/database:/database
    restart: unless-stopped
    environment:
      - FB_BASEURL=""
      - FB_NOAUTH=false</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                    
                    <p class="text-sm mb-3">Start the Docker container:</p>
                    <div class="bg-gray-900 text-gray-100 p-3 rounded-lg code-block text-sm relative">
                        <pre>cd /opt/filebrowser
sudo docker-compose up -d</pre>
                        <button class="copy-btn absolute top-2 right-2 text-xs bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded !rounded-button">Copy</button>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 mb-6">
                <div class="flex items-start">
                    <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center text-primary mr-2">
                        <i class="ri-information-line"></i>
                    </div>
                    <p class="text-sm text-gray-700">The Docker approach provides better isolation and makes it easier to upgrade or move your File Browser installation. However, the systemd service approach is simpler and uses fewer resources.</p>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm mb-4 border border-gray-100">
                <h3 class="font-medium mb-3">Accessing Your File Manager</h3>
                <p class="text-sm mb-3">You can now access your web-based file manager by visiting your domain in a browser:</p>
                <div class="bg-gray-100 p-3 rounded-lg text-sm mb-3">
                    <code>https://your-domain.com</code>
                </div>
                <p class="text-sm">Log in with the admin credentials you set up earlier. If you're using the default credentials, remember to change them immediately for security.</p>
                <div class="mb-3 mt-4">
                    <img src="https://readdy.ai/api/search-image?query=web%20file%20manager%20login%20screen%20with%20username%20and%20password%20fields%2C%20modern%20clean%20interface&width=375&height=250&seq=3&orientation=landscape" alt="File Browser Login Screen" class="w-full h-auto rounded-lg">
                    <p class="text-xs text-gray-500 mt-1 text-center">File Browser login screen</p>
                </div>
            </div>
            </div>
        </section>
        
        <!-- Conclusion -->
        <section class="mb-10">
            <h2 class="text-xl font-bold mb-4">Conclusion</h2>
            <p class="mb-4">Congratulations! You've successfully set up a secure web-based file manager on your Linux server. This provides an easy way to manage files without needing FTP or command-line access, making file management more accessible for users of all technical levels.</p>
            
            <div class="bg-white p-4 rounded-lg shadow-sm mb-6 border border-gray-100">
                <h3 class="font-medium mb-3">Summary of What We've Accomplished</h3>
                <ul class="list-disc pl-5 text-sm text-gray-700 space-y-1">
                    <li>Installed prerequisites and dependencies</li>
                    <li>Set up File Browser with proper configuration</li>
                    <li>Configured Nginx as a reverse proxy</li>
                    <li>Secured the connection with HTTPS using Let's Encrypt</li>
                    <li>Set up user authentication and permissions</li>
                    <li>Configured firewall rules for security</li>
                    <li>Set up File Browser to run as a service or with Docker</li>
                </ul>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm mb-4 border border-gray-100">
                <h3 class="font-medium mb-3">Next Steps</h3>
                <ul class="list-disc pl-5 text-sm text-gray-700 space-y-1">
                    <li>Consider setting up regular backups of your files</li>
                    <li>Implement monitoring for your server</li>
                    <li>Keep your system and File Browser updated regularly</li>
                    <li>Review and adjust user permissions as needed</li>
                </ul>
            </div>
        </section>
    </main>
    
    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-20 right-4 bg-primary text-white w-10 h-10 rounded-full flex items-center justify-center shadow-lg opacity-0 transition-opacity duration-300 cursor-pointer">
        <i class="ri-arrow-up-line ri-lg"></i>
    </button>
    
    <!-- Share Modal -->
    <div id="share-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end justify-center hidden">
        <div class="bg-white rounded-t-xl w-full max-w-md p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-medium text-lg">Share this article</h3>
                <button id="close-share-modal" class="w-8 h-8 flex items-center justify-center text-gray-600">
                    <i class="ri-close-line ri-lg"></i>
                </button>
            </div>
            <div class="grid grid-cols-4 gap-4 mb-6">
                <a href="#" class="flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mb-1">
                        <i class="ri-twitter-fill ri-lg"></i>
                    </div>
                    <span class="text-xs">Twitter</span>
                </a>
                <a href="#" class="flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-800 mb-1">
                        <i class="ri-facebook-fill ri-lg"></i>
                    </div>
                    <span class="text-xs">Facebook</span>
                </a>
                <a href="#" class="flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 mb-1">
                        <i class="ri-linkedin-fill ri-lg"></i>
                    </div>
                    <span class="text-xs">LinkedIn</span>
                </a>
                <a href="#" class="flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 mb-1">
                        <i class="ri-whatsapp-fill ri-lg"></i>
                    </div>
                    <span class="text-xs">WhatsApp</span>
                </a>
            </div>
            <div class="bg-gray-100 rounded-lg p-2 flex items-center mb-4">
                <input type="text" value="https://your-domain.com/article-url" class="bg-transparent flex-1 text-sm border-none outline-none" readonly>
                <button id="copy-link" class="bg-primary text-white px-3 py-1 rounded text-sm !rounded-button">Copy</button>
            </div>
        </div>
    </div>
    
    <script id="tab-switcher">
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-btn');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');
                    const tabContainer = this.closest('.tabs').parentElement;
                    
                    // Hide all tab contents in this container
                    tabContainer.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.add('hidden');
                    });
                    
                    // Show the selected tab content
                    const selectedContent = tabContainer.querySelector(`#${tabName}-content`);
                    if (selectedContent) {
                        selectedContent.classList.remove('hidden');
                    }
                    
                    // Update active state of tab buttons
                    tabContainer.querySelectorAll('.tab-btn').forEach(btn => {
                        btn.classList.remove('active', 'text-primary', 'border-primary');
                        btn.classList.add('text-gray-600');
                    });
                    
                    this.classList.add('active', 'text-primary', 'border-b-2', 'border-primary');
                    this.classList.remove('text-gray-600');
                });
            });
        });
    </script>
    
    <script id="copy-button-handler">
        document.addEventListener('DOMContentLoaded', function() {
            const copyButtons = document.querySelectorAll('.copy-btn');
            copyButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const codeBlock = this.parentElement.querySelector('pre');
                    const textToCopy = codeBlock.textContent;
                    // Try clipboard API, fallback to execCommand
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(textToCopy).then(() => {
                            showCopyFeedback(this);
                        }, () => {
                            fallbackCopyTextToClipboard(textToCopy, this);
                        });
                    } else {
                        fallbackCopyTextToClipboard(textToCopy, this);
                    }
                });
            });
            function fallbackCopyTextToClipboard(text, btn) {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                document.body.appendChild(textarea);
                textarea.select();
                try {
                    document.execCommand('copy');
                    showCopyFeedback(btn);
                } catch (err) {
                    alert('Copy failed');
                }
                document.body.removeChild(textarea);
            }
            function showCopyFeedback(btn) {
                const originalText = btn.textContent;
                btn.textContent = 'Copied!';
                btn.classList.add('bg-green-700');
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.classList.remove('bg-green-700');
                }, 2000);
            }
            // Copy link button
            const copyLinkButton = document.getElementById('copy-link');
            if (copyLinkButton) {
                copyLinkButton.addEventListener('click', function() {
                    const linkInput = this.previousElementSibling;
                    linkInput.select();
                    navigator.clipboard.writeText(linkInput.value).then(() => {
                        const originalText = this.textContent;
                        this.textContent = 'Copied!';
                        setTimeout(() => {
                            this.textContent = originalText;
                        }, 2000);
                    });
                });
            }
        });
    </script>
    
    <script id="toc-search-handler">
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('toc-search');
            const tocList = document.getElementById('toc-list');
            if (searchInput && tocList) {
                searchInput.addEventListener('input', function() {
                    const filter = this.value.toLowerCase();
                    tocList.querySelectorAll('li').forEach(li => {
                        const text = li.textContent.toLowerCase();
                        li.style.display = text.includes(filter) ? '' : 'none';
                    });
                });
            }
        });
    </script>
    
    <script id="dark-mode-toggle-handler">
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('dark-mode-toggle');
            toggle.addEventListener('click', function() {
                document.body.classList.toggle('dark');
                if (document.body.classList.contains('dark')) {
                    document.body.style.backgroundColor = '#18181b';
                    document.body.style.color = '#f3f4f6';
                    toggle.innerHTML = '<i class="ri-sun-line ri-lg"></i>';
                } else {
                    document.body.style.backgroundColor = '#f9fafb';
                    document.body.style.color = '#18181b';
                    toggle.innerHTML = '<i class="ri-moon-line ri-lg"></i>';
                }
            });
        });
    </script>
    
    <script id="scroll-handler">
        document.addEventListener('DOMContentLoaded', function() {
            const backToTopButton = document.getElementById('back-to-top');
            const progressBar = document.getElementById('progress-bar');
            
            window.addEventListener('scroll', function() {
                // Back to top button visibility
                if (window.scrollY > 300) {
                    backToTopButton.classList.remove('opacity-0');
                    backToTopButton.classList.add('opacity-100');
                } else {
                    backToTopButton.classList.remove('opacity-100');
                    backToTopButton.classList.add('opacity-0');
                }
                
                // Progress bar
                const scrollPosition = window.scrollY;
                const documentHeight = document.body.scrollHeight - window.innerHeight;
                const scrollPercentage = (scrollPosition / documentHeight) * 100;
                progressBar.style.width = `${scrollPercentage}%`;
            });
            
            backToTopButton.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>
    
    <script id="modal-handler">
        document.addEventListener('DOMContentLoaded', function() {
            const shareButton = document.getElementById('share-btn');
            const shareModal = document.getElementById('share-modal');
            const closeShareModal = document.getElementById('close-share-modal');
            
            shareButton.addEventListener('click', function() {
                shareModal.classList.remove('hidden');
            });
            
            closeShareModal.addEventListener('click', function() {
                shareModal.classList.add('hidden');
            });
            
            shareModal.addEventListener('click', function(e) {
                if (e.target === shareModal) {
                    shareModal.classList.add('hidden');
                }
            });
        });
    </script>
    
    <script id="bookmark-handler">
        document.addEventListener('DOMContentLoaded', function() {
            const bookmarkButton = document.getElementById('bookmark-btn');
            let isBookmarked = false;
            
            bookmarkButton.addEventListener('click', function() {
                isBookmarked = !isBookmarked;
                
                if (isBookmarked) {
                    bookmarkButton.innerHTML = '<i class="ri-bookmark-fill ri-lg"></i>';
                    bookmarkButton.classList.add('text-primary');
                    
                    // Show toast notification
                    showToast('Article saved to bookmarks');
                } else {
                    bookmarkButton.innerHTML = '<i class="ri-bookmark-line ri-lg"></i>';
                    bookmarkButton.classList.remove('text-primary');
                    
                    // Show toast notification
                    showToast('Removed from bookmarks');
                }
            });
            
            function showToast(message) {
                // Create toast element
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-20 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-4 py-2 rounded-lg text-sm z-50';
                toast.textContent = message;
                
                // Add to document
                document.body.appendChild(toast);
                
                // Remove after 2 seconds
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
                    setTimeout(() => {
                        document.body.removeChild(toast);
                    }, 300);
                }, 2000);
            }
        });
    </script>
    
    <script id="advanced-uiux">
        document.addEventListener('DOMContentLoaded', function() {
            // Collapsible steps
            document.querySelectorAll('.collapsible-step .step-header').forEach(header => {
                header.addEventListener('click', function() {
                    const section = header.closest('.collapsible-step');
                    section.classList.toggle('collapsed');
                });
            });
            // Drag-and-drop for steps
            let dragSrcEl = null;
            document.querySelectorAll('.collapsible-step').forEach(step => {
                step.addEventListener('dragstart', function(e) {
                    dragSrcEl = this;
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/html', this.outerHTML);
                    this.classList.add('opacity-50');
                });
                step.addEventListener('dragend', function() {
                    this.classList.remove('opacity-50');
                });
                step.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                });
                step.addEventListener('drop', function(e) {
                    e.stopPropagation();
                    if (dragSrcEl !== this) {
                        this.parentNode.insertBefore(dragSrcEl, this.nextSibling);
                    }
                    return false;
                });
            });
            // Profile dropdown
            const avatarBtn = document.getElementById('profile-avatar');
            const dropdown = document.getElementById('profile-dropdown');
            avatarBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
                avatarBtn.setAttribute('aria-expanded', dropdown.classList.contains('hidden') ? 'false' : 'true');
            });
            document.addEventListener('click', function() {
                dropdown.classList.add('hidden');
                avatarBtn.setAttribute('aria-expanded', 'false');
            });
            // Theme picker
            const themes = [
                { name: 'Default', primary: '#3B82F6', secondary: '#10B981', bg: '#f9fafb', text: '#18181b' },
                { name: 'Sunset', primary: '#F59E42', secondary: '#F43F5E', bg: '#fff7ed', text: '#3b2f2f' },
                { name: 'Emerald', primary: '#059669', secondary: '#34d399', bg: '#ecfdf5', text: '#064e3b' },
                { name: 'High Contrast', primary: '#000', secondary: '#FFD600', bg: '#fff', text: '#000' }
            ];
            function applyTheme(theme) {
                document.documentElement.style.setProperty('--primary', theme.primary);
                document.documentElement.style.setProperty('--secondary', theme.secondary);
                document.body.style.backgroundColor = theme.bg;
                document.body.style.color = theme.text;
                localStorage.setItem('theme', JSON.stringify(theme));
            }
            function showThemePicker() {
                let modal = document.getElementById('theme-picker-modal');
                if (!modal) {
                    modal = document.createElement('div');
                    modal.id = 'theme-picker-modal';
                    modal.className = 'fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 animate-fade-in';
                    modal.innerHTML = `<div class="bg-white rounded-lg p-6 w-80 shadow-xl animate-fade-in">
                        <h3 class="text-lg font-bold mb-4">Choose Theme</h3>
                        <div class="space-y-2">
                            ${themes.map((t, i) => `<button class="w-full px-4 py-2 rounded text-left border border-gray-200 hover:bg-gray-100" data-theme-idx="${i}">${t.name}</button>`).join('')}
                        </div>
                        <button class="mt-4 w-full bg-primary text-white py-2 rounded" id="close-theme-picker">Close</button>
                    </div>`;
                    document.body.appendChild(modal);
                    modal.querySelectorAll('[data-theme-idx]').forEach(btn => {
                        btn.addEventListener('click', function() {
                            applyTheme(themes[parseInt(this.dataset.themeIdx)]);
                        });
                    });
                    modal.querySelector('#close-theme-picker').addEventListener('click', function() {
                        modal.remove();
                    });
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) modal.remove();
                    });
                }
            }
            document.getElementById('theme-picker-btn').addEventListener('click', function(e) {
                e.stopPropagation();
                showThemePicker();
            });
            // Load theme from localStorage
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                try { applyTheme(JSON.parse(savedTheme)); } catch {}
            }
            // FAB actions
            document.getElementById('fab').addEventListener('click', function() {
                showThemePicker();
            });
            // Keyboard navigation for modals
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('theme-picker-modal');
                    if (modal) modal.remove();
                }
            });
        });
    </script>

    <!-- Footer -->
    <footer class="w-full bg-gradient-to-r from-primary to-secondary text-white py-8 px-4 mt-12 shadow-inner">
      <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-center md:items-start justify-between gap-6">
        <div class="flex flex-col items-center md:items-start">
          <span class="text-2xl font-['Pacifico'] mb-2">ATZ</span>
          <p class="text-sm opacity-80 mb-2">Empowering Linux users with modern web-based file management.</p>
          <p class="text-xs opacity-60">&copy; 2025 MohdAkmal. All rights reserved.</p>
        </div>
        <div class="flex flex-col items-center md:items-end gap-2">
          <div class="flex gap-3 mb-2">
            <a href="#" class="hover:text-yellow-300 transition-colors" aria-label="Twitter"><i class="ri-twitter-fill ri-xl"></i></a>
            <a href="#" class="hover:text-yellow-300 transition-colors" aria-label="Facebook"><i class="ri-facebook-fill ri-xl"></i></a>
            <a href="#" class="hover:text-yellow-300 transition-colors" aria-label="LinkedIn"><i class="ri-linkedin-fill ri-xl"></i></a>
            <a href="#" class="hover:text-yellow-300 transition-colors" aria-label="GitHub"><i class="ri-github-fill ri-xl"></i></a>
          </div>
          <span class="text-xs opacity-70">Made with <i class="ri-heart-fill text-red-400"></i> for the Linux community</span>
        </div>
      </div>
    </footer>

    <style>
    :root {
      --primary: #3B82F6;
      --secondary: #10B981;
    }
    .theme-light {
      --primary: #3B82F6;
      --secondary: #10B981;
      --bg: #f9fafb;
      --text: #18181b;
    }
    .theme-dark {
      --primary: #18181b;
      --secondary: #3B82F6;
      --bg: #18181b;
      --text: #f3f4f6;
    }
    .theme-sunset {
      --primary: #F59E42;
      --secondary: #F43F5E;
      --bg: #fff7ed;
      --text: #3b2f2f;
    }
    .theme-emerald {
      --primary: #059669;
      --secondary: #34d399;
      --bg: #ecfdf5;
      --text: #064e3b;
    }
    body {
      background: var(--bg);
      color: var(--text);
      transition: background 0.4s, color 0.4s;
    }
    .bg-primary { background-color: var(--primary) !important; }
    .bg-secondary { background-color: var(--secondary) !important; }
    .text-primary { color: var(--primary) !important; }
    .text-secondary { color: var(--secondary) !important; }
    .bg-gradient-to-r {
      background: linear-gradient(to right, var(--primary), var(--secondary));
    }
    footer a { color: inherit; }
    footer a:hover { color: #FFD600; }
    </style>

    <script id="theme-switcher-enhanced">
    document.addEventListener('DOMContentLoaded', function() {
      const themes = [
        { name: 'Light', class: 'theme-light' },
        { name: 'Dark', class: 'theme-dark' },
        { name: 'Sunset', class: 'theme-sunset' },
        { name: 'Emerald', class: 'theme-emerald' }
      ];
      function setTheme(themeClass) {
        document.body.classList.remove(...themes.map(t => t.class));
        document.body.classList.add(themeClass);
        localStorage.setItem('theme', themeClass);
      }
      // Theme picker modal
      function showThemePicker() {
        let modal = document.getElementById('theme-picker-modal');
        if (!modal) {
          modal = document.createElement('div');
          modal.id = 'theme-picker-modal';
          modal.className = 'fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 animate-fade-in';
          modal.innerHTML = `<div class="bg-white rounded-lg p-6 w-80 shadow-xl animate-fade-in">
            <h3 class="text-lg font-bold mb-4">Choose Theme</h3>
            <div class="space-y-2">
              ${themes.map(t => `<button class="w-full px-4 py-2 rounded text-left border border-gray-200 flex items-center gap-2 hover:bg-gray-100" data-theme-class="${t.class}"><span class="inline-block w-4 h-4 rounded-full" style="background: linear-gradient(90deg, var(--primary), var(--secondary)); margin-right: 8px;"></span>${t.name}</button>`).join('')}
            </div>
            <button class="mt-4 w-full bg-primary text-white py-2 rounded" id="close-theme-picker">Close</button>
          </div>`;
          document.body.appendChild(modal);
          modal.querySelectorAll('[data-theme-class]').forEach(btn => {
            btn.addEventListener('click', function() {
              setTheme(this.dataset.themeClass);
            });
          });
          modal.querySelector('#close-theme-picker').addEventListener('click', function() {
            modal.remove();
          });
          modal.addEventListener('click', function(e) {
            if (e.target === modal) modal.remove();
          });
        }
      }
      document.getElementById('theme-picker-btn').addEventListener('click', function(e) {
        e.stopPropagation();
        showThemePicker();
      });
      // Load theme from localStorage
      const savedTheme = localStorage.getItem('theme');
      if (savedTheme) {
        setTheme(savedTheme);
      } else {
        setTheme('theme-light');
      }
    });
    </script>
</body>
</html>