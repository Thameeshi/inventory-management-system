<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Inventory Management System" />
    
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <img src="/images/logo1.png" alt="Logo" class="h-16 w-auto" />
                    </div>
                    
                    <!-- Auth Links -->
                    <div class="flex items-center space-x-4">
                        <Link
                            v-if="canLogin"
                            :href="route('login')"
                            class="text-gray-600 hover:text-gray-900 px-4 py-2 text-sm font-medium transition-colors duration-200"
                        >
                            Sign In
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-md text-sm font-semibold transition-colors duration-200"
                        >
                            Get Started
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="py-16 sm:py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <!-- Main Heading -->
                    <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-4">
                        Inventory Management
                        <span class="text-blue-600">Made Simple</span>
                    </h1>
                    
                    <!-- Subtitle -->
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-8">
                        Track, manage, and optimize your inventory with ease. 
                        Real-time updates, powerful search, and complete transaction history.
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <Link
                            v-if="canLogin"
                            :href="route('login')"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-md transition-colors duration-200"
                        >
                            <span>Access Dashboard</span>
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-md border border-gray-300 transition-colors duration-200"
                        >
                            Create Account
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Everything You Need</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        A complete solution for managing your inventory efficiently
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Stock Tracking</h3>
                        <p class="text-gray-600 text-sm">
                            Monitor stock levels in real-time. Get alerts when items are running low.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Easy Operations</h3>
                        <p class="text-gray-600 text-sm">
                            Add and deduct items with a few clicks. Batch operations supported.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Transaction History</h3>
                        <p class="text-gray-600 text-sm">
                            Complete audit trail for all inventory movements and changes.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div>
                        <div class="text-3xl font-bold text-blue-600 mb-1">100%</div>
                        <div class="text-sm text-gray-600">Free & Open Source</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-blue-600 mb-1">Laravel 12</div>
                        <div class="text-sm text-gray-600">Backend Framework</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-blue-600 mb-1">Vue 3</div>
                        <div class="text-sm text-gray-600">Frontend Framework</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-blue-600 mb-1">Inertia.js</div>
                        <div class="text-sm text-gray-600">SPA Architecture</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Ready to Get Started?</h2>
                <p class="text-gray-600 mb-8 max-w-xl mx-auto">
                    Start managing your inventory today. It's free and takes only a minute to set up.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <Link
                        v-if="canLogin"
                        :href="route('login')"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-md transition-colors duration-200"
                    >
                        Sign In
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-md transition-colors duration-200"
                    >
                        Create Account
                    </Link>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-50 border-t border-gray-200 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center space-x-2">
                        <img src="/images/logo1.png" alt="Logo" class="h-8 w-auto" />
                    </div>
                    <p class="text-sm text-gray-500">
                        Built with Laravel, Vue.js & Tailwind CSS
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
