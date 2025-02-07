<script setup>
import { ref, onMounted, computed, onBeforeUnmount } from "vue";

const isMobile = ref(false);

const props = defineProps({
    tickets: {
        type: Array,
        required: true,
    },
    currentPage: {
        type: Number,
        required: true,
    },
    totalPages: {
        type: Number,
        required: true,
    },
    success: String,
});

const visiblePages = computed(() => {
    const pages = [];
    const maxVisiblePages = isMobile.value ? 3 : 5;
    const pageRange = Math.floor(maxVisiblePages / 2);

    if (props.totalPages <= maxVisiblePages) {
        for (let i = 1; i <= props.totalPages; i++) {
            pages.push(i);
        }
    } else {
        pages.push(1);

        if (props.currentPage - pageRange > 2) {
            pages.push("...");
        }

        const startPage = Math.max(2, props.currentPage - pageRange);
        const endPage = Math.min(
            props.totalPages - 1,
            props.currentPage + pageRange
        );

        for (let i = startPage; i <= endPage; i++) {
            pages.push(i);
        }

        if (props.currentPage + pageRange < props.totalPages - 1) {
            pages.push("...");
        }

        pages.push(props.totalPages);
    }

    return pages;
});

const handleResize = () => {
    isMobile.value = window.innerWidth <= 1244;
};

onMounted(() => {
    handleResize();
    window.addEventListener("resize", handleResize);
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", handleResize);
});

const emit = defineEmits([
    "comment",
    "open",
    "close",
    "asign",
    "view",
    "edit",
    "eliminar",
    "changePage",
]);
</script>

