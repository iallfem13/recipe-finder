<template>
    <div class="min-h-screen flex flex-col">
        <header class="bg-white shadow-md px-8 py-4 flex items-center justify-between">
            <!-- logo -->
            <div class="flex items-center space-x-2">
                <a href="/">
                    <img src="https://res.cloudinary.com/wildasset/image/upload/v1714663340/logo/logo.svg" alt="Search logo" class="h-10 w-auto"/>
                </a>
            </div>
        
            <!-- nav -->
            <router-link to="/recipe/new" class="bg-[#f1c232] text-[#003b4a] px-4 py-2 rounded-full hover:bg-[#f7d76c] font-semibold shadow-md">
                CREATE RECIPE
            </router-link>
        </header>
    
        <!-- main -->
        <main class="flex-grow">
            <slot />
        </main>

        <footer class="bg-[#003b4a] text-white text-center py-4 text-sm text-gray-500">
            WAC Awesome Search Engine 3000.
        </footer>
    </div>
</template>

