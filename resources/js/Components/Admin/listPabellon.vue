<script setup>
import { ref, onMounted, computed } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import Table from "@/Components/Table.vue";
import ModalCrear from "@/Components/ModalCrear.vue";
import ModalVer from "@/Components/ModalVer.vue";
import ModalEditar from "@/Components/ModalEditar.vue";
import ModalEliminar from "@/Components/ModalEliminar.vue";
import ButtonNuevo from "@/Components/ButtonNuevo.vue";
import axios from "axios";

const pabellons = ref([]);
const sedes = ref([]);
const formFields = ref([]);
const formFieldsVer = ref([]);
const buscarQuery = ref("");
const mostrarModalCrear = ref(false);
const mostrarModalDetalles = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalEliminar = ref(false);
const itemSeleccionado = ref(null);
const archivadoActivo = ref(false);
const currentPage = ref(1);
const itemsPerPage = ref(6);
const totalPages = ref(1);

const headers = ["N°", "Pabellon", "Sede", "Estado"];

const toggleArchivedFilter = () => {
    archivadoActivo.value = !archivadoActivo.value;
    localStorage.setItem("archivadoActivo", archivadoActivo.value);
};

const resetArchivedFilter = () => {
    archivadoActivo.value = false;
    localStorage.setItem("archivadoActivo", archivadoActivo.value);
};

const getArchivedFilterFromLocalStorage = () => {
    const storedValue = localStorage.getItem("archivadoActivo");
    archivadoActivo.value = storedValue === "true";
};

const filtrarPabellones = computed(() => {
    let filteredPabellones = pabellons.value;

    if (buscarQuery.value) {
        const query = buscarQuery.value.toLowerCase();
        filteredPabellones = filteredPabellones.filter((pabellon) => {
            return pabellon.pab_nombre.toLowerCase().includes(query);
        });
    }

    if (archivadoActivo.value) {
        return filteredPabellones.filter(
            (pabellon) => pabellon.pab_activo === 0
        );
    } else {
        return filteredPabellones.filter(
            (pabellon) => pabellon.pab_activo === 1
        );
    }
});

const mapPabellonData = (pabellon, index, totalPabellones) => {
    const startIndex = (currentPage.value - 1) * itemsPerPage.value;
    return {
        id: pabellon.id,
        pab_nombre: pabellon.pab_nombre,
        sed_id: pabellon.sed_id,
        sed_nombre: pabellon.sede.sed_nombre,
        pab_activo: pabellon.pab_activo,
        row_number: totalPabellones - (startIndex + index),
    };
};

const changePage = (pageNumber) => {
    currentPage.value = pageNumber;
    localStorage.setItem("currentPage", pageNumber);
    fetchPabellones(pageNumber);
};

const fetchPabellones = async (page = 1) => {
    try {
        const response = await axios.get(`/pabellonespaginated?page=${page}`);

        const totalPabellones = response.data.total;
        const pabellonData = response.data.data.map((pabellon, index) =>
            mapPabellonData(pabellon, index, totalPabellones)
        );

        pabellons.value = pabellonData;
        currentPage.value = response.data.current_page;
        itemsPerPage.value = response.data.per_page;
        totalPages.value = response.data.last_page;
    } catch (error) {
        toast.error("Error al cargar los pabellones", {
            autoClose: 5000,
            position: "bottom-right",
            style: {
                width: "400px",
            },
            className: "border-l-4 border-red-500 p-4",
        });
    }
};

const fetchSedes = async () => {
    try {
        const response = await axios.get("/sedes");
        sedes.value = response.data
            .filter((sede) => sede.sed_activo)
            .map((sede) => ({
                value: sede.id,
                text: sede.sed_nombre,
            }));
        formFields.value = formFields.value.map((field) => {
            if (field.name === "sed_id") {
                return {
                    ...field,
                    options: sedes.value,
                };
            }
            return field;
        });
        formFieldsVer.value = formFieldsVer.value.map((field) => {
            if (field.name === "sed_id") {
                return {
                    ...field,
                    options: sedes.value,
                };
            }
            return field;
        });
    } catch (error) {
        toast.error("Error al cargar las sedes", {
            autoClose: 5000,
            position: "bottom-right",
            style: {
                width: "400px",
            },
            className: "border-l-4 border-red-500 p-4",
        });
    }
};

formFields.value = [
    { name: "pab_nombre", label: "Pabellon", type: "text", required: true },
    {
        name: "sed_id",
        label: "Sede",
        type: "select",
        required: true,
        options: sedes.value,
    },
    { name: "pab_activo", label: "Estado", type: "boolean" },
];

formFieldsVer.value = [
    { name: "pab_nombre", label: "Pabellon", type: "text" },
    { name: "sed_nombre", label: "Sede", type: "text" },
];

