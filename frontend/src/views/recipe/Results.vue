<template>
    <Layout>
        <div class="max-w-3xl mx-auto">
            <h2 class="text-xl font-semibold py-6 text-[#003b4a]">{{ meta.total }} Recipes found:</h2>

            <div v-if="results.length > 0">
                <div v-for="recipe in results" :key="recipe.slug" class="flex items-center p-4 border-b">
                    <img :src="recipe.image_url" alt="thumb" class="w-16 h-16 object-cover mr-4" />
                    <div class="flex-1">
                        <router-link :to="{name: 'Detail', params: { slug: recipe.slug }}" class="font-semibold text-lg text-[#003b4a] hover:text-[#f1c232]">
                            <h3 class="font-semibold">{{ recipe.title }}</h3>
                        </router-link>
                        <p class="text-sm text-gray-600 line-clamp-2"> {{ recipe.description }}</p>
                    </div>
                </div>
            </div>

            <p v-else-if="!loading" class="text-center mt-4">
                No recipes found.
            </p>

            <!-- pagination -->
            <div v-if="lastPage > 1" class="flex justify-center space-x-2 mt-6 py-5">
                <button v-for="page in lastPage" :key="page" @click="goToPage(page)" 
                        :class="['px-3 py-1 border rounded', page === currentPage ? 'bg-blue-500' : 'bg-white hover:bg-gray-100']">
                    {{ page }}
                </button>
            </div>

            <p v-if="loading" class="text-center mt-4">Loading...</p>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "@/components/Layout.vue"
import { ref, computed, onMounted, watch } from "vue"
import { useRoute, useRouter } from "vue-router"
import axios from "axios"

const route = useRoute()
const router = useRouter()

const results = ref([])
const loading = ref(false)
const error = ref(null)
const meta = ref({ currentPage: 1, lastPage: 1, total: 1 })
const lastPage = computed(() => meta.value.lastPage)
const currentPage = computed(() => meta.value.currentPage)

const filters = computed(() => {
    if (route.query.filters) {
        try {
            return JSON.parse(route.query.filters)
        } catch {
            return {}
        }
    }
    return {}
})

const fetchResults = async (page = 1) => {
    loading.value = true;
    error.value = null;

    try {
        const params = { page } // add to our params (filters) the page we will be visiting
        if (route.query.filters) {
            params.filters = route.query.filters
        }

        const response = await axios.get("http://localhost:8888/api/recipe/search", { 
            params,
            withCredentials: false
        });
        const data = response.data
        console.log(data)

        if (data.status === "success") {
            const paginator = data.data;
            results.value = paginator.data;
            meta.value = {
                currentPage: paginator.current_page,
                lastPage: paginator.last_page,
                total: paginator.total
            }
        } else {
            results.value = [];
            meta.value = { currentPage: 1, lastPage: 1 }
        }
    } catch (err) {
        results.value = []
        meta.value = { currentPage: 1, lastPage: 1 }
        error.value = err.response?.data?.message || err.message || "Error fetching results"
    } finally {
        loading.value = false;
    }
}

// fetch when mountied
onMounted(() => fetchResults(1));

watch(
    () => route.query,
    () => {
        fetchResults(Number(route.query.page) || 1)
    },
    { deep: true }
)

// update query string with new page
const goToPage = (page) => {
    fetchResults(page)
}

</script>
