<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
/*
|--------------------------------------------------------------------------
| IMPORTANTE
|--------------------------------------------------------------------------
|
| Este componente es únicamente un ejemplo.
|
| Reemplace:
|
| - Layout principal
| - Componentes UI
| - Botones
| - Selects
| - Switches
|
| según la estructura de su proyecto.
|
*/
import App from '@/Layouts/App.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import Switch from '@/Components/ui/switch.vue';
import Label from '@/Components/Label.vue';
import Button from '@/Components/Button.vue';

const props = defineProps({
  logs: {
    type: Array,
    default: null
  },
  availableLogs: {
    type: Array,
    default: []
  },
  selectedLog: {
    type: String,
    default: ''
  },
  currentFilters: {
    type: Object,
    default: null
  },
});

const searchForm = useForm({})
const clearForm = useForm({
    log: null
});
/*
|--------------------------------------------------------------------------
| Colores
|--------------------------------------------------------------------------
*/

const levelColors = {
    debug: 'bg-gray-100 text-gray-800 border-gray-300',
    info: 'bg-blue-100 text-blue-800 border-blue-300',
    notice: 'bg-cyan-100 text-cyan-800 border-cyan-300',
    warning: 'bg-yellow-100 text-yellow-800 border-yellow-300',
    error: 'bg-red-100 text-red-800 border-red-300',
    critical: 'bg-red-200 text-red-900 border-red-400',
    alert: 'bg-orange-200 text-orange-900 border-orange-400',
    emergency: 'bg-purple-200 text-purple-900 border-purple-400',
};

const levelRowColors = {
    debug: 'border-l-gray-400',
    info: 'border-l-blue-400',
    notice: 'border-l-cyan-400',
    warning: 'border-l-yellow-500',
    error: 'border-l-red-500',
    critical: 'border-l-red-700',
    alert: 'border-l-orange-600',
    emergency: 'border-l-purple-700',
};


/*
|--------------------------------------------------------------------------
| Constantes
|--------------------------------------------------------------------------
*/
const ALLOWED_CHANNELS = ["local", "stack", "daily"];
const ALLOWED_LEVELS = ["debug", "info", "notice", "warning", "error", "critical", "alert", "emergency"];

/*
|--------------------------------------------------------------------------
| Estados
|--------------------------------------------------------------------------
*/
const filterLog = ref(
    props.currentFilters?.log ?? props.selectedLog ?? ''
);
const selectedLevel = ref(
    props.currentFilters?.level ?? 'all'
);

const selectedChannel = ref(
    props.currentFilters?.channel ?? 'all'
);
const filterDate = ref(
    props.currentFilters?.date ?? ''
);

const filterHasException = ref(
    props.currentFilters?.has_exception == null
        ? null
        : Boolean(Number(props.currentFilters.has_exception))
);

const expandedRows = ref(new Set());

const toggleException = (checked) => {
    filterHasException.value = checked
        ? true
        : null;
};

