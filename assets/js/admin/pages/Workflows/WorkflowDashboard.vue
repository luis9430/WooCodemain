<template>
    <div class="p-card">
        <DataTable :value="orders" responsiveLayout="scroll">
            <Column field="id" header="ID Pedido"></Column>
            <Column field="customer" header="Cliente"></Column>
            <Column header="Estado Workflow">
                <template #body="slotProps">
                    <Tag :value="slotProps.data.workflow_status.current_place" 
                         :severity="getStatusSeverity(slotProps.data.workflow_status.current_place)" />
                </template>
            </Column>
            <Column header="Fecha"></Column>
            <Column header="Total"></Column>
            <Column header="Acciones">
                <template #body="slotProps">
                    <OrderWorkflowActions 
                        :orderId="slotProps.data.id" 
                        :currentPlace="slotProps.data.workflow_status.current_place"
                        :availableTransitions="slotProps.data.workflow_status.available_transitions"
                        @transition-applied="onTransitionApplied" 
                    />
                </template>
            </Column>
        </DataTable>
    </div>

    <Dialog header="Historial del Pedido" v-model:visible="displayHistoryDialog" :modal="true">
        <OrderWorkflowHistory v-if="selectedOrderForHistory" :orderId="selectedOrderForHistory" />
    </Dialog>

    <Toast />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import Toast from 'primevue/toast';



</script>