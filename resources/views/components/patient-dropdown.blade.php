<div class="mb-4 relative" x-data="{
        search: '',
        selectedPatientId: '',
        patients: {{ json_encode($patients) }},
        showDropdown: false,
        get filteredPatients() {
            return this.patients.filter(p => 
                (`${p.first_name} ${p.last_name} (SSN: ${p.ssn})`).toLowerCase()
                    .includes(this.search.toLowerCase())
            );
        }
    }">
    <label for="patient_search" class="block mb-2 font-semibold text-gray-800 dark:text-gray-200">
        Select Patient:
    </label>
    <input type="text" id="patient_search" x-model="search" @input="showDropdown = search.length > 0"
           class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white" autocomplete="off" />
    <input type="hidden" name="patient_id" x-model="selectedPatientId">

    @error('patient_id')
        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
    @enderror

    <div x-show="showDropdown" class="absolute z-10 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded mt-1 overflow-y-auto" style="width: 100%; max-height: 200px">
        <template x-if="filteredPatients.length === 0">
            <div class="p-2 text-gray-500 dark:text-gray-300">No patients found</div>
        </template>
        <template x-for="patient in filteredPatients" :key="patient.patient_id">
            <div @click="search = `${patient.first_name} ${patient.last_name} (SSN: ${patient.ssn})`; selectedPatientId = patient.patient_id; showDropdown = false"
                 class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 cursor-pointer">
                <span x-text="`${patient.first_name} ${patient.last_name} (SSN: ${patient.ssn})`"></span>
            </div>
        </template>
    </div>
</div>