watch(
    [
        filterLog,
        selectedLevel,
        selectedChannel,
        filterHasException,
        filterDate,
    ],
    ([log, level, channel, hasException, date]) => {

        const query = {};

        if (log && log !== '') {
            query.log = log;
        }

        if (level && level !== 'all') {
            query.level = level;
        }

        if (channel && channel !== 'all') {
            query.channel = channel;
        }

        if (hasException !== null) {
            query.has_exception = hasException ? 1 : 0;
        }

        if (date) {
            query.date = date;
        }

        searchForm.get(route('log.index', query), {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    }
);
/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/
const clearLog = () => {

    if (
        ! confirm(
            `¿Desea vaciar ${currentLog.value?.name} (${currentLog.value?.size_human})?`
        )
    ) {
        return;
    }

    clearForm.log = filterLog.value;

    clearForm.delete(
        route('log.clear'),
        {
            preserveScroll: true,
            onSuccess: () => {

                expandedRows.value = new Set();

            }
        }
    );
};

const resetFilters = () => {
    selectedLevel.value = 'all';
    selectedChannel.value = 'all';
    filterDate.value = '';
    filterHasException.value = null;
    filterLog.value = props.selectedLog;
};

const toggleRow = (index) => {
    const next = new Set(expandedRows.value);

    if (next.has(index)) {
        next.delete(index);
    } else {
        next.add(index);
    }

    expandedRows.value = next;
};

const lastModified = computed(() => {
    return props.availableLogs.find(
        l => l.name === filterLog.value
    )?.modified_at;
});
const currentLog = computed(() => {

    return props.availableLogs.find(
        file => file.name === filterLog.value
    );

});
</script>
<template>
  <App>
    <template #content>
      <div class="min-h-screen dark:bg-gray-950 p-4 md:p-6">
        <div class="max-w-[1400px] mx-auto space-y-4">
          <header class="flex items-center gap-3">
            <i class="far fa-newspaper text-3xl text-emerald-400"></i>
            <h1 class="text-2xl font-bold">Visor de Logs</h1>
          </header>
          <!-- log seleccionado -->
          <Card class="dark:bg-gray-900 border-gray-800">
            <CardContent class="p-4">
              <div class="flex items-center gap-4 flex-wrap">
                <div class="flex items-center gap-2">
                  <Label class="text-sm whitespace-nowrap">Archivo:</Label>
                  <Select v-model="filterLog">
                    <SelectTrigger class="w-36 bg-gray-200 dark:bg-gray-800 border-gray-700">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent class="dark:bg-gray-800 border-gray-700">
                      <SelectItem v-for="file in availableLogs" :key="file.name" :value="file.name"
                        class="focus:bg-gray-700 focus:text-gray-100">
                        {{ file.name }} {{ file.size_human }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <span class="text-xs">
                  Última modificación: {{lastModified}}
                </span>
              </div>
              <Button
                  @click="clearLog"
                  variant="destructive"
                  size="sm"
                  class="h-9"
              >
                  <i class="fas fa-trash mr-1"></i>

                  Vaciar log
              </Button>
            </CardContent>
          </Card>

          <!-- filtros -->
          <Card class="dark:bg-gray-900 border-gray-800">
            <CardHeader class="pb-3 pt-4 px-4">
              <CardTitle class="text-sm font-medium flex items-center gap-2">
                <i class="fas fa-filter text-2xl"></i>
                Filtros
              </CardTitle>
            </CardHeader>
            <CardContent class="px-4 pb-4">
              <div class="flex items-end gap-4 flex-wrap">
                <!-- nivel -->
                <div class="space-y-1 w-36">
                  <Label class="text-xs">Nivel</Label>
                  <Select v-model="selectedLevel">
                    <SelectTrigger class="w-36 dark:bg-gray-800 border-gray-700  h-9">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent class="dark:bg-gray-800 border-gray-700">
                      <SelectItem value="all" class="dark:focus:bg-gray-700 focus:text-gray-100">Todos</SelectItem>
                        <SelectItem
                          v-for="level in ALLOWED_LEVELS"
                          :key="level"
                          :value="level"
                          class="dark:focus:bg-gray-700 focus:text-gray-100 uppercase"
                        >
                          {{level}}
                        </SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <!-- Channel -->
                <div class="space-y-1 w-24 ">
                  <Label class="text-xs ">Canal</Label>
                  <Select v-model="selectedChannel">
                    <SelectTrigger class="w-32 dark:bg-gray-800 border-gray-700  h-9">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent class="dark:bg-gray-800 border-gray-700">
                      <SelectItem value="all" class="dark:focus:bg-gray-700 focus:text-gray-100">Todos</SelectItem>
                        <SelectItem
                          v-for="channel in ALLOWED_CHANNELS"
                          :key="channel"
                          :value="channel" 
                          class="dark:focus:bg-gray-700 focus:text-gray-100"
                        >
                          {{ channel }}
                        </SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <!-- Date -->
                <div class="space-y-1">
                  <Label class="text-xs ">Fecha</Label>
                  <input
                    type="date"
                    v-model="filterDate"
                    class="w-[160px] dark:bg-gray-800 border-gray-700  h-9 rounded-md"
                  />
                </div>

                <!-- Has Exception -->
                <div class="space-y-1">
                  <Label class="text-xs ">Con excepción</Label>
                  <div class="flex items-center gap-2 h-9">
                    <Switch
                      :model-value="filterHasException === true"
                      @update:model-value="toggleException"
                      class="data-[state=checked]:bg-emerald-500"
                    />
                    <span class="text-xs">
                      {{filterHasException === null ? "Todos" : filterHasException ? "Sí" : "No"}}
                    </span>
                  </div>
                </div>

                <!-- Reset -->
                <Button
                  @click="resetFilters"
                  variant="outline" 
                  size="sm" 
                  class="h-9 border-gray-700 hover:bg-gray-300 dark:hover:bg-gray-800 hover:text-gray-100"
                >
                  <i class="fas fa-undo mr-1 text-xl"></i>
                  Limpiar
                </Button>
              </div>
            </CardContent>
          </Card>

          <!-- resultados -->
          <div class="flex items-center justify-between">
            <span class="text-sm ">
              Mostrando <span class="text-gray-400 font-extrabold">{{logs.length}}</span>
              registros del archivo
              <span class="text-gray-400 font-extrabold uppercase">{{ filterLog }}</span>
            </span>
          </div>

          <!-- log entries -->
           <div class="space-y-1">
            <Card
              v-if="logs.length === 0"
              class="dark:bg-gray-900 border border-gray-800"
            >
              <CardContent class="p-8 text-center">
                No se encontraron registros.
              </CardContent>
            </Card>
            <div v-else class="max-h-screen overflow-y-auto">
              <div
                  v-for="(log, index) in logs"
                  :key="index"
                  :class="[
                      'dark:bg-gray-900 border border-gray-800 rounded-md border-l-4',
                      levelRowColors[log.level] ?? 'border-l-gray-500'
                  ]"
              >
                  <div
                      class="flex items-center gap-3 px-3 py-2 cursor-pointer"
                      @click="toggleRow(index)"
                  >
                      <i
                          :class="
                              expandedRows.has(index)
                                  ? 'fas fa-chevron-down'
                                  : 'fas fa-chevron-right'
                          "
                      />

                      <span class="w-[145px] text-xs font-mono">
                          {{ log.date }}
                      </span>

                      <span
                          :class="[
                              'text-xs px-2 py-1 rounded border',
                              levelColors[log.level]
                          ]"
                      >
                          {{ log.level.toUpperCase() }}
                      </span>

                      <span
                          class="text-xs px-2 py-1 rounded border dark:bg-gray-800 border-gray-700"
                      >
                          {{ log.channel }}
                      </span>

                      <span class="truncate">
                          {{ log.message }}
                      </span>
                  </div>

                  <div
                      v-if="expandedRows.has(index)"
                      class="px-4 pb-3 pt-1 border-t border-gray-800"
                  >
                      <pre
                          class="text-xs font-mono whitespace-pre-wrap break-all dark:bg-gray-950 rounded p-3 max-h-[400px] overflow-auto"
                      >
                        {{ log.fullText }}
                      </pre>
                  </div>
              </div>
            </div>
           </div>
        </div>
      </div>
    </template>
  </App>
</template>