const eliminarItem = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.delete(
                `/pabellones/${itemSeleccionado.value.id}/eliminar`
            );
            await fetchPabellones();
            mostrarModalEliminar.value = false;
            alertaEliminar();
        } catch (error) {
            toast.error(
                "No puedes eliminar este pabellon, por el momento solo desactivelo",
                {
                    autoClose: 5000,
                    position: "bottom-right",
                    style: {
                        width: "400px",
                    },
                    className: "border-l-4 border-red-500 p-4",
                }
            );
        }
    }
};

const cerrarCrearModal = async () => {
    mostrarModalCrear.value = false;
    currentPage.value = 1;
    localStorage.setItem("currentPage", 1);
    await fetchPabellones(currentPage.value);
};

const abrirDetallesModal = (pabellon) => {
    itemSeleccionado.value = pabellon;
    mostrarModalDetalles.value = true;
};

const cerrarDetallesModal = async () => {
    mostrarModalDetalles.value = false;
    localStorage.setItem("currentPage", 1);
    await fetchPabellones(currentPage.value);
};

const abrirEditarModal = async (pabellon) => {
    itemSeleccionado.value = pabellon;
    await fetchSedes();
    mostrarModalEditar.value = true;
};

const cerrarEditarModal = async () => {
    mostrarModalEditar.value = false;
    currentPage.value = 1;
    localStorage.setItem("currentPage", 1);
    await fetchPabellones(currentPage.value);
};

const abrirEliminarModal = (pabellon) => {
    itemSeleccionado.value = pabellon;
    mostrarModalEliminar.value = true;
};

const cerrarEliminarModal = async () => {
    mostrarModalEliminar.value = false;
    currentPage.value = 1;
    localStorage.setItem("currentPage", 1);
    await fetchPabellones(currentPage.value);
};

const alertaEliminar = () => {
    fetchPabellones();
    toast.success("Pabellon eliminado correctamente", {
        autoClose: 3000,
        position: "bottom-right",
        style: {
            width: "400px",
        },
        className: "border-l-4 border-green-500 p-4",
    });
};

onMounted(() => {
    fetchPabellones();
    fetchSedes();
    getArchivedFilterFromLocalStorage();
});
</script>

<template>
    <div class="p-6">
        <h1 class="mb-6 text-sm font-bold text-gray-500 sm:text-lg md:text-xl">
            Lista de Pabellones
        </h1>
        <div
            class="flex flex-col items-center justify-between mb-4 sm:flex-row"
        >
            <div
                class="relative w-full mb-2 sm:w-auto sm:mb-0 flex items-center"
            >
                <span
                    class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                >
                    <i class="text-gray-400 fas fa-search"></i>
                </span>
                <input
                    type="text"
                    v-model="buscarQuery"
                    placeholder="Buscar..."
                    class="w-full py-2 placeholder-gray-400 border border-gray-300 rounded-md px-9 sm:w-auto focus:border-gray-400 focus:ring focus:ring-gray-400 focus:ring-opacity-5"
                />
                <button
                    @click="toggleArchivedFilter"
                    class="ml-2 flex items-center px-2 py-1 rounded-md hover:bg-opacity-80 font-semibold"
                    :class="
                        archivadoActivo
                            ? 'bg-red-500 text-white'
                            : 'bg-[#2EBAA1] text-white'
                    "
                >
                    <i class="fas fa-filter mr-1"></i>
                    <span>{{ archivadoActivo ? "Archivado" : "Activo" }}</span>
                    <i
                        class="fas fa-times ml-2"
                        v-if="archivadoActivo"
                        @click.stop="resetArchivedFilter"
                    ></i>
                </button>
            </div>
            <ButtonNuevo @click="mostrarModalCrear = true" />
        </div>

        <Table
            :headers="headers"
            :items="filtrarPabellones"
            :currentPage="currentPage"
            :totalPages="totalPages"
            entityType="pabellon"
            @view="abrirDetallesModal"
            @edit="abrirEditarModal"
            @eliminar="abrirEliminarModal"
            @changePage="changePage"
        />

        <ModalCrear
            v-if="mostrarModalCrear"
            :formFields="formFields"
            :sedes="sedes"
            itemName="Pabellon"
            endpoint="/pabellones"
            @cerrar="cerrarCrearModal"
            @crear="fetchPabellones"
        />

        <ModalVer
            v-if="mostrarModalDetalles"
            :item="itemSeleccionado"
            itemName="Pabellon"
            :formFieldsVer="formFieldsVer"
            :mostrarModalDetalles="mostrarModalDetalles"
            @close="cerrarDetallesModal"
        />

        <ModalEditar
            v-if="mostrarModalEditar"
            :item="itemSeleccionado"
            itemName="Pabellon"
            :formFields="formFields"
            :sedes="sedes"
            :mostrarModalEditar="mostrarModalEditar"
            endpoint="/pabellones"
            @cerrar="cerrarEditarModal"
            @update="fetchPabellones"
        />

        <ModalEliminar
            v-if="mostrarModalEliminar"
            :item="itemSeleccionado"
            itemName="Pabellon"
            fieldName="name"
            @cancelar="cerrarEliminarModal"
            @confirmar="eliminarItem"
        />
    </div>
</template>