<template>
    <div>
        <div v-if="success" class="alert alert-success">
            {{ success }}
        </div>
        <div
            v-if="tickets.length > 0"
            class="grid grid-cols-1 gap-4 sm:grid-cols-3 lg:grid-cols-5"
        >
            <div
                v-for="ticket in tickets"
                :key="ticket.id"
                class="relative w-full cursor-pointer transition duration-300 transform hover:scale-105 hover:shadow-lg"
                @click="$emit('comment', ticket)"
            >
                <span
                    class="absolute top-0 left-0 w-full h-full mt-1 ml-1 rounded-lg"
                    :class="{
                        'bg-orange-600': ticket.tic_estado === 'Abierto',
                        'bg-gray-600': ticket.tic_estado === 'Asignado',
                        'bg-blue-600': ticket.tic_estado === 'En progreso',
                        'bg-green-600': ticket.tic_estado === 'Resuelto',
                        'bg-red-600': ticket.tic_estado === 'Cerrado',
                        'bg-yellow-600': ticket.tic_estado === 'Reabierto',
                    }"
                ></span>
                <div
                    class="relative h-full p-6 bg-white border-2 rounded-lg"
                    :class="{
                        'border-orange-600': ticket.tic_estado === 'Abierto',
                        'border-gray-600': ticket.tic_estado === 'Asignado',
                        'border-blue-600': ticket.tic_estado === 'En progreso',
                        'border-green-600': ticket.tic_estado === 'Resuelto',
                        'border-red-600': ticket.tic_estado === 'Cerrado',
                        'border-yellow-600': ticket.tic_estado === 'Reabierto',
                    }"
                >
                    <div
                        class="absolute px-3 py-1 text-xs font-semibold transition-colors duration-300 rounded-full shadow-md top-2 right-2"
                        :class="{
                            'bg-orange-600 text-white':
                                ticket.tic_estado === 'Abierto',
                            'bg-gray-600 text-white':
                                ticket.tic_estado === 'Asignado',
                            'bg-blue-600 text-white':
                                ticket.tic_estado === 'En progreso',
                            'bg-green-600 text-white':
                                ticket.tic_estado === 'Resuelto',
                            'bg-red-600 text-white':
                                ticket.tic_estado === 'Cerrado',
                            'bg-yellow-600 text-white':
                                ticket.tic_estado === 'Reabierto',
                        }"
                    >
                        {{ ticket.tic_estado }}
                    </div>
                    <div>
                        <h2
                            class="text-lg font-bold text-gray-800 uppercase truncate mt-2"
                        >
                            {{ ticket.cat_nombre }}
                        </h2>
                        <hr class="my-2 border-gray-200" />
                    </div>
                    <div class="flex flex-col space-y-2 text-sm text-gray-600">
                        <span
                            ><strong>Título:</strong>
                            {{ ticket.tic_titulo || "N/A" }}</span
                        >
                        <span
                            ><strong>Prioridad:</strong>
                            {{ ticket.pri_nombre || "N/A" }}</span
                        >
                        <span>
                            <strong>Soporte:</strong>
                            {{ ticket.soporte_nombre || "N/A" }}
                        </span>
                        <span>
                            <strong>Creado el:</strong>
                            {{
                                new Date(ticket.created_at).toLocaleDateString()
                            }}
                        </span>
                        <span>
                            <strong>Actualizado el:</strong>
                            {{
                                new Date(ticket.updated_at).toLocaleDateString()
                            }}
                        </span>
                    </div>

                    <div class="flex mt-4 space-x-1 justify-between">
                        <div class="flex space-x-1">
                            <button
                                v-if="ticket.tic_estado === 'Cerrado'"
                                @click.stop="$emit('open', ticket)"
                                class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-green-300 to-green-500 hover:from-green-400 hover:to-green-600"
                                title="Reabrir Ticket"
                            >
                                <i class="fa-solid fa-lock-open"></i>
                            </button>

                            <button
                                v-if="
                                    ticket.tic_estado === 'Abierto' ||
                                    ticket.tic_estado === 'Asignado' ||
                                    ticket.tic_estado === 'Reabierto'
                                "
                                @click.stop="$emit('close', ticket)"
                                class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-purple-300 to-purple-500 hover:from-purple-400 hover:to-purple-600"
                                title="Cerrar Ticket"
                            >
                                <i class="fa-solid fa-lock"></i>
                            </button>
                        </div>

                        <div class="flex space-x-1">
                            <button
                                v-if="
                                    ticket.tic_estado === 'Abierto' ||
                                    ticket.tic_estado === 'Asignado' ||
                                    ticket.tic_estado === 'Reabierto'
                                "
                                @click.stop="$emit('asign', ticket)"
                                class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-blue-300 to-blue-500 hover:from-blue-400 hover:to-blue-600"
                                title="Asignar Soporte"
                            >
                                <i class="mr-1 fas fa-user-plus"></i>
                            </button>

                            <button
                                @click.stop="$emit('view', ticket)"
                                class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-gray-300 to-gray-500 hover:from-gray-400 hover:to-gray-600"
                                title="Ver detalles"
                            >
                                <i class="mr-1 fas fa-eye"></i>
                            </button>

                            <button
                                @click.stop="$emit('edit', ticket)"
                                class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-teal-300 to-teal-500 hover:from-teal-400 hover:to-green-600"
                                title="Editar"
                            >
                                <i class="mr-1 fas fa-edit"></i>
                            </button>

                            <button
                                @click.stop="$emit('eliminar', ticket)"
                                class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-red-300 to-red-500 hover:from-red-400 hover:to-red-600"
                                title="Eliminar"
                            >
                                <i class="mr-1 fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 flex justify-center" v-if="totalPages > 1">
            <button
                v-for="page in visiblePages"
                :key="page"
                :disabled="page === '...'"
                :class="[
                    currentPage === page
                        ? 'bg-[#2EBAA1] text-white'
                        : 'bg-white text-[#2EBAA1]',
                    'mx-2 px-3 py-1 rounded-lg pagination-button',
                    { 'cursor-default': page === '...' },
                ]"
                @click="page !== '...' && $emit('changePage', page)"
            >
                {{ page }}
            </button>
        </div>

        <div
            v-else
            colspan="7"
            class="py-3 text-xs text-left text-gray-500 sm:text-sm"
        >
            <p>No se encontraron resultados.</p>
        </div>
    </div>
</template>

<style scoped>
@media (max-width: 640px) {
    .pagination-button {
        margin: 0.5rem 0.1rem;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
}
</style>
