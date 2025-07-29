<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#ffffff">
    <title>Theme Test - Connect Pure ERP</title>
    @vite(['resources/css/app.css'])
    
    <!-- Theme Initialization Script -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 
                         (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>
<body class="font-sans antialiased gradient-primary min-h-screen">
    <div class="min-h-screen p-8">
        <!-- Header -->
        <div class="modern-card p-8 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gradient mb-2">Theme System Test</h1>
                    <p class="text-lg text-gray-600 font-medium">Test the dual theme functionality</p>
                </div>
                
                <!-- Theme Toggle Buttons -->
                <div class="flex space-x-4">
                    <!-- Simple Toggle Button -->
                    <button type="button" 
                            onclick="toggleTheme()" 
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all duration-300 font-semibold">
                        Toggle Theme
                    </button>
                    
                    <!-- Set Light Theme -->
                    <button type="button" 
                            onclick="setTheme('light')" 
                            class="px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl transition-all duration-300 font-semibold">
                        Light Mode
                    </button>
                    
                    <!-- Set Dark Theme -->
                    <button type="button"
                            onclick="setTheme('dark')"
                            class="px-6 py-3 bg-gray-800 hover:bg-gray-900 text-white rounded-xl transition-all duration-300 font-semibold">
                        Dark Mode
                    </button>

                    <!-- Debug Button -->
                    <button type="button"
                            onclick="debugTheme()"
                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-all duration-300 font-semibold">
                        Debug Theme
                    </button>
                </div>
            </div>
        </div>

        <!-- Theme Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Current Theme Info -->
            <div class="modern-card p-6">
                <h2 class="text-2xl font-bold mb-4">Current Theme Information</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="font-semibold">Active Theme:</span>
                        <span id="currentTheme" class="font-mono bg-gray-100 px-2 py-1 rounded">light</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">Stored Theme:</span>
                        <span id="storedTheme" class="font-mono bg-gray-100 px-2 py-1 rounded">none</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">System Preference:</span>
                        <span id="systemTheme" class="font-mono bg-gray-100 px-2 py-1 rounded">light</span>
                    </div>
                </div>
            </div>

            <!-- Theme Variables -->
            <div class="modern-card p-6">
                <h2 class="text-2xl font-bold mb-4">Theme Variables</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span>Background:</span>
                        <div class="w-6 h-6 rounded border" style="background: var(--theme-bg)"></div>
                    </div>
                    <div class="flex justify-between">
                        <span>Text:</span>
                        <div class="w-6 h-6 rounded border" style="background: var(--theme-text)"></div>
                    </div>
                    <div class="flex justify-between">
                        <span>Border:</span>
                        <div class="w-6 h-6 rounded border" style="background: var(--theme-border)"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Component Examples -->
        <div class="space-y-8">
            <!-- Cards -->
            <div class="modern-card p-6">
                <h2 class="text-2xl font-bold mb-4">Component Examples</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="modern-card p-4">
                        <h3 class="font-bold mb-2">Card Component</h3>
                        <p class="text-sm">This is a modern card component that adapts to the current theme.</p>
                    </div>
                    <div class="glass-card p-4">
                        <h3 class="font-bold mb-2">Glass Card</h3>
                        <p class="text-sm">This is a glass morphism card with backdrop blur effects.</p>
                    </div>
                    <div class="modern-card p-4">
                        <h3 class="font-bold mb-2">Another Card</h3>
                        <p class="text-sm">All components automatically adapt to the selected theme.</p>
                    </div>
                </div>
            </div>

            <!-- Form Elements -->
            <div class="modern-card p-6">
                <h2 class="text-2xl font-bold mb-4">Form Elements</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="form-label">Text Input</label>
                        <input type="text" class="form-input" placeholder="Enter some text">
                    </div>
                    <div>
                        <label class="form-label">Select Input</label>
                        <select class="form-input">
                            <option>Option 1</option>
                            <option>Option 2</option>
                            <option>Option 3</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="modern-table">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="text-left py-4 px-6">Name</th>
                            <th class="text-left py-4 px-6">Email</th>
                            <th class="text-left py-4 px-6">Role</th>
                            <th class="text-left py-4 px-6">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-4 px-6">John Doe</td>
                            <td class="py-4 px-6">john@example.com</td>
                            <td class="py-4 px-6">Admin</td>
                            <td class="py-4 px-6">Active</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6">Jane Smith</td>
                            <td class="py-4 px-6">jane@example.com</td>
                            <td class="py-4 px-6">User</td>
                            <td class="py-4 px-6">Active</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Instructions -->
        <div class="modern-card p-8 mt-8">
            <h2 class="text-2xl font-bold mb-4">How to Use the Theme System</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-3">Manual Theme Switching:</h3>
                    <ul class="space-y-2 text-sm">
                        <li>• Click the "Toggle Theme" button above</li>
                        <li>• Click "Light Mode" for light theme</li>
                        <li>• Click "Dark Mode" for dark theme</li>
                        <li>• Use keyboard shortcut: Ctrl/Cmd + Shift + T</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-3">Automatic Features:</h3>
                    <ul class="space-y-2 text-sm">
                        <li>• Theme preference is saved automatically</li>
                        <li>• Detects your system's dark/light preference</li>
                        <li>• Smooth transitions between themes</li>
                        <li>• All components adapt automatically</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Theme Toggle Functionality
        function toggleTheme() {
            console.log('Toggle theme clicked');
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            setTheme(newTheme);
        }

        function setTheme(theme) {
            console.log('Setting theme to:', theme);
            
            // Apply theme
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            
            // Update meta theme color
            const metaThemeColor = document.querySelector('meta[name="theme-color"]');
            if (metaThemeColor) {
                const color = theme === 'dark' ? '#0f172a' : '#ffffff';
                metaThemeColor.setAttribute('content', color);
            }
            
            // Update info display
            updateThemeInfo();
            
            // Add transition effect
            document.documentElement.style.transition = 'background-color 0.3s ease, color 0.3s ease';
            setTimeout(() => {
                document.documentElement.style.transition = '';
            }, 300);
        }

        function updateThemeInfo() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const storedTheme = localStorage.getItem('theme') || 'none';
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';

            document.getElementById('currentTheme').textContent = currentTheme;
            document.getElementById('storedTheme').textContent = storedTheme;
            document.getElementById('systemTheme').textContent = systemTheme;
        }

        function debugTheme() {
            console.log('=== THEME DEBUG INFO ===');
            console.log('HTML data-theme:', document.documentElement.getAttribute('data-theme'));
            console.log('LocalStorage theme:', localStorage.getItem('theme'));
            console.log('Body computed style:', window.getComputedStyle(document.body).backgroundColor);
            console.log('CSS variables:');
            console.log('  --theme-bg:', getComputedStyle(document.documentElement).getPropertyValue('--theme-bg'));
            console.log('  --theme-text:', getComputedStyle(document.documentElement).getPropertyValue('--theme-text'));

            // Force apply theme styles
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            if (currentTheme === 'dark') {
                document.body.style.background = 'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)';
                document.body.style.color = '#f8fafc';
            } else {
                document.body.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
                document.body.style.color = '#1f2937';
            }

            // Apply to all cards
            const cards = document.querySelectorAll('.modern-card');
            cards.forEach(card => {
                if (currentTheme === 'dark') {
                    card.style.background = '#1e293b';
                    card.style.borderColor = '#334155';
                    card.style.color = '#f8fafc';
                } else {
                    card.style.background = '#ffffff';
                    card.style.borderColor = '#e5e7eb';
                    card.style.color = '#1f2937';
                }
            });

            alert('Theme debug applied! Check console for details.');
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateThemeInfo();
            
            // Keyboard shortcut
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'T') {
                    e.preventDefault();
                    toggleTheme();
                }
            });
        });
    </script>
</body>
</html>
