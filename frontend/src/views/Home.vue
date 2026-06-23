<template>
    <Layout>
        <div class="relative h-[70vh] flex flex-col items-center justify-center text-center bg-[url('https://res.cloudinary.com/wildasset/image/upload/w_1900,f_auto,q_auto/web/branding-2024/homepage-1.jpg')] bg-cover bg-center bg-no-repeat">

            <div class="z-10 w-full text-white px-4">
                <h2 class="text-4xl text-[#003b4a] font-semibold m-6">Recipe Search 3000</h2>

                <div class="relative max-w-3xl mb-4 mx-auto" id="filter-container">
                    <div class ="flex items-center border bg-white rounded-full px-2 py-1 gap-1 text-black flex-nowrap overflow-x-auto">
                        <span v-for="(f, key) in filters" :key="key" class="bg-[#003b4a] text-white px-2 py-1 rounded-lg flex items-center gap-2 flex-shrink-0">
                            {{ f.type }}: {{ f.value }}
                            <button @click="removeFilter(key)" class="px-1 text-black bg-[#f1c232] rounded-full hover:text-gray-200">x</button>
                        </span>

                        <span v-if="currentType" class="bg-gray-200 text-black px-2 py-1 rounded-full">
                            {{ currentType }}:
                        </span>

                        <span v-if="availableOptions.length">
                            <input v-model="currentValue" :placeholder="currentType ? `Enter ${currentType}...` : 'Select filter type...'" 
                                class=" min-w-[100px] bg-transparent px-1 py-1 outline-none" 
                                @focus="showDropdown = true" 
                                @keydown.enter.prevent="addFilter" 
                                @keydown.space.prevent="addFilter"
                                @keydown.backspace="handleBackspace"
                            />
                        </span>

                        <button @click="search" class="ml-auto bg-green-600 text-white px-6 py-2 rounded-full hover:bg-green-700 font-semibold">Search</button>
                    </div>

                    <ul v-if="showDropdown && !currentType" class="absolute top-full left-0 bg-white text-black border rounded mt-1 min-w-max z-20">
                        <li v-for="option in availableOptions" :key="option" @click="selectType(option)" class="px-2 py-1 hover:bg-gray-200 curser:pointer">
                            {{ option }}
                        </li>
                    </ul>                    
                </div>

                <!-- floating categories presets -->
                <div class="flex flex-wrap gap-2 justify-center">
                    <button v-for="category in categories" :key="category.id" @click="filterByCategory(category.slug)" class="bg-[#f1c232] text-[#003b4a] px-5 py-1 rounded-full hover:bg-white">
                        {{ category.name }}
                    </button>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "@/components/Layout.vue"
import { computed, ref, onMounted, onBeforeUnmount } from "vue"
import { useRouter } from "vue-router"

const router = useRouter()

const filters = ref([])
const currentType = ref("")
const currentValue = ref("")
const showDropdown = ref(false)

// categories
const categories = ref([
    { id: 1, name: "Trending", slug: "trending" },
    { id: 2, name: "Breakfast", slug: "breakfast" },
    { id: 3, name: "Easy", slug: "easy" }
])

const allOptions = ['author', 'ingredient', 'keyword']

const availableOptions = computed(() => allOptions.filter(type => !filters.value.some(f => f.type === type)))

function selectType(type) {
    currentType.value = type
    currentValue.value = ""
    showDropdown.value = true

    const inputEl = document.querySelector("#filter-container input")
    inputEl && inputEl.focus()
}

function handleClickOutside(event) {
    const container = document.getElementById("filter-container")
    if (!container.contains(event.target)) {
        showDropdown.value = false
    }
}

function handleBackspace(event) {
    if (currentValue.value !== "") {
        return;
    }

    if (currentType.value) {
        event.preventDefault()
        currentType.value = ""
        showDropdown.value = true
    } else if (filters.value.length > 0) {
        event.preventDefault()
        filters.value.pop()
    }
}

function addFilter() {
    if (currentType.value && currentValue.value.trim() !== "") {
        filters.value.push({ type: currentType.value, value: currentValue.value })
        currentType.value = ""
        currentValue.value = ""
        showDropdown.value = true
    }
}

function removeFilter(index) {
    filters.value.splice(index, 1)
}

function search() {
    const payload = {}

    if (filters.value.length === 0 && currentValue.value.trim() !== "") {
        filters.value.push({ type: "keyword", value: currentValue.value })
        currentValue.value = ""
    }

    filters.value.forEach(f => {
        payload[f.type] = f.value
    })

    if (filters.value.length > 0) {
        router.push({ name: "Search", query: { filters: JSON.stringify(payload) } })
    }
}

onMounted(() => document.addEventListener("click", handleClickOutside))
onBeforeUnmount(() => document.removeEventListener("click", handleClickOutside))
</script>

