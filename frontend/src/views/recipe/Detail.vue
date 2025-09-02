<template>
    <Layout>
        <div class="max-w-3xl mx-auto p-4">
            <div v-if="error" class="text-red-500 text-center">{{ error }}</div>

            <div v-else-if="recipe">
                <!-- Recipe body -->
                <h2 class="text-3xl text-[#003b4a] font-bold mb-2">{{ recipe.title }}</h2>

                <div class="text-gray-600 mb-4">
                    By: {{ recipe.user.name }} | Servings: {{ recipe.servings }} | Cook time: {{ recipe.cook_time }} mins
                </div>

                <img v-if="recipe.image_url" :src="recipe.image_url" :alt="recipe.title" class="w-full h-64 object-cover rounded-lg mb-4" />

                <p v-if="recipe.description" class="mb-4">
                    {{ recipe.description }}
                </p>

                <!-- ingredient list -->
                <div class="mb-4">
                    <h3 class="text-xl font-semibold mb2 text-[#003b4a]">Ingredients</h3>
                    <ul class="list-disc list-inside">
                        <li v-for="ingredient in recipe.ingredients" :key="ingredient.id">
                            {{ ingredient.pivot.quantity }} {{ ingredient.name }} 
                                {{ ingredient.pivot.presentation ? `(${ingredient.pivot.presentation})` : '' }}
                        </li>
                    </ul>
                </div>

                <!-- instructions -->
                <div>
                    <h2 class="text-xl font-semibold mb-2 text-[#003b4a]">Instructions</h2>
                    <li v-for="(instruction, step) in recipe.instructions" :key="instruction">
                            {{ step }}. {{ instruction }}
                    </li>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "@/components/Layout.vue"
import { ref, onMounted } from "vue"
import { useRoute } from "vue-router"
import axios from "axios"

const route = useRoute()
const slug = route.params.slug

const recipe = ref(null)
const error = ref("")

const fetchRecipe = async () => {
    error.value = ""

    try {
        const response = await axios.get(`http://localhost:8888/api/recipe/${slug}`)
        const data = response.data

        console.log(data)

        if (data.status === "success") {
            recipe.value = data.data
        } else {
            error.value = data.message || "Recipe not found"
        }
    } catch (err) {
        error.value = err.response?.data?.message || err.message || "Error fetching recipe"
    } 
}

onMounted(() => {
    fetchRecipe()
})

</script>