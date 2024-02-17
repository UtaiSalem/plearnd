<script setup>
    import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
    import { Icon } from '@iconify/vue';

    const emit = defineEmits([
        'edit-model',
        'delete-model',
        'delete-description',
    ]);

</script>
<template>
    <Menu as="div" class="relative inline-block text-left overflow-visible">
        <div>
            <MenuButton
                class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white/60 px-3 py-1 text-sm font-semibold text-gray-900  hover:bg-gray-50">
                <!-- Options -->
                <!-- <ChevronDownIcon class="-mr-1 h-5 w-5 text-gray-400" aria-hidden="true" /> -->
                <Icon icon="heroicons-solid:dots-horizontal" class=" h-5 w-5 text-gray-400" aria-hidden="true" />
            </MenuButton>
        </div>

        <transition enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95">
            <MenuItems
                class="absolute top-6 right-0 z-10 mt-1 w-40 origin-top-right rounded-md bg-white shadow-lg ring-2 ring-black ring-opacity-5 focus:outline-none">
                <div class="p-1">
                    <MenuItem v-slot="{ active }" class="" v-if="$slots.editModel">
                        <button @click.prevent="emit('edit-model')" class="flex justify-start items-center space-x-2 w-full"
                            :class="[ active ? 'bg-gray-100 text-gray-900' : 'text-gray-700','block p-2 text-sm', ]">
                            <Icon icon="tabler:edit" class="text-red-500 w-5 h-5 " /> 
                            <slot name="editModel"></slot>
                        </button>
                    </MenuItem>
                    <MenuItem v-slot="{ active }" v-if="$slots.deleteModel">
                        <button @click.prevent="emit('delete-model')" class="flex justify-start items-center space-x-2 w-full"
                            :class="[ active ? 'bg-gray-100 text-gray-900' : 'text-gray-700','block p-2 text-sm', ]">
                            <Icon icon="heroicons-solid:trash" class="text-red-500 w-5 h-5 " /> 
                            <slot name="deleteModel"></slot>
                        </button>
                    </MenuItem>
                    <MenuItem v-slot="{ active }" v-if="$slots.deleteDescription">
                        <button @click.prevent="emit('delete-description')" class="flex justify-start items-center space-x-2 w-full"
                            :class="[ active ? 'bg-gray-100 text-gray-900' : 'text-gray-700','block p-2 text-sm', ]">
                            <Icon icon="heroicons-solid:trash" class="text-red-500 w-5 h-5 " /> 
                            <slot name="delete-description"></slot>
                        </button>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>
