<template>
  <div class="p-4">
    <h1 class="text-xl font-bold text-left mb-4">Historial de Tickets Resueltos</h1>

    <!-- Barra de búsqueda -->
    <div class="relative w-full sm:w-auto mb-4">
      <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <i class="text-gray-400 fas fa-search"></i>
      </span>
      <input
        type="text"
        v-model="buscarQuery"
        placeholder="Buscar tickets resueltos..."
        class="w-full py-2 placeholder-gray-400 border border-gray-300 rounded-md px-9 focus:border-gray-400 focus:ring focus:ring-gray-400 focus:ring-opacity-5"
      />
    </div>

    <!-- Tabla de Tickets Resueltos -->
    <div v-if="filtrarTicketsResueltos.length > 0" class="overflow-x-auto bg-white rounded-lg shadow-md">
      <!-- Tabla para pantallas grandes -->
      <table class="min-w-full divide-y divide-gray-200 hidden md:table">
        <thead class="bg-white">
          <tr>
            <th class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base">
              N°
            </th>
            <th class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base">
              Categoría
            </th>
            <th class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base">
              Título
            </th>
            <th class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base">
              Prioridad
            </th>
            <th class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base">
              Estado
            </th>
            <th class="px-2 py-2 text-xs font-bold text-center text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base">
              Creado el
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr
            v-for="(ticket, index) in paginatedTickets"
            :key="ticket.id"
            class="transition-colors duration-200 border-b hover:bg-gray-100"
          >
            <td class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm md:text-base">
              {{ index + 1 + (currentPage - 1) * ticketsPerPage }}
            </td>
            <td class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm md:text-base">
              {{ ticket.categoria }}
            </td>
            <td class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm md:text-base">
              {{ ticket.tic_titulo }}
            </td>
            <td class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm md:text-base">
              {{ ticket.prioridad }}
            </td>
            <td class="px-2 py-2 text-xs sm:px-4 sm:py-3 sm:text-sm md:text-base">
              <span class="px-2 py-1 text-xs font-semibold rounded-full sm:text-xs md:text-sm bg-green-100 text-green-800">
                {{ ticket.tic_estado }}
              </span>
            </td>
            <td class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm md:text-base">
              {{ new Date(ticket.created_at).toLocaleDateString() }}
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Tarjetas para pantallas pequeñas -->
      <div class="md:hidden space-y-4">
        <div
          v-for="(ticket, index) in paginatedTickets"
          :key="ticket.id"
          class="bg-white p-4 rounded-lg shadow-md space-y-2 border border-gray-200"
        >
          <div class="text-sm font-bold text-gray-500">N°: {{ index + 1 + (currentPage - 1) * ticketsPerPage }}</div>
          <div class="text-sm font-bold text-gray-500">Categoría: <span class="text-gray-700">{{ ticket.categoria }}</span></div>
          <div class="text-sm font-bold text-gray-500">Título: <span class="text-gray-700">{{ ticket.tic_titulo }}</span></div>
          <div class="text-sm font-bold text-gray-500">Prioridad: <span class="text-gray-700">{{ ticket.prioridad }}</span></div>
          <div class="text-sm font-bold text-gray-500">Estado: 
            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ ticket.tic_estado }}</span>
          </div>
          <div class="text-sm font-bold text-gray-500">Creado el: <span class="text-gray-700">{{ new Date(ticket.created_at).toLocaleDateString() }}</span></div>
        </div>
      </div>
    </div>

    <!-- Paginación -->
    <div class="flex justify-center space-x-2 mt-4">
      <button
        v-for="page in totalPages"
        :key="page"
        @click="currentPage = page"
        :class="{'bg-green-500 text-white': currentPage === page, 'bg-gray-200 text-gray-600': currentPage !== page}"
        class="px-3 py-1 rounded-lg focus:outline-none transition-colors"
      >
        {{ page }}
      </button>
    </div>

  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';

export default {
  setup() {
    const tickets = ref([]);
    const buscarQuery = ref('');
    const currentPage = ref(1);
    const ticketsPerPage = 10; // Cambiado para mostrar 10 filas

    // Computed para filtrar solo tickets resueltos
    const filtrarTicketsResueltos = computed(() => {
      let filteredTickets = tickets.value.filter(ticket => ticket.tic_estado === 'Resuelto');

      // Filtro de búsqueda
      if (buscarQuery.value) {
        const query = buscarQuery.value.toLowerCase();
        filteredTickets = filteredTickets.filter(ticket => {
          return (
            (ticket.tic_titulo && ticket.tic_titulo.toLowerCase().includes(query)) ||
            (ticket.prioridad && ticket.prioridad.toLowerCase().includes(query)) ||
            (ticket.categoria && ticket.categoria.toLowerCase().includes(query))
          );
        });
      }

      return filteredTickets;
    });

    // Paginación
    const paginatedTickets = computed(() => {
      const start = (currentPage.value - 1) * ticketsPerPage;
      const end = start + ticketsPerPage;
      return filtrarTicketsResueltos.value.slice(start, end);
    });

    const totalPages = computed(() => {
      return Math.ceil(filtrarTicketsResueltos.value.length / ticketsPerPage);
    });

    const recargarTickets = async () => {
      try {
        const response = await fetch('support-optener');
        const data = await response.json();

        tickets.value = data.map(ticket => ({
          id: ticket.id,
          tic_titulo: ticket.tic_titulo,
          tic_descripcion: ticket.tic_descripcion,
          tic_estado: ticket.tic_estado,
          prioridad: ticket.prioridad ? ticket.prioridad.pri_nombre : 'N/A',
          categoria: ticket.categoria ? ticket.categoria.cat_nombre : 'N/A',
          created_at: ticket.created_at,
        }));
      } catch (error) {
        console.error('Error al recargar los tickets:', error);
      }
    };

    onMounted(recargarTickets);

    return {
      buscarQuery,
      filtrarTicketsResueltos,
      paginatedTickets,
      currentPage,
      totalPages,
      ticketsPerPage, // Variable añadida para control
    };
  },
};
</script>

<style scoped>
/* Mejora la experiencia en dispositivos móviles */
@media (max-width: 640px) {
  table th,
  table td {
    padding: 0.5rem;
    font-size: 0.75rem;
  }
}

/* Estilo para el paginador */
button {
  cursor: pointer;
}
</style
