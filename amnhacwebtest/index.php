<?php
session_start();
// Redirect to login if user is not logged in. 
if (!isset($_SESSION['user'])) {
    header('Location: auth/login_form.php');
    exit();
}

// Ensure correct path resolution
$basePath = __DIR__;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root { 
            --bg-black: #000000;
            --sidebar-bg: #121212;
            --text-main: #ffffff;
            --text-sub: #b3b3b3;
            --spotify-green: #1DB954;
        }
        body { margin: 0; background-color: var(--bg-black); color: var(--text-main); font-family: 'Outfit', sans-serif; overflow: hidden; }
        
        #app { display: flex; height: 100vh; width: 100vw; }
        #sidebar-area { width: 260px; flex-shrink: 0; background: var(--sidebar-bg); z-index: 100; }
        #main-view { flex-grow: 1; display: flex; flex-direction: column; height: 100%; position: relative; background: var(--bg-black); }
        #header-area { width: 100%; z-index: 50; }
        #content-area { flex-grow: 1; overflow-y: auto; padding-bottom: 100px; /* Space for player */ }
        
        #player-area { position: fixed; bottom: 0; left: 0; width: 100%; z-index: 2000; }

        /* Override specific page styles when loaded in shell */
        .sidebar { position: relative !important; height: 100% !important; }
        .top-nav { position: relative !important; left: 0 !important; width: 100% !important; }
        .main-content { 
            margin-left: 0 !important; 
            width: 100% !important; 
            padding-top: 20px !important;
            padding-bottom: 20px !important;
            min-height: auto !important;
        }
    </style>
</head>
<body>

<div id="app">
    <div id="sidebar-area">
        <?php include 'partials/sidebar.php'; ?>
    </div>

    <div id="main-view">
        <div id="header-area">
            <?php include 'partials/header.php'; ?>
        </div>
        
        <div id="content-area">
            <?php 
                // Initial Load logic
                $_GET['ajax'] = 1; // Simulate ajax to skip headers in included file
                include 'user/home.php';
            ?>
        </div>
    </div>
</div>

<div id="player-area">
    <?php include 'partials/player.php'; ?>
</div>

<script>
    async function loadPage(url) {
        if (!url) return;
        try {
            // Determine directory of the target page for path fixing
            // e.g. "user/home.php" -> "user/"
            // e.g. "../user/home.php" (from index) -> "user/" (clean up)
            
            // Normalize url relative to root
            // If url is "../user/home.php", and we are at root... this logic is tricky.
            // Let's assume url provided to loadPage is valid from current view.
            // If we are at index.php, and click link in sidebar "user/home.php" (if sidebar fixed).
            // Sidebar currently has "../user/home.php".
            // new URL("../user/home.php", "http://localhost/root/index.php") -> "http://localhost/user/home.php" (Parent of root).
            // This is the sidebar issue. Sidebar links are broken for index.php.
            
            // WE MUST FIX SIDEBAR LINKS MANUALLY or intercept and guess.
            // If url contains "/user/", we assume it's in user dir.
            
            const fetchUrl = new URL(url, document.baseURI);
            fetchUrl.searchParams.set('ajax', '1');

            const response = await fetch(fetchUrl);
            const html = await response.text();

            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Extract content
            let content = doc.querySelector('.main-content');
            if (!content) content = doc.body;

            // --- PATH FIXING LOGIC ---
            // Calculate relative offset based on target URL directory
            // We need to map relative paths in the helper content to be relative to index.php
            
            // Heuristic:
            // If fetched page is "user/home.php", its context is "user/".
            // A link "foo.php" in it means "user/foo.php".
            // A link "../assets/x.png" means "assets/x.png".
            
            // We get the path relative to root.
            // document.baseURI is .../amnhacwebtest/index.php
            // fetchUrl is .../amnhacwebtest/user/home.php
            
            // We want the relative path from root to the file.
            // e.g. "user/"
            const rootPath = document.baseURI.substring(0, document.baseURI.lastIndexOf('/') + 1);
            const targetPath = fetchUrl.href.substring(0, fetchUrl.href.lastIndexOf('/') + 1);
            
            // Check if target is subdirectory
            if (targetPath.startsWith(rootPath)) {
                const relativeDir = targetPath.substring(rootPath.length); // e.g. "user/" or "page/"
                
                if (relativeDir) {
                    // Update attributes: src, href, action
                    const fixAttribute = (element, attr) => {
                        const val = element.getAttribute(attr);
                        if (!val) return;
                        // Ignore absolute, data, #
                        if (val.startsWith('http') || val.startsWith('/') || val.startsWith('#') || val.startsWith('data:')) return;
                        
                        if (val.startsWith('../')) {
                            // "../" cancels one level of relativeDir
                            // If relativeDir is "user/", "../assets" -> "assets"
                            // If relativeDir is "user/sub/", "../assets" -> "user/assets"
                            // Simple case: remove "../" and reduce relativeDir?
                            // For this project: 1 level deep seems standard (user/ page/ favorite/).
                            element.setAttribute(attr, val.substring(3));
                        } else {
                            // Sibling: prepend dir
                            element.setAttribute(attr, relativeDir + val);
                        }
                    };

                    content.querySelectorAll('[src]').forEach(el => fixAttribute(el, 'src'));
                    content.querySelectorAll('[href]').forEach(el => fixAttribute(el, 'href'));
                    content.querySelectorAll('form').forEach(el => fixAttribute(el, 'action'));
                }
            }
            // -------------------------

            const contentArea = document.getElementById('content-area');
            contentArea.innerHTML = content.innerHTML;
            
            // Re-run scripts
            // We also need to fix paths inside scripts? (e.g. window.location.href='...')
            // THIS IS HARD. We rely on loadPage overriding/patching.
            
            const scripts = contentArea.querySelectorAll('script');
            scripts.forEach(oldScript => {
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });

            // Update Browser URL
            const cleanUrl = new URL(url, document.baseURI);
            window.history.pushState({}, '', cleanUrl);

            // AUTO-REFRESH PLAYER
            if (fetchUrl.searchParams.has('song_id')) {
                 const pResponse = await fetch('partials/player.php');
                 const pHtml = await pResponse.text();
                 document.getElementById('player-area').innerHTML = pHtml;
                 // Execute scripts (simplified for brevity)
                 const pContainer = document.getElementById('player-area');
                 Array.from(pContainer.querySelectorAll('script')).forEach(s => {
                     const ns = document.createElement('script');
                     ns.text = s.innerHTML;
                     s.replaceWith(ns);
                 });
            }

        } catch (e) {
            console.error('Navigation failed:', e);
        }
    }

    // Intercept clicks on links
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link) {
            const href = link.getAttribute('href');
            // Check if it's an internal link suitable for AJAX
            if (href && 
                !href.startsWith('#') && 
                !href.startsWith('javascript:') &&
                !href.includes('logout.php') &&
                !link.hasAttribute('target')   // Ignore _blank
            ) {
                e.preventDefault();
                loadPage(href);
            }
        }
    });

    // Provide global navigation for onclick handlers
    window.location.navigateTo = function(url) {
        loadPage(url);
    };
    
    // Also overwrite default location assignment used in existing code?
    // Not easily possible for location.href assignment.
    // We must manually update the onclick handlers or add this polyfill:
    // This is a "dirty" hack but might save modifying 100 files:
    // But modifying home.php is safer.
    
    window.addEventListener('popstate', () => {
        loadPage(window.location.href);
    });
</script>

</body>
</html>
