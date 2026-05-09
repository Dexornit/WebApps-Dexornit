<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Installer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-2xl">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Application Installer</h1>
            <p class="text-gray-600">Complete the form below to install your application</p>
        </div>

        <!-- Environment Check -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Environment Requirements</h2>
            
            <!-- PHP Version -->
            <div class="flex items-center justify-between py-2 border-b">
                <span class="text-gray-700">{{ $requirements['php_version']['name'] }}</span>
                <div class="flex items-center">
                    <span class="text-sm text-gray-500 mr-2">{{ $requirements['php_version']['current'] }}</span>
                    @if($requirements['php_version']['status'])
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </div>
            </div>

            <!-- Extensions -->
            @foreach($requirements['extensions'] as $ext => $data)
            <div class="flex items-center justify-between py-2 border-b">
                <span class="text-gray-700">{{ $data['name'] }}</span>
                @if($data['status'])
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                @else
                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                @endif
            </div>
            @endforeach

            <!-- Permissions -->
            @foreach($requirements['permissions'] as $perm => $data)
            <div class="flex items-center justify-between py-2 border-b">
                <span class="text-gray-700">{{ $data['name'] }} writable</span>
                @if($data['status'])
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                @else
                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Installation Form -->
        <div class="bg-white rounded-lg shadow-md p-6" x-data="{ dbType: 'sqlite', installing: false }">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Installation Configuration</h2>

            @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('installer.install') }}" @submit="installing = true">
                @csrf

                <!-- Application Settings -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-3">Application Settings</h3>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Application Name</label>
                        <input type="text" name="app_name" value="{{ old('app_name', 'Wanseven') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Application URL</label>
                        <input type="url" name="app_url" value="{{ old('app_url', url('/')) }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>
                </div>

                <!-- Database Configuration -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-3">Database Configuration</h3>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Database Type</label>
                        <select name="db_type" x-model="dbType" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="sqlite">SQLite (Recommended for testing)</option>
                            <option value="mysql">MySQL</option>
                        </select>
                    </div>

                    <div x-show="dbType === 'mysql'" x-cloak>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-2">Database Host</label>
                            <input type="text" name="db_host" value="{{ old('db_host', 'localhost') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-2">Database Port</label>
                            <input type="text" name="db_port" value="{{ old('db_port', '3306') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-2">Database Name</label>
                            <input type="text" name="db_name" value="{{ old('db_name') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-2">Database Username</label>
                            <input type="text" name="db_user" value="{{ old('db_user') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-2">Database Password</label>
                            <input type="password" name="db_pass" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Admin Account -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-3">Admin Account</h3>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Admin Name</label>
                        <input type="text" name="admin_name" value="{{ old('admin_name', 'Admin') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Admin Email</label>
                        <input type="email" name="admin_email" value="{{ old('admin_email') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Admin Password</label>
                        <input type="password" name="admin_password" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required minlength="8">
                        <p class="text-sm text-gray-500 mt-1">Minimum 8 characters</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Confirm Password</label>
                        <input type="password" name="admin_password_confirmation" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required minlength="8">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" 
                            :disabled="installing"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                        <span x-show="!installing">Install Application</span>
                        <span x-show="installing" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Installing...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-gray-600 text-sm">
            <p>Make sure all environment requirements are met before installing</p>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>
